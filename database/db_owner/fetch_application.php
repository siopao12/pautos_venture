<?php
// Database connection
require_once __DIR__ . '/../../config/db_config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an owner
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Set headers for JSON response
header('Content-Type: application/json');

try {
    // Check if this is an application status update request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get request body
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Validate required fields
        if (!isset($data['runner_id']) || !isset($data['status'])) {
            throw new Exception("Runner ID and status are required");
        }
        
        $runnerId = $data['runner_id'];
        $status = $data['status'];
        $notes = $data['notes'] ?? '';
        
        // Validate status value
        if (!in_array($status, ['approved', 'rejected'])) {
            throw new Exception("Invalid status value");
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update runner status
            $stmt = $pdo->prepare("
            UPDATE runners 
            SET application_status = ?,
                status_notes = CASE WHEN ? = 'approved' THEN 'Verified' ELSE ? END,
                updated_at = NOW()
            WHERE runner_id = ?
            ");
            $stmt->execute([$status, $status, $notes, $runnerId]);
        
        // If approved, update user role to 'runner' in users table
        if ($status === 'approved') {
            // First get the user_id for this runner
            $userStmt = $pdo->prepare("
                SELECT user_id 
                FROM runners 
                WHERE runner_id = ?
            ");
            $userStmt->execute([$runnerId]);
            $result = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $userId = $result['user_id'];
                
                // Set role_id = 2 (runner)
                $roleStmt = $pdo->prepare("
                    UPDATE users 
                    SET role_id = 2 
                    WHERE user_id = ?
                ");
                $roleStmt->execute([$userId]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Success response
        $response = [
            'success' => true,
            'message' => 'Runner application ' . $status . ' successfully'
        ];
    } 
    // If GET request, fetch all pending runner applications
    else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get runners with different statuses
        $stmt = $pdo->query("
            SELECT 
                r.runner_id,
                r.user_id,
                r.id_photo,
                r.selfie_photo,
                r.transportation_method,
                r.application_status as status,
                r.created_at,
                u.first_name,
                u.last_name,
                u.email as user_email,
                u.phone as user_phone,
                up.profile_picture as profile_photo,
                CONCAT(u.first_name, ' ', u.last_name) as user_name,
                rv.vehicle_type,
                rv.registration_number,
                rv.license_number,
                rw.service_radius,
                rt.transit_type,
                rt.transit_radius
            FROM runners r
            JOIN users u ON r.user_id = u.user_id
            LEFT JOIN user_profiles up ON u.user_id = up.user_id
            LEFT JOIN runner_vehicles rv ON r.runner_id = rv.runner_id AND r.transportation_method = 'vehicle'
            LEFT JOIN runner_walking rw ON r.runner_id = rw.runner_id AND r.transportation_method = 'walking'
            LEFT JOIN runner_transit rt ON r.runner_id = rt.runner_id AND r.transportation_method = 'commute'
            ORDER BY r.created_at DESC
        ");
        
        $runners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get counts for dashboard
        $countsStmt = $pdo->query("
            SELECT 
                application_status, 
                COUNT(*) as count 
            FROM runners 
            GROUP BY application_status
        ");
        
        $countsResult = $countsStmt->fetchAll(PDO::FETCH_ASSOC);
        $counts = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];
        
        foreach ($countsResult as $row) {
            $counts[$row['application_status']] = (int)$row['count'];
        }
        
        // Get services selected by each runner
        foreach ($runners as $key => $runner) {
            $serviceStmt = $pdo->prepare("
                SELECT 
                    sc.category_name,
                    sc.category_code,
                    ss.subcategory_name
                FROM runner_services rs
                JOIN service_subcategories ss ON rs.subcategory_id = ss.subcategory_id
                JOIN service_categories sc ON ss.category_id = sc.category_id
                WHERE rs.runner_id = ?
            ");
            $serviceStmt->execute([$runner['runner_id']]);
            $runners[$key]['services'] = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $response = [
            'success' => true,
            'data' => [
                'runners' => $runners,
                'counts' => $counts
            ]
        ];
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Error response
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    // Log detailed error information
    error_log("Runner application error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}

// Send JSON response
echo json_encode($response);
?>