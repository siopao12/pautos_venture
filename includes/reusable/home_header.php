<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

      <!-- Become a Runner -->
      <li>
        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#verifyRunnerModal">
          <i class="fa-solid fa-person-running me-2" style="color: #00A1D6;"></i>Become a Runner
        </a>
      </li>

      <!-- Profile -->
      <li>
        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
          <i class="fa-solid fa-user me-2" style="color: #00A1D6;"></i>Profile
        </a>
      </li>

      <!-- Settings -->
      <li>
        <a class="dropdown-item" href="#">
          <i class="fa-solid fa-gear me-2" style="color: #00A1D6;"></i>Settings
        </a>
      </li>

      <li><hr class="dropdown-divider"></li>

      <!-- Logout -->
      <li>
        <a class="dropdown-item" href="../auth/logout.php">
          <i class="fa-solid fa-right-from-bracket me-2" style="color: #00A1D6;"></i>Logout
        </a>
      </li>
    </ul>
    </div>
  </div>
</header>

