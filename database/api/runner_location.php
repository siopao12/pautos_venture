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

// Check if this is a POST request with location data
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

if (!$data || !isset($data['latitude']) || !isset($data['longitude'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid location data'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];
$timestamp = isset($data['timestamp']) ? $data['timestamp'] : date('Y-m-d H:i:s');
$address_id = null;

try {
    // Create PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start transaction
    $conn->beginTransaction();
    
    // Check if human-readable address is provided
    if (isset($data['address']) && is_array($data['address'])) {
        $address = $data['address'];
        
        // Insert or update address
        $address_query = "INSERT INTO user_address (
                            street_number, 
                            street_name, 
                            barangay, 
                            district, 
                            city_municipality, 
                            province, 
                            region, 
                            postal_code, 
                            landmark
                          ) VALUES (
                            :street_number, 
                            :street_name, 
                            :barangay, 
                            :district, 
                            :city_municipality, 
                            :province, 
                            :region, 
                            :postal_code, 
                            :landmark
                          )";
        
                        // Create variables first
                        $street_number = $address['street_number'] ?? null;
                        $street_name = $address['street_name'] ?? null;
                        $barangay = $address['barangay'] ?? null;
                        $district = $address['district'] ?? null;
                        $city_municipality = $address['city_municipality'] ?? null;
                        $province = $address['province'] ?? null;
                        $region = $address['region'] ?? null;
                        $postal_code = $address['postal_code'] ?? null;
                        $landmark = $address['landmark'] ?? null;

                        // Then bind the variables
                        $address_stmt = $conn->prepare($address_query);
                        $address_stmt->bindParam(':street_number', $street_number);
                        $address_stmt->bindParam(':street_name', $street_name);
                        $address_stmt->bindParam(':barangay', $barangay);
                        $address_stmt->bindParam(':district', $district);
                        $address_stmt->bindParam(':city_municipality', $city_municipality);
                        $address_stmt->bindParam(':province', $province);
                        $address_stmt->bindParam(':region', $region);
                        $address_stmt->bindParam(':postal_code', $postal_code);
                        $address_stmt->bindParam(':landmark', $landmark);
                        $address_stmt->execute();
                                
        $address_id = $conn->lastInsertId();
    }
    
    // Insert location
    $location_query = "INSERT INTO user_locations (
                          user_id, 
                          location_type, 
                          latitude, 
                          longitude, 
                          address_id, 
                          timestamp
                       ) VALUES (
                          :user_id, 
                          'current', 
                          :latitude, 
                          :longitude, 
                          :address_id, 
                          :timestamp
                       )";
    
    $location_stmt = $conn->prepare($location_query);
    $location_stmt->bindParam(':user_id', $user_id);
    $location_stmt->bindParam(':latitude', $latitude);
    $location_stmt->bindParam(':longitude', $longitude);
    $location_stmt->bindParam(':address_id', $address_id);
    $location_stmt->bindParam(':timestamp', $timestamp);
    $location_stmt->execute();
    
    // If user is a runner, update their availability status if provided
    if (isset($data['is_available'])) {
        $is_available = $data['is_available'] ? 1 : 0;
        
        $runner_query = "UPDATE runners 
                         SET is_available = :is_available 
                         WHERE user_id = :user_id";
        
        $runner_stmt = $conn->prepare($runner_query);
        $runner_stmt->bindParam(':is_available', $is_available);
        $runner_stmt->bindParam(':user_id', $user_id);
        $runner_stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Location updated successfully',
        'timestamp' => $timestamp
    ]);
    
} catch (PDOException $e) {
    // Roll back transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>