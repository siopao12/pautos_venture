<?php
// Database connection
require_once __DIR__ . '/../../config/db_config.php';

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

$user_id = $_SESSION['user_id'];

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

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
    // Create DB connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check user role
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

    // Update availability
    $runner_query = "UPDATE runners SET is_available = :is_available WHERE user_id = :user_id";
    $runner_stmt = $conn->prepare($runner_query);
    $runner_stmt->bindParam(':is_available', $is_available);
    $runner_stmt->bindParam(':user_id', $user_id);
    $runner_stmt->execute();

    // Get runner_id
    $runner_id_stmt = $conn->prepare("SELECT runner_id FROM runners WHERE user_id = :user_id");
    $runner_id_stmt->bindParam(':user_id', $user_id);
    $runner_id_stmt->execute();
    $runner_data = $runner_id_stmt->fetch(PDO::FETCH_ASSOC);

    if ($runner_data) {
        $runner_id = $runner_data['runner_id'];
        $status_change = $is_available ? 'online' : 'offline';

        // Check last status
        $last_status_stmt = $conn->prepare("SELECT status_change FROM runner_status_logs 
                                            WHERE runner_id = :runner_id 
                                            ORDER BY timestamp DESC LIMIT 1");
        $last_status_stmt->bindParam(':runner_id', $runner_id);
        $last_status_stmt->execute();
        $last_status = $last_status_stmt->fetch(PDO::FETCH_ASSOC);

        // Only log if status is different
        if (!$last_status || $last_status['status_change'] !== $status_change) {
            $log_stmt = $conn->prepare("INSERT INTO runner_status_logs (
                                            runner_id, 
                                            status_change, 
                                            timestamp
                                        ) VALUES (
                                            :runner_id, 
                                            :status_change, 
                                            NOW()
                                        )");
            $log_stmt->bindParam(':runner_id', $runner_id);
            $log_stmt->bindParam(':status_change', $status_change);
            $log_stmt->execute();
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Runner status updated successfully',
        'is_available' => (bool)$is_available
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
