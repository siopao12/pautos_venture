<?php
require_once '../config/db_config.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if a file was uploaded successfully
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or there was an error uploading the file.']);
    exit;
}

// Define correct path - use ../ to go up one directory level from current script location
$upload_dir = '../assests/image/uploads/profile_pictures/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true); // create directory if it doesn't exist
}

// Create a unique filename
$filename = uniqid('profile_', true) . '.' . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
$target_file = $upload_dir . $filename;

// Move the uploaded file
if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
    // Add error details for debugging
    $error = error_get_last();
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to save the uploaded file.',
        'error' => $error ? $error['message'] : 'Unknown error',
        'source' => $_FILES['profile_picture']['tmp_name'],
        'target' => $target_file
    ]);
    exit;
}

// Store path relative to web root for HTML <img> (without the leading ../)
$public_path = 'assests/image/uploads/profile_pictures/' . $filename;

// Insert or update in the database
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare("UPDATE user_profiles SET profile_picture = ?, updated_at = NOW() WHERE user_id = ?");
    if (!$stmt->execute([$public_path, $user_id])) {
        echo json_encode([
            'success' => false,
            'message' => 'Database update failed: ' . implode(', ', $stmt->errorInfo())
        ]);
        exit;
    }
} else {
    $stmt = $pdo->prepare("INSERT INTO user_profiles (user_id, profile_picture, created_at) VALUES (?, ?, NOW())");
    if (!$stmt->execute([$user_id, $public_path])) {
        echo json_encode([
            'success' => false,
            'message' => 'Database insert failed: ' . implode(', ', $stmt->errorInfo())
        ]);
        exit;
    }
}

// Update session
$_SESSION['profile_pic'] = $public_path;

echo json_encode([
    'success' => true,
    'message' => 'Profile picture updated successfully!',
    'newImage' => $public_path,
    'sessionValue' => $_SESSION['profile_pic']
]);
?>