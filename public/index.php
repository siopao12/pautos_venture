
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pa-Utos - Your Personal Errand Runner</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assests/css/index.css">
    
</head>
<body>
 <!-- Navigation Bar -->
 <?php
    // Include the login form from auth/login.php
    include '../includes/navbar.php';  
?>

    <?php
    // Include the login form from auth/login.php
    include '../auth/login.php';  
    include '../auth/registration.php';
?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Your Personal <span>Errand Runner</span></h1>
                    <p class="lead mb-4">Get your tasks done quickly and efficiently with our trusted errand service providers.</p>
                    <a href="#" class="btn btn-primary px-4 py-2">Learn More</a>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="../assests/image/runner.png?height=300&width=300" alt="Runner Illustration" class="runner-image">
                </div>
            </div>
        </div>
    </section>

   <!-- Services Section -->
   <?php
    // Include the login form from includes/service.php
    include '../includes/service.php';  
?>

 <!-- Testimonials Section -->
 <?php
    // Include the login form from includes/service.php
    include '../includes/testimonials.php';  
?>

    <!-- Featured Services Section (from second image) -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <img src="/placeholder.svg?height=200&width=400" class="card-img-top" alt="Service 1">
                        <div class="card-body text-center">
                            <p class="card-text">Need someone to help with your errands?</p>
                            <a href="#" class="btn btn-outline-primary">Discover →</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <img src="/placeholder.svg?height=200&width=400" class="card-img-top" alt="Service 2">
                        <div class="card-body text-center">
                            <p class="card-text">Need help with your business?</p>
                            <a href="#" class="btn btn-outline-primary">Discover →</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <img src="/placeholder.svg?height=200&width=400" class="card-img-top" alt="Service 3">
                        <div class="card-body text-center">
                            <p class="card-text">Looking for professional assistance?</p>
                            <a href="#" class="btn btn-outline-primary">Discover →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 <!-- Footer -->
 <?php
    // Include the login form from includes/footer.php
    include '../includes/reusable/footer.php';  
?>

   
   
    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assests/js/validate.js"></script>
    <script src="../assests/js/loginapi.js"></script>

    
</body>
</html>