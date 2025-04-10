<?php
session_start();  

// Simple check - if no user_id or session is expired, redirect to login
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

// Add these stronger cache control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");

// Rest of your code follows...
$user_name = $_SESSION['user_name'] ?? '';

// Get profile picture from session or use default
$profile_pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : '../assests/image/default-profile.jpg';

// Add debugging if needed
// echo "Debug - Profile pic: " . $profile_pic;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Pa-utos - Your trusted errand service platform in Davao City">
  <title>Pa-utos - Your Trusted Errand Service</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assests/css/home.css">
  
</head>
<body>
  <!-- Header -->
  <?php
    // Include the login form from auth/login.php
    include '../includes/home_header.php';  
?>
  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar" aria-labelledby="sidebarToggle" aria-hidden="true">
    <nav class="sidebar-nav">
      <a href="#" class="sidebar-item active">
        <i class="bi bi-house"></i>
        <span>Home</span>
      </a>
      <a href="#" class="sidebar-item">
        <i class="bi bi-list-check"></i>
        <span>My Errands</span>
      </a>
      <a href="#" class="sidebar-item">
        <i class="bi bi-clipboard-data"></i>
        <span>Status</span>
      </a>
      <a href="#" class="sidebar-item">
        <i class="bi bi-clock-history"></i>
        <span>History</span>
      </a>
      <a href="#" class="sidebar-item">
        <i class="bi bi-person"></i>
        <span>Profile</span>
      </a>
      <div class="sidebar-divider"></div>
      <a href="#" class="sidebar-item">
        <i class="bi bi-gear"></i>
        <span>Settings</span>
      </a>
      <a href="../auth/logout.php" class="sidebar-item">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Welcome Section -->
    <section class="welcome-section mb-4">
      <h1 class="fw-bold">Welcome to Pa-utos, Gerald!</h1>
      <p class="text-muted">Your trusted errand service platform</p>
    </section>


 <!-- How It Works Section with Carousel -->
 <?php
    // Include the login form from auth/login.php
    include '../includes/home_coursell.php';  
?>

    <!-- Runner Search Section -->

    <?php
    // Include the login form from auth/login.php
    include '../includes/home_runner.php';  
?>

      <!-- Pagination -->
      <?php
    // Include the login form from auth/login.php
    include '../includes/home_pagination.php';  
?>
    
  </main>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assests/js/home_runner.js"></script>
  <script src="../assests/js/upload_profile.js"></script>


</body>
</html>