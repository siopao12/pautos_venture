<?php
require_once '../config/db_config.php';
require_once '../includes/role_function.php';

function start_session_if_not_started() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
start_session_if_not_started();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize_input($_POST['firstName'] ?? '');
    $last_name = sanitize_input($_POST['lastName'] ?? '');
    $email = sanitize_input($_POST['registerEmail'] ?? '');
    $phone = sanitize_input($_POST['phoneNumber'] ?? '');
    $password = $_POST['registerPassword'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';
    $terms_agree = isset($_POST['termsAgree']);

    // Validation
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!is_valid_email($email)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (!$terms_agree) $errors[] = "You must agree to the terms and conditions.";

    // Check for existing email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "An account with this email already exists.";
        }
    }

    // Register the user
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Get default role ID
            $stmt = $pdo->prepare("SELECT role_id FROM roles WHERE name = 'default'");
            $stmt->execute();
            $default_role = $stmt->fetch();
            if (!$default_role) {
                throw new Exception("Default role not found.");
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users 
                (first_name, last_name, email, phone, password, role_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                $phone,
                $hashed_password,
                $default_role['role_id']
            ]);

            $user_id = $pdo->lastInsertId();
            $pdo->commit();

            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $_SESSION['user_role'] = 'default';
            $_SESSION['user_role_id'] = $default_role['role_id'];

            $success = "Registration successful!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => empty($errors) ? 'success' : 'error',
        'message' => empty($errors) ? $success : $errors[0],
        'redirect' => '../public/index.php'
    ]);
    exit;
}
?>