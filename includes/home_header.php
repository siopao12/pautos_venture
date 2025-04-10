<header class="header">
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar" aria-expanded="false" aria-controls="sidebar">
    <div class="hamburger-icon">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </button>

  <a href="#" class="header-logo">
    <img src="../assests/image/runner.png" style="height: 40px; margin-right: 10px;">
    Pa-Utos
  </a>

  <div class="header-actions">
    <div class="header-icon" aria-label="Saved items" role="button" tabindex="0">
      <i class="bi bi-bookmark"></i>
      <span class="badge rounded-pill">3</span>
    </div>

    <div class="header-icon" aria-label="Notifications" role="button" tabindex="0">
      <i class="bi bi-bell"></i>
      <span class="badge rounded-pill">5</span>
    </div>

    <div class="dropdown">
    <img src="<?= isset($profile_pic) ? '../' . $profile_pic : '../assests/image/default-profile.jpg' ?>" 
     alt="Profile"
     class="profile-img"
     id="profileDropdown"
     data-bs-toggle="dropdown"
     aria-expanded="false">

      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
        <li><span class="dropdown-item-text fw-bold"><?= htmlspecialchars($user_name) ?></span></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</header>

<!-- Profile Modal (moved outside of <ul>) -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form enctype="multipart/form-data" id="profileForm">
        <div class="modal-header">
          <h5 class="modal-title" id="profileModalLabel">Update Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Profile Picture Section -->
          <div class="text-center mb-4">
            <div class="position-relative d-inline-block">
            <img 
                src="<?= isset($profile_pic) ? '../' . $profile_pic : '../assests/image/default-profile.jpg' ?>" 
                id="profilePreview" 
                class="rounded-circle" 
                style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #dee2e6;"
              >
              <label for="profilePicture" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="cursor: pointer;">
                <i class="bi bi-camera-fill"></i>
                <span class="visually-hidden">Upload profile picture</span>
              </label>
            </div>
            <input type="file" class="d-none" id="profilePicture" name="profile_picture" accept="image/*">
            <small class="form-text text-muted d-block mt-2">Click on the camera icon to upload your profile picture</small>
          </div>
          
          <input type="hidden" name="update_type" value="profile_picture">

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('profilePicture').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('profilePreview').src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
});
</script>

