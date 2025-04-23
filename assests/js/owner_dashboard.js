// Global variables
let allRunners = [];
let currentFilters = {
    status: 'all',
    transport: 'all',
    search: ''
};

// Define base URL for assets
// This will be used for all image paths to handle the JS file being in a different location
const BASE_URL = '../../';

// DOM Ready
$(document).ready(function() {
    // Fetch runner applications
    fetchRunnerApplications();
    
    // Setup event listeners
    setupEventListeners();
});

// Fetch runner applications from the server
function fetchRunnerApplications() {
    $.ajax({
        url: '../../database/db_owner/fetch_application.php', // use GET
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Store runners data globally
                allRunners = response.data.runners;
                
                // Update dashboard counters
                updateDashboardCounters(response.data.counts);
                
                // Display runner applications
                displayRunnerApplications(allRunners);
            } else {
                // Show error message with SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to load runner applications',
                    confirmButtonColor: '#3085d6'
                });
            }
        },
        error: function(xhr, status, error) {
            // Show network error message with SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to load runner applications',
                confirmButtonColor: '#3085d6'
            });
            console.error('AJAX Error:', error);
        }
    });
}

// Update dashboard counter cards
function updateDashboardCounters(counts) {
    $('#pending-count').text(counts.pending || 0);
    $('#approved-count').text(counts.approved || 0);
    $('#rejected-count').text(counts.rejected || 0);
}

// Display runner applications as cards
function displayRunnerApplications(runners) {
    const container = $('#runner-applications-container');
    
    // Clear container
    container.empty();
    
    // Apply filters
    const filteredRunners = applyFilters(runners);
    
    // Check if any runners match the filters
    if (filteredRunners.length === 0) {
        container.html(
            `<div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>No runner applications found matching your filters</h5>
                <p class="text-muted">Try changing your search criteria or filters</p>
            </div>`
        );
        return;
    }
    
    // Loop through filtered runners and create cards
    filteredRunners.forEach(runner => {
        // Format status badge
        let statusClass = '';
        let statusIcon = '';
        
        switch(runner.status) {
            case 'pending':
                statusClass = 'bg-warning';
                statusIcon = '<i class="fas fa-clock me-1"></i>';
                break;
            case 'approved':
                statusClass = 'bg-success';
                statusIcon = '<i class="fas fa-check-circle me-1"></i>';
                break;
            case 'rejected':
                statusClass = 'bg-danger';
                statusIcon = '<i class="fas fa-times-circle me-1"></i>';
                break;
            default:
                statusClass = 'bg-secondary';
                statusIcon = '<i class="fas fa-question-circle me-1"></i>';
        }
        
        // Format transportation icon
        let transportIcon = '';
        switch(runner.transportation_method) {
            case 'vehicle':
                transportIcon = '<i class="fas fa-car me-1"></i>';
                break;
            case 'walking':
                transportIcon = '<i class="fas fa-walking me-1"></i>';
                break;
            case 'commute':
                transportIcon = '<i class="fas fa-bus me-1"></i>';
                break;
            default:
                transportIcon = '<i class="fas fa-question me-1"></i>';
        }
        
        // Get profile photo or use placeholder - Using BASE_URL
        const profilePhoto = runner.profile_photo ? 
            BASE_URL + runner.profile_photo : 
            BASE_URL + 'assests/image/uploads/profile_pictures/default-profile.jpg';
        
        // Format application date
        const applicationDate = new Date(runner.created_at).toLocaleDateString();
        
        // Create runner card
        const card = `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card runner-card h-100" data-runner-id="${runner.runner_id}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge ${statusClass}">${statusIcon} ${runner.status.toUpperCase()}</span>
                        <small class="text-muted">Applied: ${applicationDate}</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="${profilePhoto}" class="profile-img me-3" alt="${runner.user_name}" onerror="this.src='${BASE_URL}assests/image/uploads/profile_pictures/default-profile.jpg'">
                            <div>
                                <h5 class="card-title mb-0">${runner.user_name}</h5>
                                <p class="text-muted mb-0">${runner.user_email}</p>
                                <span class="badge bg-info text-dark">${transportIcon} ${capitalize(runner.transportation_method)}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="card-subtitle mb-2">Services Offered:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                ${generateServiceBadges(runner.services)}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm view-application" data-runner-id="${runner.runner_id}">
                            <i class="fas fa-eye me-1"></i> View Details
                        </button>
                        ${runner.status === 'pending' ? `
                            <button class="btn btn-success btn-sm ms-1 quick-approve" data-runner-id="${runner.runner_id}">
                                <i class="fas fa-check me-1"></i> Approve
                            </button>
                            <button class="btn btn-danger btn-sm ms-1 quick-reject" data-runner-id="${runner.runner_id}">
                                <i class="fas fa-times me-1"></i> Reject
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        container.append(card);
    });
    
    // Activate event listeners for the newly created cards
    activateCardEventListeners();
}

// Generate service badges
function generateServiceBadges(services) {
    if (!services || services.length === 0) {
        return '<span class="badge bg-light text-dark">No services specified</span>';
    }
    
    // Group services by category
    const categories = {};
    services.forEach(service => {
        if (!categories[service.category_code]) {
            categories[service.category_code] = {
                name: service.category_name,
                count: 0
            };
        }
        categories[service.category_code].count++;
    });
    
    // Generate badges for each category
    let badges = '';
    for (const code in categories) {
        const category = categories[code];
        badges += `<span class="badge bg-secondary">${category.name} (${category.count})</span> `;
    }
    
    return badges;
}

// Apply filters to runners
function applyFilters(runners) {
    return runners.filter(runner => {
        // Filter by status
        if (currentFilters.status !== 'all' && runner.status !== currentFilters.status) {
            return false;
        }
        
        // Filter by transportation method
        if (currentFilters.transport !== 'all' && runner.transportation_method !== currentFilters.transport) {
            return false;
        }
        
        // Filter by search term (name, email, or transportation)
        if (currentFilters.search) {
            const searchTerm = currentFilters.search.toLowerCase();
            const nameMatch = runner.user_name.toLowerCase().includes(searchTerm);
            const emailMatch = runner.user_email.toLowerCase().includes(searchTerm);
            const transportMatch = runner.transportation_method.toLowerCase().includes(searchTerm);
            
            if (!nameMatch && !emailMatch && !transportMatch) {
                return false;
            }
        }
        
        return true;
    });
}

// Setup event listeners
function setupEventListeners() {
    // Filter buttons
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        currentFilters.status = $(this).data('filter');
        displayRunnerApplications(allRunners);
    });
    
    // Transportation filter buttons
    $('.transport-filter').on('click', function() {
        $('.transport-filter').removeClass('active');
        $(this).addClass('active');
        
        currentFilters.transport = $(this).data('transport');
        displayRunnerApplications(allRunners);
    });
    
    // Search input
    $('#search-runner').on('input', function() {
        currentFilters.search = $(this).val();
        displayRunnerApplications(allRunners);
    });
    
    // Navigation between sections
    $('#runner-applications-link').on('click', function(e) {
        e.preventDefault();
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        // Show runner applications section
        // In a real app, you might hide other sections here
    });
    
    // Modal action buttons
    $('#approve-runner-btn').on('click', function() {
        const runnerId = $(this).data('runner-id');
        updateRunnerStatus(runnerId, 'approved');
    });
    
    $('#reject-runner-btn').on('click', function() {
        const runnerId = $(this).data('runner-id');
        updateRunnerStatus(runnerId, 'rejected');
    });
}

// Activate event listeners for cards
function activateCardEventListeners() {
    // View application details
    $('.view-application').on('click', function() {
        const runnerId = $(this).data('runner-id');
        openRunnerDetailsModal(runnerId);
    });
    
    // Quick approve
    $('.quick-approve').on('click', function() {
        const runnerId = $(this).data('runner-id');
        confirmUpdateStatus(runnerId, 'approved');
    });
    
    // Quick reject
    $('.quick-reject').on('click', function() {
        const runnerId = $(this).data('runner-id');
        confirmUpdateStatus(runnerId, 'rejected');
    });
}

// Open runner details modal
function openRunnerDetailsModal(runnerId) {
    // Find runner data
    const runner = allRunners.find(r => r.runner_id == runnerId);
    
    if (!runner) {
        console.error('Runner not found:', runnerId);
        return;
    }
    
    // Set modal data
    $('#modal-runner-name').text(runner.user_name);
    $('#modal-runner-email').text(runner.user_email);
    $('#modal-runner-phone').text(runner.user_phone);
    $('#modal-runner-date').text(new Date(runner.created_at).toLocaleDateString());
    
    // Set runner profile image with proper path and fallback - Using BASE_URL
    const profilePhoto = runner.profile_photo ? 
        BASE_URL + runner.profile_photo : 
        BASE_URL + 'assests/image/uploads/profile_pictures/default-profile.jpg';
    $('#modal-runner-profile').attr('src', profilePhoto)
        .on('error', function() {
            $(this).attr('src', BASE_URL + 'assests/image/uploads/profile_pictures/default-profile.jpg');
        });
    
    // Set status badge
    let statusClass = '';
    switch (runner.status) {
        case 'pending': statusClass = 'status-pending'; break;
        case 'approved': statusClass = 'status-approved'; break;
        case 'rejected': statusClass = 'status-rejected'; break;
        default: statusClass = '';
    }
    
    $('#modal-runner-status')
        .text(runner.status.toUpperCase())
        .removeClass('status-pending status-approved status-rejected')
        .addClass(statusClass);
    
    // Set document photos with proper paths - Using BASE_URL
    if (runner.id_photo) {
        $('#modal-id-photo').attr('src', BASE_URL + runner.id_photo)
            .on('error', function() {
                $(this).attr('src', BASE_URL + 'assests/image/placeholder-id.jpg');
            });
    }
    
    if (runner.selfie_photo) {
        $('#modal-selfie-photo').attr('src', BASE_URL + runner.selfie_photo)
            .on('error', function() {
                $(this).attr('src', BASE_URL + 'assests/image/placeholder-selfie.jpg');
            });
    }
    
    // Set transportation method
    $('#modal-transportation-method').text(capitalize(runner.transportation_method));
    
    // Hide all transportation detail sections initially
    $('#vehicle-details-section, #walking-details-section, #transit-details-section').hide();
    
    // Show appropriate transportation details based on method
    if (runner.transportation_method === 'vehicle') {
        $('#vehicle-details-section').show();
        $('#modal-vehicle-type').text(runner.vehicle_type || 'Not specified');
        $('#modal-registration-number').text(runner.registration_number || 'Not specified');
        $('#modal-license-number').text(runner.license_number || 'Not specified');
        $('#modal-vehicle-phone').text(runner.vehicle_phone || 'Not specified');
        
        // Set vehicle photo with proper path - Using BASE_URL
        if (runner.vehicle_photo) {
            $('#modal-vehicle-photo').attr('src', BASE_URL + runner.vehicle_photo)
                .on('error', function() {
                    $(this).attr('src', BASE_URL + 'assests/image/placeholder-vehicle.jpg');
                });
        }
    } else if (runner.transportation_method === 'walking') {
        $('#walking-details-section').show();
        $('#modal-service-radius').text(runner.service_radius || 'Not specified');
    } else if (runner.transportation_method === 'commute') {
        $('#transit-details-section').show();
        $('#modal-transit-type').text(runner.transit_type || 'Not specified');
        $('#modal-transit-radius').text(runner.transit_radius || 'Not specified');
    }
    
    // Populate services list
    const servicesList = $('#modal-services-list');
    servicesList.empty();
    
    if (runner.services && runner.services.length > 0) {
        // Group services by category
        const servicesByCategory = {};
        runner.services.forEach(service => {
            if (!servicesByCategory[service.category_name]) {
                servicesByCategory[service.category_name] = [];
            }
            servicesByCategory[service.category_name].push(service.subcategory_name);
        });
        
        // Create service list
        for (const category in servicesByCategory) {
            const categoryHtml = `
                <div class="mb-3">
                    <h6>${category}</h6>
                    <ul class="list-group">
                        ${servicesByCategory[category].map(subcategory => 
                            `<li class="list-group-item">${subcategory}</li>`
                        ).join('')}
                    </ul>
                </div>
            `;
            servicesList.append(categoryHtml);
        }
    } else {
        servicesList.html('<p class="text-muted">No services specified</p>');
    }
    
    // Set action buttons data
    $('#approve-runner-btn, #reject-runner-btn').data('runner-id', runner.runner_id);
    
    // Show/hide approve/reject buttons based on current status
    if (runner.status === 'pending') {
        $('#approve-runner-btn, #reject-runner-btn').show();
    } else {
        $('#approve-runner-btn, #reject-runner-btn').hide();
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('runnerDetailsModal'));
    modal.show();
}

// Confirm status update with SweetAlert
function confirmUpdateStatus(runnerId, status) {
    const action = status === 'approved' ? 'approve' : 'reject';
    const icon = status === 'approved' ? 'success' : 'warning';
    const confirmButtonColor = status === 'approved' ? '#28a745' : '#dc3545';
    const confirmButtonText = status === 'approved' ? 'Yes, approve it!' : 'Yes, reject it!';
    
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${action} this runner application?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            updateRunnerStatus(runnerId, status);
        }
    });
}

// Update runner status (approve/reject)
function updateRunnerStatus(runnerId, status) {
    // Prepare status notes
    const statusNotes = `${status.charAt(0).toUpperCase() + status.slice(1)} by admin on ${new Date().toLocaleDateString()}`;
    
    // Show loading state
    Swal.fire({
        title: 'Processing...',
        html: `Updating runner application status to ${status}...`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request to update status
    $.ajax({
        url: '../../database/db_owner/fetch_application.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            runner_id: runnerId,
            status: status,
            notes: statusNotes
        }),
        success: function(response) {
            if (response.success) {
                // Show success message with SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Close modal if open
                    const modalElement = document.getElementById('runnerDetailsModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Refresh runner applications
                    fetchRunnerApplications();
                });
            } else {
                // Show error message with SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to update status',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr, status, error) {
            // Show network error with SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to update runner status. Please try again.',
                confirmButtonColor: '#dc3545'
            });
            console.error('AJAX Error:', error);
        }
    });
}

// Helper function to capitalize first letter
function capitalize(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}