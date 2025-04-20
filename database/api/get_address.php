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

// Get coordinates from POST data if provided
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$latitude = null;
$longitude = null;
if ($data && isset($data['latitude']) && isset($data['longitude'])) {
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];
}

try {
    // Create PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get latest location and address information for the user
    $query = "SELECT ul.latitude, ul.longitude, ul.timestamp, 
                      ua.street_number, ua.street_name, ua.barangay, 
                      ua.city_municipality, ua.province
               FROM user_locations ul
               LEFT JOIN user_address ua ON ul.address_id = ua.address_id
               WHERE ul.user_id = :user_id";
    
    // If coordinates were provided, try to find closest matching address
    if ($latitude && $longitude) {
        $query .= " ORDER BY ABS(ul.latitude - :latitude) + ABS(ul.longitude - :longitude) LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
    } else {
        // Otherwise get most recent address
        $query .= " ORDER BY ul.timestamp DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
    }
    
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        // Format address nicely
        $addressParts = [];
        if (!empty($data['street_number'])) $addressParts[] = $data['street_number'];
        if (!empty($data['street_name'])) $addressParts[] = $data['street_name'];
        if (!empty($data['barangay'])) $addressParts[] = $data['barangay'];
        if (!empty($data['city_municipality'])) $addressParts[] = $data['city_municipality'];
        if (!empty($data['province'])) $addressParts[] = $data['province'];
        
        $formattedAddress = !empty($addressParts) ? implode(', ', $addressParts) : "Unknown location";
        
        echo json_encode([
            'success' => true,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'timestamp' => $data['timestamp'],
            'address' => $formattedAddress,
            'address_parts' => [
                'street_number' => $data['street_number'],
                'street_name' => $data['street_name'],
                'barangay' => $data['barangay'],
                'city_municipality' => $data['city_municipality'],
                'province' => $data['province']
            ]
        ]);
    } else {
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
?>