    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Owner Dashboard</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="../../assests/css/owner_dashboard.css">
    </head>

    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 sidebar p-0">
                    <div class="d-flex flex-column p-3">
                        <a href="#" class="d-flex align-items-center mb-3 text-decoration-none text-white">
                            <span class="fs-4">Owner Dashboard</span>
                        </a>
                        <hr>
                        <ul class="nav nav-pills flex-column mb-auto">
                            <li class="nav-item">
                                <a href="#" class="nav-link active" aria-current="page">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="#" class="nav-link" id="runner-applications-link">
                                    <i class="fas fa-user-check me-2"></i>
                                    Runner Applications
                                </a>
                            </li>
                            <li>
                                <a href="#" class="nav-link">
                                    <i class="fas fa-clipboard-list me-2"></i>
                                    Orders
                                </a>
                            </li>
                            <li>
                                <a href="#" class="nav-link">
                                    <i class="fas fa-cog me-2"></i>
                                    Settings
                                </a>
                            </li>
                            <li>
                                <a href=" ../../auth/logout.php " class="nav-link">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9 ms-sm-auto col-lg-10 main-content">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Dashboard Overview</h1>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <div class="btn-group me-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                                <i class="fas fa-calendar me-1"></i>
                                This week
                            </button>
                        </div>
                    </div>

                    <!-- Dashboard Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Pending Applications</h5>
                                            <h2 class="display-4" id="pending-count">0</h2>
                                        </div>
                                        <i class="fas fa-user-clock fa-3x"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white text-decoration-none">View Details</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Approved Runners</h5>
                                            <h2 class="display-4" id="approved-count">0</h2>
                                        </div>
                                        <i class="fas fa-user-check fa-3x"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white text-decoration-none">View Details</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Rejected Applications</h5>
                                            <h2 class="display-4" id="rejected-count">0</h2>
                                        </div>
                                        <i class="fas fa-user-times fa-3x"></i>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white text-decoration-none">View Details</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Runner Applications Section -->
                    <div id="runner-applications-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>Runner Applications</h3>
                            <div class="input-group w-50">
                                <input type="text" id="search-runner" class="form-control" placeholder="Search by name, email, or transportation...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Filter Options -->
                        <div class="mb-4">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">All</button>
                                <button type="button" class="btn btn-outline-warning filter-btn" data-filter="pending">Pending</button>
                                <button type="button" class="btn btn-outline-success filter-btn" data-filter="approved">Approved</button>
                                <button type="button" class="btn btn-outline-danger filter-btn" data-filter="rejected">Rejected</button>
                            </div>
                            <div class="btn-group ms-2" role="group">
                                <button type="button" class="btn btn-outline-secondary transport-filter" data-transport="all">All Transport</button>
                                <button type="button" class="btn btn-outline-secondary transport-filter" data-transport="vehicle">Vehicle</button>
                                <button type="button" class="btn btn-outline-secondary transport-filter" data-transport="walking">Walking</button>
                                <button type="button" class="btn btn-outline-secondary transport-filter" data-transport="commute">Public Transit</button>
                            </div>
                        </div>

                        <!-- Runner Applications Cards -->
                        <div class="row" id="runner-applications-container">
                            <!-- Cards will be populated dynamically through JavaScript -->
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading runner applications...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Runner Details Modal -->
        <div class="modal fade" id="runnerDetailsModal" tabindex="-1" aria-labelledby="runnerDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="runnerDetailsModalLabel">Runner Application Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center mb-3">
                                    <img id="modal-runner-profile" src="" alt="Runner Profile" class="img-fluid rounded mb-2">
                                    <h5 id="modal-runner-name" class="mb-0"></h5>
                                    <p id="modal-runner-email" class="text-muted"></p>
                                    <span id="modal-runner-status" class="status-badge"></span>
                                </div>
                                <div class="mb-3">
                                    <h6>Personal Information</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Phone:</strong> <span id="modal-runner-phone"></span></li>
                                        <li class="list-group-item"><strong>Applied on:</strong> <span id="modal-runner-date"></span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <ul class="nav nav-tabs" id="runnerDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button" role="tab">Documents</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="transportation-tab" data-bs-toggle="tab" data-bs-target="#transportation-tab-pane" type="button" role="tab">Transportation</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-tab-pane" type="button" role="tab">Services</button>
                                    </li>
                                </ul>
                                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="runnerDetailsTabContent">
                                    <div class="tab-pane fade show active" id="documents-tab-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <h6>ID Photo</h6>
                                                <img id="modal-id-photo" src="" alt="ID Photo" class="modal-img img-thumbnail">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <h6>Selfie Photo</h6>
                                                <img id="modal-selfie-photo" src="" alt="Selfie Photo" class="modal-img img-thumbnail">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="transportation-tab-pane" role="tabpanel" aria-labelledby="transportation-tab" tabindex="0">
                                        <div class="mb-3">
                                            <h6>Transportation Method</h6>
                                            <p><span class="badge bg-info" id="modal-transportation-method"></span></p>
                                        </div>
                                        
                                        <!-- Vehicle Details (conditionally shown) -->
                                        <div id="vehicle-details-section">
                                            <div class="mb-3">
                                                <h6>Vehicle Details</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Vehicle Type:</strong> <span id="modal-vehicle-type"></span></li>
                                                    <li class="list-group-item"><strong>Registration Number:</strong> <span id="modal-registration-number"></span></li>
                                                    <li class="list-group-item"><strong>License Number:</strong> <span id="modal-license-number"></span></li>
                                                    <li class="list-group-item"><strong>Contact Phone:</strong> <span id="modal-vehicle-phone"></span></li>
                                                </ul>
                                            </div>
                                            <div class="mb-3">
                                                <h6>Vehicle Photo</h6>
                                                <img id="modal-vehicle-photo" src="" alt="Vehicle Photo" class="modal-img img-thumbnail">
                                            </div>
                                        </div>
                                        
                                        <!-- Walking Details (conditionally shown) -->
                                        <div id="walking-details-section">
                                            <div class="mb-3">
                                                <h6>Walking Service Details</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Service Radius:</strong> <span id="modal-service-radius"></span> km</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <!-- Transit Details (conditionally shown) -->
                                        <div id="transit-details-section">
                                            <div class="mb-3">
                                                <h6>Public Transit Details</h6>
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Transit Type:</strong> <span id="modal-transit-type"></span></li>
                                                    <li class="list-group-item"><strong>Transit Radius:</strong> <span id="modal-transit-radius"></span> km</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="services-tab-pane" role="tabpanel" aria-labelledby="services-tab" tabindex="0">
                                        <h6>Offered Services</h6>
                                        <div id="modal-services-list" class="mb-3">
                                            <!-- Services will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="approve-runner-btn">Approve Runner</button>
                        <button type="button" class="btn btn-danger" id="reject-runner-btn">Reject Runner</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Custom JS -->
        <script src="../../assests/js/owner_dashboard.js"></script>
    </body>
    </html>