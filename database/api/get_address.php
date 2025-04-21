<?php
// Database connection
require_once __DIR__ . '/../../config/db_config.php';

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

$user_id = $_SESSION['user_id'];

try {
    // Query the database for the user's last known address
    $query = "SELECT r.last_latitude, r.last_longitude, r.last_location_update, r.last_location_address,
              ua.street_number, ua.street_name, ua.barangay, ua.city_municipality as city, 
              ua.province, ua.postal_code, ua.country, ua.formatted_address
              FROM runners r
              LEFT JOIN user_address ua ON r.last_address_id = ua.address_id
              WHERE r.user_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Return the address data from the database
        echo json_encode([
            'success' => true,
            'location' => [
                'latitude' => $result['last_latitude'],
                'longitude' => $result['last_longitude'],
                'timestamp' => $result['last_location_update'],
                'address' => $result['last_location_address'] ?? $result['formatted_address'] ?? null,
                'address_parts' => [
                    'street_number' => $result['street_number'] ?? '',
                    'street_name' => $result['street_name'] ?? '',
                    'barangay' => $result['barangay'] ?? '',
                    'city' => $result['city'] ?? '',
                    'province' => $result['province'] ?? '',
                    'postal_code' => $result['postal_code'] ?? '',
                    'country' => $result['country'] ?? '',
                    'formatted_address' => $result['formatted_address'] ?? $result['last_location_address'] ?? ''
                ]
            ]
        ]);
    } else {
        // No location data found
        echo json_encode([
            'success' => false,
            'message' => 'No location data found for this user'
        ]);
    }
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}