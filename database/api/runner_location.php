<?php
require_once __DIR__ . '/../../config/db_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// More detailed session validation
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    error_log("Auth error: No user_id in session");
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'error_code' => 'AUTH_ERROR']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    error_log("Invalid JSON data received");
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
if (!isset($data['latitude']) || !isset($data['longitude']) || !isset($data['timestamp'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_id = $_SESSION['user_id'];
$latitude = floatval($data['latitude']);
$longitude = floatval($data['longitude']);
$timestamp = $data['timestamp'];
$is_available = isset($data['is_available']) ? intval($data['is_available']) : 0;

// Process the address data - handle both structured and string formats
$street_number = null;
$street_name = null;
$barangay = null;
$city = null;
$province = null;
$postal_code = null;
$temp_formatted_address = null; // This will hold the received formatted address but won't be stored in DB

// Handle structured address format
if (isset($data['address']) && is_array($data['address'])) {
    $a = $data['address'];
    $street_number = isset($a['street_number']) ? $a['street_number'] : null;
    $street_name = isset($a['street_name']) ? $a['street_name'] : null;
    $barangay = isset($a['barangay']) ? $a['barangay'] : null;
    $city = isset($a['city']) ? $a['city'] : null;
    $province = isset($a['province']) ? $a['province'] : null;
    $postal_code = isset($a['postal_code']) ? $a['postal_code'] : null;
    $temp_formatted_address = isset($a['formatted_address']) ? $a['formatted_address'] : null;
}
// Handle string address format (fallback)
elseif (isset($data['address_string']) && is_string($data['address_string'])) {
    $temp_formatted_address = $data['address_string'];
}

try {
    // Check database connection first
    try {
        $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed");
    }
    
    $pdo->beginTransaction();
    
    // First, insert or update the address in user_address table if address info is available
    $address_id = null;
    
    // UPDATED LOGIC: Check if the user already has an address record
    $checkUserAddressQuery = "SELECT ul.address_id 
                             FROM user_locations ul
                             WHERE ul.user_id = ? AND ul.address_id IS NOT NULL
                             ORDER BY ul.timestamp DESC LIMIT 1";
    $checkUserStmt = $pdo->prepare($checkUserAddressQuery);
    $checkUserStmt->execute([$user_id]);
    $existingUserAddress = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUserAddress && $existingUserAddress['address_id']) {
        // User has an existing address, update it instead of creating a new one
        $address_id = $existingUserAddress['address_id'];
        
        // Update the existing address with new information
        $updateAddressQuery = "UPDATE user_address SET 
                              street_number = ?, 
                              street_name = ?, 
                              barangay = ?, 
                              city_municipality = ?, 
                              province = ?, 
                              postal_code = ?, 
                              updated_at = NOW()
                              WHERE address_id = ?";
                              
        $updateAddressStmt = $pdo->prepare($updateAddressQuery);
        $updateAddressStmt->execute([
            $street_number,
            $street_name,
            $barangay,
            $city,
            $province,
            $postal_code,
            $address_id
        ]);
    } 
    // Only create a new address if the user doesn't have one yet and we have address info
    elseif ($street_name && $city) {
        try {
            // Check if user_address table has an updated_at column
            $hasUpdatedAtColumn = false;
            try {
                $columnCheckStmt = $pdo->query("SHOW COLUMNS FROM user_address LIKE 'updated_at'");
                $hasUpdatedAtColumn = $columnCheckStmt->rowCount() > 0;
            } catch (PDOException $e) {
                // Ignore this error, assume column doesn't exist
            }

            // Insert new address with or without updated_at
            if ($hasUpdatedAtColumn) {
                $insertAddressQuery = "INSERT INTO user_address 
                        (street_number, street_name, barangay, city_municipality, 
                        province, postal_code, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
            } else {
                $insertAddressQuery = "INSERT INTO user_address 
                        (street_number, street_name, barangay, city_municipality, 
                        province, postal_code, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())";
            }

            $insertAddressStmt = $pdo->prepare($insertAddressQuery);
            $insertAddressStmt->execute([
                $street_number,
                $street_name,
                $barangay,
                $city,
                $province,
                $postal_code
            ]);

            $address_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Address update error: " . $e->getMessage());
            throw $e; // Re-throw to be caught by the outer try-catch
        }
    }
    
    // Check if a current location already exists
    try {
        $checkQuery = "SELECT location_id FROM user_locations WHERE user_id = ? AND location_type = 'current'";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$user_id]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update the existing row
            $updateQuery = "UPDATE user_locations SET 
                            latitude = ?, 
                            longitude = ?, 
                            address_id = ?, 
                            timestamp = ?, 
                            updated_at = NOW() 
                            WHERE location_id = ?";

            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([
                $latitude,
                $longitude,
                $address_id,
                $timestamp,
                $existing['location_id']
            ]);
        } else {
            // Check if user_locations table has an updated_at column
            $hasUpdatedAtColumn = false;
            try {
                $columnCheckStmt = $pdo->query("SHOW COLUMNS FROM user_locations LIKE 'updated_at'");
                $hasUpdatedAtColumn = $columnCheckStmt->rowCount() > 0;
            } catch (PDOException $e) {
                // Ignore this error, assume column doesn't exist
            }
            
            // Insert a new row with or without updated_at
            if ($hasUpdatedAtColumn) {
                $insertQuery = "INSERT INTO user_locations 
                           (user_id, location_type, latitude, longitude, address_id, timestamp, created_at, updated_at) 
                           VALUES (?, 'current', ?, ?, ?, ?, NOW(), NOW())";
            } else {
                $insertQuery = "INSERT INTO user_locations 
                           (user_id, location_type, latitude, longitude, address_id, timestamp, created_at) 
                           VALUES (?, 'current', ?, ?, ?, ?, NOW())";
            }
            
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([
                $user_id,
                $latitude,
                $longitude,
                $address_id,
                $timestamp
            ]);
        }
    } catch (PDOException $e) {
        error_log("Location update error: " . $e->getMessage());
        throw $e; // Re-throw to be caught by the outer try-catch
    }

    $pdo->commit();
    
    // Construct formatted address from parts for the response
    $constructed_formatted_address = '';
    if ($street_number) $constructed_formatted_address .= $street_number . ' ';
    if ($street_name) $constructed_formatted_address .= $street_name . ', ';
    if ($barangay) $constructed_formatted_address .= $barangay . ', ';
    if ($city) $constructed_formatted_address .= $city . ', ';
    if ($province) $constructed_formatted_address .= $province . ' ';
    if ($postal_code) $constructed_formatted_address .= $postal_code;
    
    // Use the original formatted address from the request if available, otherwise use constructed one
    $final_formatted_address = $temp_formatted_address ?: trim($constructed_formatted_address);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Location updated successfully',
        'data' => [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timestamp' => $timestamp,
            'is_available' => $is_available
        ],
        'address' => $final_formatted_address,
        'address_parts' => [
            'street_number' => $street_number,
            'street_name' => $street_name,
            'barangay' => $barangay,
            'city' => $city,
            'province' => $province,
            'postal_code' => $postal_code,
        ]
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Log the full error details
    error_log("Database Error in runner_location.php: " . $e->getMessage() . " - SQL State: " . $e->getCode());
    
    // Return a more specific error message
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'error_code' => 'DB_ERROR',
        'sql_state' => $e->getCode()
    ]);
    exit;
} catch (Exception $e) {
    // General exception handling
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("General Error in runner_location.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'error_code' => 'GENERAL_ERROR'
    ]);
    exit;
}
?>