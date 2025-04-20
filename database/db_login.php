<?php
require_once '../config/db_config.php';
require_once '../includes/role_function.php';

function start_session_if_not_started() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
start_session_if_not_started();

header('Content-Type: application/json');

$email = $_POST['loginEmail'] ?? '';
$password = $_POST['loginPassword'] ?? '';
$errors = [];

if (empty($email)) $errors[] = "Email is required.";
if (empty($password)) $errors[] = "Password is required.";

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Get user role information
        $role = get_user_role($user['user_id']);
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_role'] = $role ? $role['name'] : 'default';
        $_SESSION['user_role_id'] = $user['role_id'];
        
        // Inside login.php, after setting other session variables:
      // Get profile picture if exists
        $profile_stmt = $pdo->prepare("SELECT profile_picture FROM user_profiles WHERE user_id = ?");
        $profile_stmt->execute([$user['user_id']]);
        $profile_data = $profile_stmt->fetch();

        if ($profile_data && !empty($profile_data['profile_picture'])) {
            $_SESSION['profile_pic'] = $profile_data['profile_picture'];
        } else {
            $_SESSION['profile_pic'] = '../assests/image/default-profile.jpg';
        }
        
        // Determine redirect based on role
        $redirect_url = '../public/home.php'; // Default redirect
        
        // Check role and set appropriate redirect
        if ($_SESSION['user_role_id'] == 2) { // Runner
            $redirect_url = '../public/runner/runner_dashboard.php';
        } elseif ($_SESSION['user_role_id'] == 3) { // Owner
            $redirect_url = '../public/owner/owner-dashboard.php';
        } elseif ($_SESSION['user_role_id'] == 4) { // Admin
            $redirect_url = '../public/admin_dashboard.php';
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'redirect' => $redirect_url
        ]);
        exit;
    }
}

echo json_encode([
    'status' => 'error',
    'message' => $errors[0] ?? 'Login failed.'
]);
exit;
?>