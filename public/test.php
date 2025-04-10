<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/role_functions.php';

start_session_if_not_started();

// Check if user is logged in
if (!is_logged_in()) {
    redirect('../auth/login.php');
}

// Check if user can apply for runner role
$user_id = $_SESSION['user_id'];
if (!can_apply_for_runner($user_id)) {
    // Check if user already has a pending application
    $application_status = get_application_status($user_id);
    if ($application_status === 'pending') {
        redirect('application-status.php');
    } elseif (!user_has_role($user_id, 'default')) {
        // User already has a non-default role
        redirect('../index.php');
    }
}

$errors = [];
$success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $id_number = sanitize_input($_POST['id_number'] ?? '');
    $vehicle_type = sanitize_input($_POST['vehicle_type'] ?? '');
    $vehicle_details = sanitize_input($_POST['vehicle_details'] ?? '');
    $experience = sanitize_input($_POST['experience'] ?? '');
    $availability = sanitize_input($_POST['availability'] ?? '');
    
    // Validate inputs
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($id_number)) {
        $errors[] = "ID number is required";
    }
    
    if (empty($vehicle_type)) {
        $errors[] = "Vehicle type is required";
    }
    
    if (empty($vehicle_details)) {
        $errors[] = "Vehicle details are required";
    }
    
    if (empty($experience)) {
        $errors[] = "Experience information is required";
    }
    
    if (empty($availability)) {
        $errors[] = "Availability information is required";
    }
    
    // If no errors, insert application into database
    if (empty($errors)) {
        try {
            // Prepare SQL statement
            $stmt = $pdo->prepare("
                INSERT INTO runner_applications (
                    user_id, full_name, address, phone, id_number, 
                    vehicle_type, vehicle_details, experience, availability, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            // Execute statement
            $stmt->execute([
                $user_id, $full_name, $address, $phone, $id_number,
                $vehicle_type, $vehicle_details, $experience, $availability
            ]);
            
            $success = "Your application has been submitted successfully! We will review it shortly.";
            echo "<script>setTimeout(function(){ window.location.href = 'application-status.php'; }, 2000);</script>";
        } catch (PDOException $e) {
            $errors[] = "Application submission failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply as Runner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Apply as Runner</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($full_name) ? $full_name : ''; ?>" required>
                                <div class="invalid-feedback">
                                    Please enter your full name.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo isset($address) ? $address : ''; ?></textarea>
                                <div class="invalid-feedback">
                                    Please enter your address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($phone) ? $phone : ''; ?>" required>
                                <div class="invalid-feedback">
                                    Please enter your phone number.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_number" class="form-label">ID Number</label>
                                <input type="text" class="form-control" id="id_number" name="id_number" value="<?php echo isset($id_number) ? $id_number : ''; ?>" required>
                                <div class="invalid-feedback">
                                    Please enter your ID number.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                                    <option value="" selected disabled>Select vehicle type</option>
                                    <option value="bicycle" <?php echo (isset($vehicle_type) && $vehicle_type === 'bicycle') ? 'selected' : ''; ?>>Bicycle</option>
                                    <option value="motorcycle" <?php echo (isset($vehicle_type) && $vehicle_type === 'motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                                    <option value="car" <?php echo (isset($vehicle_type) && $vehicle_type === 'car') ? 'selected' : ''; ?>>Car</option>
                                    <option value="van" <?php echo (isset($vehicle_type) && $vehicle_type === 'van') ? 'selected' : ''; ?>>Van</option>
                                    <option value="other" <?php echo (isset($vehicle_type) && $vehicle_type === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a vehicle type.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="vehicle_details" class="form-label">Vehicle Details</label>
                                <textarea class="form-control" id="vehicle_details" name="vehicle_details" rows="3" placeholder="Provide details about your vehicle (make, model, year, etc.)" required><?php echo isset($vehicle_details) ? $vehicle_details : ''; ?></textarea>
                                <div class="invalid-feedback">
                                    Please provide details about your vehicle.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="experience" class="form-label">Experience</label>
                                <textarea class="form-control" id="experience" name="experience" rows="3" placeholder="Describe your experience as a delivery person or driver" required><?php echo isset($experience) ? $experience : ''; ?></textarea>
                                <div class="invalid-feedback">
                                    Please provide information about your experience.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="availability" class="form-label">Availability</label>
                                <textarea class="form-control" id="availability" name="availability" rows="3" placeholder="Describe your availability (days, hours, etc.)" required><?php echo isset($availability) ? $availability : ''; ?></textarea>
                                <div class="invalid-feedback">
                                    Please provide information about your availability.
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                                <a href="../index.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>

