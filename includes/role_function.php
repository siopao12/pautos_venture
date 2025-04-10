<?php
// Get user role information
function get_user_role($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT r.role_id, r.name, r.description 
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = ?
    ");
    
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Check if user has specific role
function user_has_role($user_id, $role_name) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.user_id = ? AND r.name = ?
    ");
    
    $stmt->execute([$user_id, $role_name]);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

// Update user role
function update_user_role($user_id, $role_name) {
    global $pdo;
    
    // Get role ID
    $stmt = $pdo->prepare("SELECT role_id FROM roles WHERE name = ?");
    $stmt->execute([$role_name]);
    $role = $stmt->fetch();
    
    if (!$role) {
        return false;
    }
    
    // Update user role
    $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
    $stmt->execute([$role['role_id'], $user_id]);
    
    // Update session if this is the current user
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
        $_SESSION['user_role'] = $role_name;
    }
    
    return $stmt->rowCount() > 0;
}

// Get role name by role ID
function get_role_name($role_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT name FROM roles WHERE role_id = ?");
    $stmt->execute([$role_id]);
    $result = $stmt->fetch();
    
    return $result ? $result['name'] : 'default';
}

// Check if user has pending runner application
function has_pending_application($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM runner_applications
        WHERE user_id = ? AND status = 'pending'
    ");
    
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

// Get application status for user
function get_application_status($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT status
        FROM runner_applications
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 1
    ");
    
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    return $result ? $result['status'] : null;
}

// Get all pending applications
function get_pending_applications() {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT ra.*, u.email
        FROM runner_applications ra
        JOIN users u ON ra.user_id = u.user_id
        WHERE ra.status = 'pending'
        ORDER BY ra.created_at ASC
    ");
    
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get application details
function get_application_details($application_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT ra.*, u.email, u.first_name, u.last_name
        FROM runner_applications ra
        JOIN users u ON ra.user_id = u.user_id
        WHERE ra.id = ?
    ");
    
    $stmt->execute([$application_id]);
    return $stmt->fetch();
}

// Review application
function review_application($application_id, $reviewer_id, $status, $comments = '') {
    global $pdo;
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update application status
        $stmt = $pdo->prepare("
            UPDATE runner_applications 
            SET status = ? 
            WHERE id = ?
        ");
        $stmt->execute([$status, $application_id]);
        
        // Add review record
        $stmt = $pdo->prepare("
            INSERT INTO application_reviews (application_id, reviewer_id, status, comments)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$application_id, $reviewer_id, $status, $comments]);
        
        // If approved, update user role
        if ($status === 'approved') {
            // Get user ID from application
            $stmt = $pdo->prepare("SELECT user_id FROM runner_applications WHERE id = ?");
            $stmt->execute([$application_id]);
            $application = $stmt->fetch();
            
            if ($application) {
                // Get runner role ID
                $stmt = $pdo->prepare("SELECT role_id FROM roles WHERE name = 'runner'");
                $stmt->execute();
                $role = $stmt->fetch();
                
                if ($role) {
                    // Update user role
                    $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE user_id = ?");
                    $stmt->execute([$role['role_id'], $application['user_id']]);
                    
                    // Update session if this is the current user
                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $application['user_id']) {
                        $_SESSION['user_role'] = 'runner';
                    }
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Review application error: " . $e->getMessage());
        return false;
    }
}

// Check if user can apply for runner role
function can_apply_for_runner($user_id) {
    // User must have default role and no pending applications
    return user_has_role($user_id, 'default') && !has_pending_application($user_id);
}

// Check if user can review applications
function can_review_applications($user_id) {
    return user_has_role($user_id, 'owner') || user_has_role($user_id, 'admin');
}
?>