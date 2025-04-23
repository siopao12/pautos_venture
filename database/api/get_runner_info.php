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

$user_id = $_SESSION['user_id'];

try {
    // Create PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user basic information
    $user_query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.role_id, r.name as role_name, 
                           CASE WHEN u.role_id = 2 THEN 'Verified Runner' ELSE 'Pending Runner' END as runner_status
                    FROM users u
                    JOIN roles r ON u.role_id = r.role_id
                    WHERE u.user_id = :user_id";
    
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    $response = [
        'success' => true,
        'user_id' => $user_data['user_id'],
        'name' => $user_data['first_name'] . ' ' . $user_data['last_name'],
        'email' => $user_data['email'],
        'role_id' => $user_data['role_id'],
        'role_name' => $user_data['role_name'],
        'runner_status' => $user_data['runner_status']
    ];
    
    // Base URL with /pautos-venture prefix
    $base_url = "";
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $base_url = "https://";
    } else {
        $base_url = "http://";
    }
    $base_url .= $_SERVER['HTTP_HOST'] . '/pautos-venture';
    
    // Get profile picture if available
    $profile_query = "SELECT profile_picture FROM user_profiles WHERE user_id = :user_id";
    $profile_stmt = $conn->prepare($profile_query);
    $profile_stmt->bindParam(':user_id', $user_id);
    $profile_stmt->execute();
    $profile_data = $profile_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profile_data && !empty($profile_data['profile_picture'])) {
        // If the profile picture is already a full URL
        if (strpos($profile_data['profile_picture'], 'http') === 0) {
            $response['profile_pic'] = $profile_data['profile_picture'];
        } 
        // If it's a filename, build the correct path
        else {
            $filename = basename($profile_data['profile_picture']);
            $response['profile_pic'] = $base_url . '/assests/image/uploads/profile_pictures/' . $filename;
        }
    } else {
        // Default profile picture with correct path
        $response['profile_pic'] = $base_url . '/assests/image/uploads/profile_pictures/default-profile.jpg';
    }
    
    // Get latest location information
    $location_query = "SELECT ul.latitude, ul.longitude, ul.timestamp, ua.street_number, ua.street_name, 
                              ua.barangay, ua.city_municipality, ua.province, ua.postal_code
                       FROM user_locations ul
                       LEFT JOIN user_address ua ON ul.address_id = ua.address_id
                       WHERE ul.user_id = :user_id
                       ORDER BY ul.timestamp DESC LIMIT 1";
    
    $location_stmt = $conn->prepare($location_query);
    $location_stmt->bindParam(':user_id', $user_id);
    $location_stmt->execute();
    $location_data = $location_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($location_data) {
        // Build address string, handling null values properly
        $address_parts = [];
        if (!empty($location_data['street_number'])) $address_parts[] = $location_data['street_number'];
        if (!empty($location_data['street_name'])) $address_parts[] = $location_data['street_name'];
        if (!empty($location_data['barangay'])) $address_parts[] = $location_data['barangay'];
        if (!empty($location_data['city_municipality'])) $address_parts[] = $location_data['city_municipality'];
        if (!empty($location_data['province'])) $address_parts[] = $location_data['province'];
        
        $formatted_address = implode(', ', $address_parts);
        
        $response['location'] = [
            'latitude' => $location_data['latitude'],
            'longitude' => $location_data['longitude'],
            'timestamp' => $location_data['timestamp'],
            'address' => $formatted_address,
            'address_parts' => [
                'street_number' => $location_data['street_number'] ?? '',
                'street_name' => $location_data['street_name'] ?? '',
                'barangay' => $location_data['barangay'] ?? '',
                'city' => $location_data['city_municipality'] ?? '',
                'province' => $location_data['province'] ?? '',
                'postal_code' => $location_data['postal_code'] ?? ''
            ]
        ];
    }
    
    // If user is a runner (role_id = 2) or has applied to be a runner, get runner specific info
    if ($user_data['role_id'] == 2 || $user_data['runner_status'] == 'Pending Runner') {
        $runner_query = "SELECT r.runner_id, r.transportation_method, r.application_status, r.is_available
                         FROM runners r
                         WHERE r.user_id = :user_id";
        
        $runner_stmt = $conn->prepare($runner_query);
        $runner_stmt->bindParam(':user_id', $user_id);
        $runner_stmt->execute();
        $runner_data = $runner_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($runner_data) {
            $response['runner_id'] = $runner_data['runner_id'];
            $response['transportation_method'] = $runner_data['transportation_method'] ?? 'Not specified';
            $response['application_status'] = $runner_data['application_status'];
            $response['is_available'] = (bool)$runner_data['is_available'];
            
            // Get more detailed transportation information based on method
            if ($runner_data['transportation_method'] == 'vehicle') {
                $vehicle_query = "SELECT vehicle_type FROM runner_vehicles WHERE runner_id = :runner_id";
                $vehicle_stmt = $conn->prepare($vehicle_query);
                $vehicle_stmt->bindParam(':runner_id', $runner_data['runner_id']);
                $vehicle_stmt->execute();
                $vehicle_data = $vehicle_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($vehicle_data) {
                    $response['transportation_method'] = $vehicle_data['vehicle_type'];
                }
            } else if ($runner_data['transportation_method'] == 'commute') {
                $transit_query = "SELECT transit_type FROM runner_transit WHERE runner_id = :runner_id";
                $transit_stmt = $conn->prepare($transit_query);
                $transit_stmt->bindParam(':runner_id', $runner_data['runner_id']);
                $transit_stmt->execute();
                $transit_data = $transit_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($transit_data) {
                    $response['transportation_method'] = $transit_data['transit_type'];
                }
            }
            
            // Get service categories
            $services_query = "SELECT sc.category_name, ssc.subcategory_name
                              FROM runner_services rs
                              JOIN service_subcategories ssc ON rs.subcategory_id = ssc.subcategory_id
                              JOIN service_categories sc ON ssc.category_id = sc.category_id
                              WHERE rs.runner_id = :runner_id";
            
            $services_stmt = $conn->prepare($services_query);
            $services_stmt->bindParam(':runner_id', $runner_data['runner_id']);
            $services_stmt->execute();
            $services_data = $services_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response['service_categories'] = [];
            if ($services_data && count($services_data) > 0) {
                foreach ($services_data as $service) {
                    $response['service_categories'][] = $service['subcategory_name'];
                }
            }
        }
    }
    
    // Log response for debugging
    error_log('Runner info response: ' . json_encode($response));
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>