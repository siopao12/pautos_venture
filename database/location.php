<?php
ob_start();
session_start();
header('Content-Type: application/json');

// Better error handling
set_error_handler(function($severity, $message, $file, $line) {
    error_log("Error in location.php: $message in $file on line $line");
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    // Ensure user is authenticated
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }
    $user_id = $_SESSION['user_id'];
    
    // Grab and parse the JSON input
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    
    // Debug log
    error_log("Received data: " . $raw_input);
    
    // Basic validation
    if (!$data || !isset($data['action']) || $data['action'] !== 'save_location') {
        throw new Exception('Invalid request data');
    }
    
    if (!isset($data['lat']) || !isset($data['lng'])) {
        throw new Exception('Missing latitude or longitude');
    }
    
    // Database connection
    require_once '../config/db_config.php';
    
    // Extract location data
    $type = $data['type'] ?? 'manual';
    $lat = floatval($data['lat']);
    $lng = floatval($data['lng']);
    $formatted_address = $data['address'] ?? 'Unknown address';
    $current_time = date('Y-m-d H:i:s');
    
    // Parse the formatted address to extract components
    $address_components = parseAddressFromText($formatted_address);
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Create address record first
        $address_stmt = $pdo->prepare("INSERT INTO user_address (
            street_number, street_name, barangay, district, city_municipality, 
            province, region, postal_code, landmark, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $address_stmt->execute([
            $address_components['street_number'],
            $address_components['street_name'],
            $address_components['barangay'],
            $address_components['district'],
            $address_components['city_municipality'],
            $address_components['province'],
            $address_components['region'],
            $address_components['postal_code'],
            $address_components['landmark'],
            $current_time,
            $current_time
        ]);
        
        $address_id = $pdo->lastInsertId();
        
        // First, delete any existing location for this user
        $delete_stmt = $pdo->prepare("DELETE FROM user_locations WHERE user_id = ?");
        $delete_stmt->execute([$user_id]);
        
        // Then insert the new location
        $location_stmt = $pdo->prepare("INSERT INTO user_locations 
            (user_id, location_type, latitude, longitude, address_id, timestamp, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
        
        $location_stmt->execute([
            $user_id, 
            $type, 
            $lat, 
            $lng, 
            $address_id, 
            $current_time, 
            $current_time
        ]);
        
        // Commit transaction
        $pdo->commit();
        
        // Update session with location info
        $_SESSION['user_location'] = [
            'type' => $type,
            'lat' => $lat,
            'lng' => $lng,
            'address' => $formatted_address,
            'city_municipality' => $address_components['city_municipality'],
            'province' => $address_components['province'],
            'barangay' => $address_components['barangay']
        ];
        
        // Return success
        ob_clean();
        echo json_encode([
            'success' => true, 
            'message' => 'Location saved successfully',
            'address' => $formatted_address
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Location handler error: " . $e->getMessage());
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Function to parse the client-provided formatted address into components
 */
function parseAddressFromText($formatted_address) {
    // Initialize address components
    $address = [
        'street_number' => '',
        'street_name' => '',
        'barangay' => '',
        'district' => '',
        'city_municipality' => '',
        'province' => '',
        'region' => '',
        'postal_code' => '',
        'landmark' => '',
        'formatted_address' => $formatted_address
    ];
    
    // Common patterns for address components in Philippines
    if (preg_match('/(\d+[A-Za-z]?)\s+([^,]+)/', $formatted_address, $matches)) {
        $address['street_number'] = $matches[1];
        $address['street_name'] = $matches[2];
    }
    
    // Look for Barangay
    if (preg_match('/Barangay\s+([^,]+)/i', $formatted_address, $matches)) {
        $address['barangay'] = $matches[1];
    } elseif (preg_match('/Brgy\.\s+([^,]+)/i', $formatted_address, $matches)) {
        $address['barangay'] = $matches[1];
    }
    
    // Look for postal code
    if (preg_match('/(\d{4})/', $formatted_address, $matches)) {
        $address['postal_code'] = $matches[1];
    }
    
    // Common cities in the Philippines
    $common_cities = ['Davao City', 'Manila', 'Cebu', 'Quezon City', 'Makati'];
    foreach ($common_cities as $city) {
        if (stripos($formatted_address, $city) !== false) {
            $address['city_municipality'] = $city;
            break;
        }
    }
    
    // Common provinces
    $common_provinces = ['Davao del Sur', 'Metro Manila', 'Cebu', 'Rizal', 'Cavite', 'Laguna'];
    foreach ($common_provinces as $province) {
        if (stripos($formatted_address, $province) !== false) {
            $address['province'] = $province;
            break;
        }
    }
    
    // Default country
    if (stripos($formatted_address, 'Philippines') !== false) {
        $address['region'] = 'Philippines';
    }
    
    return $address;
}

restore_error_handler();
?>