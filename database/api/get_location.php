<?php
// Database connection
require_once __DIR__ . '/../../config/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get the most recent location for the runner
    $stmt = $pdo->prepare("SELECT rl.latitude, rl.longitude, rl.timestamp, 
                           ua.street_number, ua.street_name, ua.barangay, ua.city_municipality, 
                           ua.province, ua.postal_code, ua.country, ua.formatted_address,
                           r.is_available
                           FROM runner_locations rl
                           LEFT JOIN user_address ua ON rl.address_id = ua.address_id
                           LEFT JOIN runners r ON rl.user_id = r.user_id
                           WHERE rl.user_id = ?
                           ORDER BY rl.timestamp DESC
                           LIMIT 1");
    
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() > 0) {
        $location = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Use formatted_address if available, otherwise build from components
        $address = $location['formatted_address'];
        if (empty($address)) {
            $address_parts = [];
            if (!empty($location['street_number'])) $address_parts[] = $location['street_number'];
            if (!empty($location['street_name'])) $address_parts[] = $location['street_name'];
            if (!empty($location['barangay'])) $address_parts[] = $location['barangay'];
            if (!empty($location['city_municipality'])) $address_parts[] = $location['city_municipality'];
            if (!empty($location['province'])) $address_parts[] = $location['province'];
            
            $address = implode(', ', $address_parts);
        }
        
        echo json_encode([
            'success' => true,
            'location' => [
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'timestamp' => $location['timestamp'],
                'address' => $address,
                'is_available' => (bool)$location['is_available'],
                'address_parts' => [
                    'street_number' => $location['street_number'],
                    'street_name' => $location['street_name'],
                    'barangay' => $location['barangay'],
                    'city' => $location['city_municipality'],
                    'province' => $location['province'],
                    'postal_code' => $location['postal_code'],
                    'country' => $location['country'],
                    'formatted_address' => $location['formatted_address']
                ]
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'location' => null
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}