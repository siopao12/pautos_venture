<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Runner Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assests/css/runner_dashboard.css">
</head>
<body>
    <!-- Location Permission Modal -->
    <div class="modal fade" id="locationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locationModalLabel">Location Access Required</h5>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-geo-alt text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <p>To start your runner duties, we need access to your location. This helps us connect you with nearby customers.</p>
                    <p>Your location will only be updated while you're marked as "Available".</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" id="allowLocationBtn">Allow Location Access</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Container -->
    <div class="dashboard-container py-4">
        <div class="container">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="profile-section">
                        <img src="../assests/image/default-profile.jpg" alt="Profile" class="profile-pic" id="profile-image">
                        <div>
                            <h1 class="h3 mb-0" id="runner-name">Welcome, Runner</h1>
                            <p class="text-muted mb-0" id="runner-email">runner@example.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                        <span id="current-date-time"></span>
                    </div>
                </div>
            </div>

            <!-- Location Banner -->
            <div class="location-banner" id="location-banner">
                <div>
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    <span id="location-status">Waiting for location access...</span>
                </div>
                <div>
                    <span id="location-update-time"></span>
                    <div class="spinner-border spinner-border-sm text-dark location-spinner d-none" id="location-spinner" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Status Toggle -->
            <div class="status-toggle">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">Runner Status</h5>
                        <p class="text-muted small mb-md-0">Toggle your availability to receive customer requests</p>
                    </div>
                    <div class="col-md-6">
                        <div class="toggle-container float-md-end">
                            <div class="status-indicator status-offline" id="status-indicator"></div>
                            <span class="me-3" id="status-text">Offline</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="availabilityToggle" disabled>
                                <label class="form-check-label" for="availabilityToggle">Available/On Duty</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card stats-card h-100">
                                <div class="card-body">
                                    <h6 class="text-muted">Today's Requests</h6>
                                    <h2 class="mb-0">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card h-100" style="border-left-color: #28a745;">
                                <div class="card-body">
                                    <h6 class="text-muted">Completed</h6>
                                    <h2 class="mb-0">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stats-card h-100" style="border-left-color: #ffc107;">
                                <div class="card-body">
                                    <h6 class="text-muted">Earnings</h6>
                                    <h2 class="mb-0">₱0.00</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Requests -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Current Requests</span>
                            <button class="btn btn-sm btn-outline-primary" id="refreshRequestsBtn">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="requests-container">
                                <div class="text-center py-5" id="no-requests-message">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No active requests at the moment.</p>
                                    <p class="text-muted small">New requests will appear here when customers need your services.</p>
                                </div>
                                
                                <!-- Sample request card (hidden by default) -->
                                <div class="card request-card mb-3 d-none" id="sample-request">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0">
                                                <i class="bi bi-cart3 category-icon"></i>
                                                <span class="request-type">Grocery Shopping</span>
                                            </h5>
                                            <span class="badge bg-primary badge-pending">Pending</span>
                                        </div>
                                        <p class="mb-2 request-description">Pick up groceries from SM Supermarket and deliver to customer's address.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> 2.5 km away
                                            </small>
                                            <small class="text-muted request-time">15 minutes ago</small>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary me-2">Details</button>
                                            <button class="btn btn-sm btn-primary">Accept</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">Recent Activity</div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <small class="text-muted">Just now</small>
                                    <p class="mb-0">You logged into the runner dashboard</p>
                                </div>
                                <!-- More timeline items would appear here as the runner performs actions -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Map -->
                    <div class="card mb-4">
                        <div class="card-header">Your Location</div>
                        <div class="card-body p-0">
                            <div class="map-container" id="map">
                                <div class="d-flex justify-content-center align-items-center h-100 bg-light">
                                    <div class="text-center">
                                        <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Location map will appear here</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile/Status Card -->
                    <div class="card mb-4">
                        <div class="card-header">Runner Details</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Transportation Method</label>
                                <p class="mb-0" id="transportation-method">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Service Categories</label>
                                <div id="service-categories">
                                    <span class="badge bg-light text-dark mb-1">No services selected</span>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small">Status</label>
                                <p class="mb-0" id="runner-status">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">Quick Actions</div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" type="button">
                                    <i class="bi bi-person-lines-fill"></i> View Profile
                                </button>
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-gear"></i> Settings
                                </button>
                                <a href="../../auth/logout.php" class="btn btn-outline-danger" role="button">
    <i class="bi bi-box-arrow-right"></i> Sign Out
</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Maps API (Use your own API key here) -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
    <script src="../../assests/js/runner_dashboard.js"></script>
   