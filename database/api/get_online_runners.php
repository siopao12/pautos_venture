<?php
// This should be saved at /database/api/get_online_runners.php
require_once __DIR__ . '/../../config/db_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Add debugging to help find issues
error_log('get_online_runners.php accessed');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('User not logged in');
    echo json_encode(['success' => false, 'message' => 'User not logged in', 'error_code' => 'AUTH_ERROR']);
    exit;
}

$user_id = $_SESSION['user_id'];
error_log('User ID: ' . $user_id);

try {
    // Get current user's location first - checking both 'current' and 'adjusted' location types
    $userLocationQuery = "SELECT latitude, longitude FROM user_locations 
                         WHERE user_id = ? AND (location_type = 'current' OR location_type = 'adjusted')
                         ORDER BY timestamp DESC LIMIT 1";
    $userLocationStmt = $pdo->prepare($userLocationQuery);
    $userLocationStmt->execute([$user_id]);
    $userLocation = $userLocationStmt->fetch(PDO::FETCH_ASSOC);
    
    error_log('User location retrieved: ' . ($userLocation ? 'Yes' : 'No'));
    
    // Construct the query to get all online runners except the current user
    // Using the correct column names from your database schema
    $query = "SELECT 
                u.user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS name,
                u.email,
                u.phone,
                up.profile_picture,
                r.runner_id,
                r.transportation_method,
                r.is_available,
                GROUP_CONCAT(DISTINCT sc.subcategory_name) AS services,
                ul.latitude,
                ul.longitude,
                ua.street_number,
                ua.street_name,
                ua.barangay,
                ua.city_municipality AS city,
                ua.province,
                ua.postal_code,
                CONCAT(
                    COALESCE(ua.street_number, ''), ' ',
                    COALESCE(ua.street_name, ''), ', ',
                    COALESCE(ua.barangay, ''), ', ',
                    COALESCE(ua.city_municipality, ''), ', ',
                    COALESCE(ua.province, '')
                ) AS formatted_address,
                rv.vehicle_type  /* Added vehicle information */
              FROM 
                users u
              JOIN 
                runners r ON u.user_id = r.user_id
              LEFT JOIN 
                user_profiles up ON u.user_id = up.user_id
              LEFT JOIN 
                runner_services rs ON r.runner_id = rs.runner_id
              LEFT JOIN 
                service_subcategories sc ON rs.subcategory_id = sc.subcategory_id
              LEFT JOIN 
                user_locations ul ON u.user_id = ul.user_id AND (ul.location_type = 'current' OR ul.location_type = 'adjusted')
              LEFT JOIN 
                user_address ua ON ul.address_id = ua.address_id
              LEFT JOIN
                runner_vehicles rv ON r.runner_id = rv.runner_id
              WHERE 
                r.is_available = 1 AND u.user_id != ? AND r.application_status = 'approved'
              GROUP BY 
                u.user_id
              ORDER BY 
                u.user_id ASC";
                
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $runners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log('Runners found: ' . count($runners));
    
    // Process runner data
    foreach ($runners as &$runner) {
        // Profile picture path handling
        if (empty($runner['profile_picture'])) {
            // Default profile picture
            $runner['profile_pic'] = '../assests/image/uploads/profile_pictures/profile.jpg';
        } else {
            // If the path already contains the full path, use it directly
            if (strpos($runner['profile_picture'], 'assests/image/uploads/profile_pictures/') !== false) {
                $runner['profile_pic'] = '../' . $runner['profile_picture'];
            } else {
                // Otherwise, append the path
                $runner['profile_pic'] = '../assests/image/uploads/profile_pictures/' . $runner['profile_picture'];
            }
        }
        
        // Convert services string to array
        if (!empty($runner['services'])) {
            $runner['services_array'] = explode(',', $runner['services']);
        } else {
            $runner['services_array'] = [];
        }
    }
    
    // Calculate distance if user has location
    if ($userLocation && count($runners) > 0) {
        foreach ($runners as &$runner) {
            if (isset($runner['latitude']) && isset($runner['longitude'])) {
                // Calculate distance using Haversine formula
                $distance = calculateDistance(
                    $userLocation['latitude'], 
                    $userLocation['longitude'],
                    $runner['latitude'],
                    $runner['longitude']
                );
                $runner['distance'] = round($distance, 1); // Round to 1 decimal place
            } else {
                $runner['distance'] = null;
            }
        }
        
        // Sort by distance if available
        usort($runners, function($a, $b) {
            if (!isset($a['distance']) || !isset($b['distance'])) {
                return 0;
            }
            return $a['distance'] <=> $b['distance'];
        });
    }
    
    // Return success response
    $response = [
        'success' => true,
        'runners' => $runners,
        'user_location' => $userLocation,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    error_log('Returning response with ' . count($runners) . ' runners');
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Error fetching online runners: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// Function to calculate distance between two points using Haversine formula
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius of the Earth in kilometers
    
    $latDiff = deg2rad($lat2 - $lat1);
    $lonDiff = deg2rad($lon2 - $lon1);
    
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lonDiff / 2) * sin($lonDiff / 2);
         
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
    
    return $distance;
}
?>