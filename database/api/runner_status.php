<?php
// Database connection
require_once __DIR__ . '/../../config/db_config.php'; // Adjust path as needed

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// Check if user is a runner
$user_id = $_SESSION['user_id'];

// Check if this is a POST request with status data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON data from request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || !isset($data['is_available'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status data'
    ]);
    exit;
}

$is_available = $data['is_available'] ? 1 : 0;

try {
    // Create PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if user is a runner
    $role_query = "SELECT role_id FROM users WHERE user_id = :user_id";
    $role_stmt = $conn->prepare($role_query);
    $role_stmt->bindParam(':user_id', $user_id);
    $role_stmt->execute();
    $role_data = $role_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$role_data || $role_data['role_id'] != 2) {
        echo json_encode([
            'success' => false,
            'message' => 'User is not a verified runner'
        ]);
        exit;
    }
    
    // Update runner availability status
    $runner_query = "UPDATE runners 
                     SET is_available = :is_available 
                     WHERE user_id = :user_id";
    
    $runner_stmt = $conn->prepare($runner_query);
    $runner_stmt->bindParam(':is_available', $is_available);
    $runner_stmt->bindParam(':user_id', $user_id);
    $runner_stmt->execute();
    
    // Log the status change
    $log_query = "INSERT INTO runner_status_logs (
                     runner_id, 
                     status_change, 
                     timestamp
                  ) VALUES (
                     (SELECT runner_id FROM runners WHERE user_id = :user_id),
                     :status_change,
                     NOW()
                  )";
    
    $status_change = $is_available ? 'online' : 'offline';
    $log_stmt = $conn->prepare($log_query);
    $log_stmt->bindParam(':user_id', $user_id);
    $log_stmt->bindParam(':status_change', $status_change);
    $log_stmt->execute();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Runner status updated successfully',
        'is_available' => (bool)$is_available
    ]);
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>