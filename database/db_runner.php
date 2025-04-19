<?php
// Database connection
require_once '../config/db_config.php'; // You'll need to create this file with your DB credentials

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'You must be logged in to apply as a runner.']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get user ID from session
        $userId = $_SESSION['user_id'];
        
        // File upload handling - Updated path to match your folder structure
        $uploadDir = '../assests/image/upload/runner_docs/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Process ID photo
        $idPhotoPath = '';
        if (isset($_FILES['id_photo']) && $_FILES['id_photo']['error'] === UPLOAD_ERR_OK) {
            $idPhotoName = 'id_' . time() . '_' . basename($_FILES['id_photo']['name']);
            $idPhotoPath = $uploadDir . $idPhotoName;
            
            if (!move_uploaded_file($_FILES['id_photo']['tmp_name'], $idPhotoPath)) {
                throw new Exception("Failed to upload ID photo");
            }
        } else {
            throw new Exception("ID photo is required");
        }
        
        // Process selfie photo
        $selfiePhotoPath = '';
        if (isset($_FILES['selfie_photo']) && $_FILES['selfie_photo']['error'] === UPLOAD_ERR_OK) {
            $selfiePhotoName = 'selfie_' . time() . '_' . basename($_FILES['selfie_photo']['name']);
            $selfiePhotoPath = $uploadDir . $selfiePhotoName;
            
            if (!move_uploaded_file($_FILES['selfie_photo']['tmp_name'], $selfiePhotoPath)) {
                throw new Exception("Failed to upload selfie photo");
            }
        } else {
            throw new Exception("Selfie photo is required");
        }
        
        // Get transportation method
        $transportationMethod = $_POST['transportation_method'] ?? '';
        if (empty($transportationMethod)) {
            throw new Exception("Transportation method is required");
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert into runners table
        $stmt = $pdo->prepare("
            INSERT INTO runners (user_id, id_photo, selfie_photo, transportation_method)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $idPhotoPath, $selfiePhotoPath, $transportationMethod]);
        
        $runnerId = $pdo->lastInsertId();
        
        // Process transportation details based on method
        if ($transportationMethod === 'vehicle') {
            // Process vehicle photo - Make this optional instead of failing
            $vehiclePhotoPath = '';
            if (isset($_FILES['vehicle_photo']) && $_FILES['vehicle_photo']['error'] === UPLOAD_ERR_OK) {
                $vehiclePhotoName = 'vehicle_' . time() . '_' . basename($_FILES['vehicle_photo']['name']);
                $vehiclePhotoPath = $uploadDir . $vehiclePhotoName;
                
                if (!move_uploaded_file($_FILES['vehicle_photo']['tmp_name'], $vehiclePhotoPath)) {
                    // Log issue but continue
                    error_log("Warning: Could not upload vehicle photo");
                }
            }
            
            // Get vehicle details with defaults to prevent errors
            $vehicleType = $_POST['vehicle_type'] ?? '';
            $registrationNumber = $_POST['registration_number'] ?? '';
            $licenseNumber = $_POST['license_number'] ?? '';
            $vehiclePhone = $_POST['vehicle_phone'] ?? '';
            
            // Insert vehicle details
            $stmt = $pdo->prepare("
                INSERT INTO runner_vehicles (
                    runner_id, vehicle_type, registration_number, 
                    license_number, vehicle_phone, vehicle_photo
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $runnerId,
                $vehicleType,
                $registrationNumber,
                $licenseNumber,
                $vehiclePhone,
                $vehiclePhotoPath
            ]);
        } elseif ($transportationMethod === 'walking') {
            // Insert walking details
            $serviceRadius = !empty($_POST['service_radius']) ? $_POST['service_radius'] : 0;
            
            $stmt = $pdo->prepare("
                INSERT INTO runner_walking (runner_id, service_radius)
                VALUES (?, ?)
            ");
            $stmt->execute([
                $runnerId,
                $serviceRadius
            ]);
        } elseif ($transportationMethod === 'commute') {
            // Get transit details with defaults
            $transitType = $_POST['transit_type'] ?? '';
            $transitRadius = !empty($_POST['transit_radius']) ? $_POST['transit_radius'] : 0;
            
            // Insert transit details
            $stmt = $pdo->prepare("
                INSERT INTO runner_transit (runner_id, transit_type, transit_radius)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $runnerId,
                $transitType,
                $transitRadius
            ]);
        }
        
        // Process service categories
        if (isset($_POST['selected_subcategories'])) {
            $selectedSubcategories = json_decode($_POST['selected_subcategories'], true);
            
            if (!empty($selectedSubcategories)) {
                foreach ($selectedSubcategories as $item) {
                    list($category, $subcategory) = explode(':', $item);
                    
                    // Get category ID
                    $stmtCategory = $pdo->prepare("
                        SELECT category_id FROM service_categories 
                        WHERE category_code = ?
                    ");
                    $stmtCategory->execute([$category]);
                    $categoryResult = $stmtCategory->fetch();
                    
                    if ($categoryResult) {
                        $categoryId = $categoryResult['category_id'];
                        
                        // Get subcategory ID
                        $stmtSubcategory = $pdo->prepare("
                            SELECT subcategory_id FROM service_subcategories 
                            WHERE category_id = ? AND subcategory_name = ?
                        ");
                        $stmtSubcategory->execute([$categoryId, $subcategory]);
                        $subcategoryResult = $stmtSubcategory->fetch();
                        
                        if ($subcategoryResult) {
                            $subcategoryId = $subcategoryResult['subcategory_id'];
                            
                            // Insert runner service
                            $stmtService = $pdo->prepare("
                                INSERT INTO runner_services (runner_id, subcategory_id)
                                VALUES (?, ?)
                            ");
                            $stmtService->execute([$runnerId, $subcategoryId]);
                        }
                    }
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Success response
        $response['success'] = true;
        $response['message'] = 'Runner application submitted successfully';
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Error response
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Clean up uploaded files on error
    if (isset($idPhotoPath) && file_exists($idPhotoPath)) {
        unlink($idPhotoPath);
    }
    if (isset($selfiePhotoPath) && file_exists($selfiePhotoPath)) {
        unlink($selfiePhotoPath);
    }
    if (isset($vehiclePhotoPath) && file_exists($vehiclePhotoPath)) {
        unlink($vehiclePhotoPath);
    }
    
    // Log detailed error information
    error_log("Runner application error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Send JSON response
echo json_encode($response);
?>