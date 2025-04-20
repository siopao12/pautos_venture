
// Global variables
let map;
let marker;
let watchId;
let currentPosition = null;
let lastUpdated = null;
let isAvailable = false;
let locationUpdateInterval;
let locationUpdateTimer;

// DOM elements
const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
const allowLocationBtn = document.getElementById('allowLocationBtn');
const availabilityToggle = document.getElementById('availabilityToggle');
const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');
const locationBanner = document.getElementById('location-banner');
const locationStatus = document.getElementById('location-status');
const locationUpdateTime = document.getElementById('location-update-time');
const locationSpinner = document.getElementById('location-spinner');
const profileImage = document.getElementById('profile-image');
const runnerName = document.getElementById('runner-name');
const runnerEmail = document.getElementById('runner-email');
const currentDateTime = document.getElementById('current-date-time');
const transportationMethod = document.getElementById('transportation-method');
const serviceCategories = document.getElementById('service-categories');
const runnerStatus = document.getElementById('runner-status');
const refreshRequestsBtn = document.getElementById('refreshRequestsBtn');

// Show location modal on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get user data from session
    fetchUserData();
    
    // Show location modal
    locationModal.show();
    
    // Update current date and time
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Set up event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Allow location button click
    allowLocationBtn.addEventListener('click', requestLocationPermission);
    
    // Availability toggle
    availabilityToggle.addEventListener('change', toggleAvailability);
    
    // Refresh requests button
    refreshRequestsBtn.addEventListener('click', refreshRequests);
}

function fetchUserData() {
    // In a real implementation, you would fetch this data from the server
    // For now, we'll use sample data
    
    // Set profile information
    runnerName.textContent = "Welcome, John Doe";
    runnerEmail.textContent = "john.doe@example.com";
    
    // You could load the profile picture if available
    // profileImage.src = "../assests/image/profile_photos/user_123.jpg";
    
    // Set transportation method
    transportationMethod.textContent = "Motorcycle";
    
    // Set service categories
    serviceCategories.innerHTML = `
        <span class="badge bg-primary mb-1 me-1">Grocery Shopping</span>
        <span class="badge bg-primary mb-1 me-1">Food Delivery</span>
        <span class="badge bg-primary mb-1 me-1">Package Pickup</span>
    `;
    
    // Set runner status
    runnerStatus.textContent = "Verified Runner";
}

function requestLocationPermission() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Location permission granted
                locationModal.hide();
                
                // Enable availability toggle
                availabilityToggle.disabled = false;
                
                // Initialize map with current location
                currentPosition = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Update location banner
                updateLocationBanner(true);
                
                // Initialize map
                initMap();
                
                // Get address from coordinates
                reverseGeocode(currentPosition);
            },
            function(error) {
                // Location permission denied
                handleLocationError(error);
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function handleLocationError(error) {
    let message = "Unable to access your location.";
    
    switch(error.code) {
        case error.PERMISSION_DENIED:
            message = "Location access was denied. Please enable location services to use the runner features.";
            break;
        case error.POSITION_UNAVAILABLE:
            message = "Location information is unavailable. Please try again later.";
            break;
        case error.TIMEOUT:
            message = "The request to get location timed out. Please try again.";
            break;
        case error.UNKNOWN_ERROR:
            message = "An unknown error occurred. Please try again later.";
            break;
    }
    
    alert(message);
}

function initMap() {
    // For this example, we'll use a placeholder map
    // In a real implementation, you would initialize a Google Map here
    
    const mapContainer = document.getElementById('map');
    mapContainer.innerHTML = `
        <div class="d-flex justify-content-center align-items-center h-100 bg-light">
            <div class="text-center">
                <i class="bi bi-geo-alt-fill text-primary" style="font-size: 3rem;"></i>
                <p class="mt-2">Your current location</p>
                <p class="text-muted small">Lat: ${currentPosition.lat.toFixed(6)}, Lng: ${currentPosition.lng.toFixed(6)}</p>
            </div>
        </div>
    `;
    
    // In a real implementation with Google Maps:
    /*
    map = new google.maps.Map(document.getElementById('map'), {
        center: currentPosition,
        zoom: 15
    });
    
    marker = new google.maps.Marker({
        position: currentPosition,
        map: map,
        title: 'Your Location'
    });
    */
}

function reverseGeocode(position) {
    // Show a loader indicator if desired
    locationSpinner.classList.remove('d-none');
    
    // Call the new endpoint to get the address
    fetch('../../database/api/get_address.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            latitude: position.lat,
            longitude: position.lng
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the location banner with real address
            locationStatus.textContent = "Your location: " + data.address;
        } else {
            // Fallback to coordinates
            locationStatus.textContent = `Your location: Lat ${position.lat.toFixed(4)}, Lng ${position.lng.toFixed(4)}`;
        }
        // Hide loading spinner
        locationSpinner.classList.add('d-none');
    })
    .catch((error) => {
        console.error('Error fetching address:', error);
        // Fallback to coordinates on error
        locationStatus.textContent = `Your location: Lat ${position.lat.toFixed(4)}, Lng ${position.lng.toFixed(4)}`;
        locationSpinner.classList.add('d-none');
    });
}

function toggleAvailability(e) {
    isAvailable = e.target.checked;
    
    if (isAvailable) {
        // Runner is available
        statusIndicator.classList.remove('status-offline');
        statusIndicator.classList.add('status-online');
        statusText.textContent = 'Online';
        
        // Start location updates
        startLocationUpdates();
        
        // Add to activity timeline
        addTimelineItem("You marked yourself as Available/On Duty");
        
        // Check for requests (in a real implementation)
        checkForRequests();
    } else {
        // Runner is offline
        statusIndicator.classList.remove('status-online');
        statusIndicator.classList.add('status-offline');
        statusText.textContent = 'Offline';
        
        // Stop location updates
        stopLocationUpdates();
        
        // Add to activity timeline
        addTimelineItem("You marked yourself as Offline");
    }
    
    // Update runner status in database (in a real implementation)
    updateRunnerStatus(isAvailable);
}

function startLocationUpdates() {
    // Start watching position
    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            updatePosition,
            handleLocationError,
            { maximumAge: 60000, timeout: 5000, enableHighAccuracy: true }
        );
        
        // Set interval to periodically update server with location
        locationUpdateInterval = setInterval(sendLocationToServer, 180000); // Every 3 minutes
        
        // Start location update timer
        startLocationUpdateTimer();
    }
}

function stopLocationUpdates() {
    // Stop watching position
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
    
    // Clear location update interval
    if (locationUpdateInterval) {
        clearInterval(locationUpdateInterval);
        locationUpdateInterval = null;
    }
    
    // Clear location update timer
    if (locationUpdateTimer) {
        clearInterval(locationUpdateTimer);
        locationUpdateTimer = null;
        locationUpdateTime.textContent = '';
    }
    
    // Hide spinner
    locationSpinner.classList.add('d-none');
}

function updatePosition(position) {
    // Update current position
    currentPosition = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
    };
    
    // Update map (in a real implementation with Google Maps)
    /*
    if (map && marker) {
        marker.setPosition(currentPosition);
        map.setCenter(currentPosition);
    }
    */
    
    // Reset last updated time
    lastUpdated = new Date();
    locationUpdateTime.textContent = 'Updated just now';
    
    // Show spinner briefly
    locationSpinner.classList.remove('d-none');
    setTimeout(() => {
        locationSpinner.classList.add('d-none');
    }, 1000);
    
    // Update location banner
    updateLocationBanner(true);
    
    // For demonstration purposes, update the map content
    initMap();
}

function sendLocationToServer() {
    // In a real implementation, you would send the location to the server
    console.log('Sending location to server:', currentPosition);
    
    // Show spinner briefly
    locationSpinner.classList.remove('d-none');
    setTimeout(() => {
        locationSpinner.classList.add('d-none');
    }, 1000);
    
    // For demonstration purposes, update last updated time
    lastUpdated = new Date();
    locationUpdateTime.textContent = 'Updated just now';
    
    // Example AJAX request:
    /*
    fetch('../api/update_location.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            latitude: currentPosition.lat,
            longitude: currentPosition.lng,
            timestamp: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
    */
}

function updateRunnerStatus(isAvailable) {
    // In a real implementation, you would update the runner status in the database
    console.log('Updating runner status:', isAvailable ? 'Available' : 'Offline');
    
    // Example AJAX request:
    /*
    fetch('../api/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            is_available: isAvailable ? 1 : 0
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Status updated:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
    */
}

function startLocationUpdateTimer() {
    // Update the "last updated" text every minute
    locationUpdateTimer = setInterval(() => {
        if (lastUpdated) {
            const now = new Date();
            const diff = Math.floor((now - lastUpdated) / 60000); // minutes
            
            if (diff < 1) {
                locationUpdateTime.textContent = 'Updated just now';
            } else if (diff === 1) {
                locationUpdateTime.textContent = 'Updated 1 minute ago';
            } else {
                locationUpdateTime.textContent = `Updated ${diff} minutes ago`;
            }
        }
    }, 60000); // Every minute
}

function updateLocationBanner(isActive) {
    if (isActive) {
        locationBanner.classList.add('active');
    } else {
        locationBanner.classList.remove('active');
    }
}

function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    currentDateTime.textContent = now.toLocaleDateString('en-US', options);
}

function addTimelineItem(text) {
    const timeline = document.querySelector('.timeline');
    const timelineItem = document.createElement('div');
    timelineItem.classList.add('timeline-item');
    
    const timeElement = document.createElement('small');
    timeElement.classList.add('text-muted');
    timeElement.textContent = 'Just now';
    
    const textElement = document.createElement('p');
    textElement.classList.add('mb-0');
    textElement.textContent = text;
    
    timelineItem.appendChild(timeElement);
    timelineItem.appendChild(textElement);
    
    // Insert at the beginning of the timeline
    timeline.insertBefore(timelineItem, timeline.firstChild);
}

function checkForRequests() {
    // In a real implementation, you would check for requests from the server
    console.log('Checking for requests...');
    
    // Example: Show sample request after a delay (for demonstration)
    setTimeout(() => {
        const noRequestsMessage = document.getElementById('no-requests-message');
        const sampleRequest = document.getElementById('sample-request');
        
        if (isAvailable) {
            noRequestsMessage.classList.add('d-none');
            sampleRequest.classList.remove('d-none');
            
            // Add to timeline
            addTimelineItem("New request received: Grocery Shopping");
        }
    }, 5000);
}
function refreshRequests() {
    // Show spinner on the refresh button
    const refreshIcon = refreshRequestsBtn.querySelector('i');
    refreshIcon.classList.remove('bi-arrow-clockwise');
    refreshIcon.classList.add('bi-arrow-repeat');
    refreshRequestsBtn.disabled = true;
    
    // In a real implementation, you would fetch requests from the server
    setTimeout(() => {
        // Reset icon
        refreshIcon.classList.remove('bi-arrow-repeat');
        refreshIcon.classList.add('bi-arrow-clockwise');
        refreshRequestsBtn.disabled = false;
        
        // If active, check for requests
        if (isAvailable) {
            checkForRequests();
        }
        
        // Add to timeline
        addTimelineItem("You refreshed requests");
    }, 1500);
    
    // Example AJAX request:
    /*
    fetch('../api/get_requests.php')
    .then(response => response.json())
    .then(data => {
        refreshIcon.classList.remove('bi-arrow-repeat');
        refreshIcon.classList.add('bi-arrow-clockwise');
        
        // Process and display requests
        displayRequests(data);
    })
    .catch((error) => {
        console.error('Error:', error);
        refreshIcon.classList.remove('bi-arrow-repeat');
        refreshIcon.classList.add('bi-arrow-clockwise');
    });
    */
}

function displayRequests(requests) {
    // In a real implementation, you would display the requests from the server
    const requestsContainer = document.getElementById('requests-container');
    const noRequestsMessage = document.getElementById('no-requests-message');
    
    if (requests && requests.length > 0) {
        noRequestsMessage.classList.add('d-none');
        
        // Clear existing requests (except the sample)
        const existingRequests = requestsContainer.querySelectorAll('.request-card:not(#sample-request)');
        existingRequests.forEach(request => request.remove());
        
        // Add new requests
        requests.forEach(request => {
            const requestCard = createRequestCard(request);
            requestsContainer.appendChild(requestCard);
        });
    } else {
        // No requests
        noRequestsMessage.classList.remove('d-none');
        
        // Hide sample request
        const sampleRequest = document.getElementById('sample-request');
        sampleRequest.classList.add('d-none');
    }
}

function createRequestCard(request) {
    // In a real implementation, you would create a request card based on the request data
    const card = document.createElement('div');
    card.classList.add('card', 'request-card', 'mb-3');
    
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">
                    <i class="bi bi-${request.icon || 'cart3'} category-icon"></i>
                    <span class="request-type">${request.type}</span>
                </h5>
                <span class="badge bg-primary badge-pending">${request.status}</span>
            </div>
            <p class="mb-2 request-description">${request.description}</p>
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bi bi-geo-alt"></i> ${request.distance} km away
                </small>
                <small class="text-muted request-time">${request.time}</small>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="viewRequestDetails(${request.id})">Details</button>
                <button class="btn btn-sm btn-primary" onclick="acceptRequest(${request.id})">Accept</button>
            </div>
        </div>
    `;
    
    return card;
}

function viewRequestDetails(requestId) {
    // In a real implementation, you would show request details
    console.log('View request details:', requestId);
    alert('Viewing details for request #' + requestId);
}

function acceptRequest(requestId) {
    // In a real implementation, you would accept the request
    console.log('Accept request:', requestId);
    
    // Show confirmation
    if (confirm('Are you sure you want to accept this request?')) {
        // Example AJAX request:
        /*
        fetch('../api/accept_request.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                request_id: requestId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Request accepted:', data);
            
            // Update UI
            // ...
            
            // Add to timeline
            addTimelineItem("You accepted request #" + requestId);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
        */
        
        // For demonstration purposes
        const sampleRequest = document.getElementById('sample-request');
        sampleRequest.querySelector('.badge').classList.remove('badge-pending');
        sampleRequest.querySelector('.badge').classList.add('badge-success');
        sampleRequest.querySelector('.badge').textContent = 'Accepted';
        
        // Disable buttons
        const buttons = sampleRequest.querySelectorAll('button');
        buttons.forEach(button => {
            button.disabled = true;
        });
        
        // Add to timeline
        addTimelineItem("You accepted a request: Grocery Shopping");
    }
}

// Helper function to create realistic timestamps for the demo
function createTimeAgo(minutes) {
    if (minutes < 1) return 'Just now';
    if (minutes === 1) return '1 minute ago';
    if (minutes < 60) return `${minutes} minutes ago`;
    
    const hours = Math.floor(minutes / 60);
    if (hours === 1) return '1 hour ago';
    if (hours < 24) return `${hours} hours ago`;
    
    const days = Math.floor(hours / 24);
    if (days === 1) return '1 day ago';
    return `${days} days ago`;
}

// Initialize the custom CSS for spinner animation
function initCustomCSS() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spinner-rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .bi-arrow-repeat {
            animation: spinner-rotate 1s linear infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .status-online.pulse {
            animation: pulse 2s infinite;
        }
    `;
    document.head.appendChild(style);
}

// Initialize custom CSS
initCustomCSS();

// Backend Integration Functions (to be implemented)

// Function to handle automatic periodic location updates to server
function setupPeriodicLocationUpdates() {
    // Set up periodic location updates even when the user is not actively using the app
    if ('serviceWorker' in navigator) {
        // Register a service worker for background location updates
        navigator.serviceWorker.register('../js/location-service-worker.js')
        .then(function(registration) {
            console.log('ServiceWorker registration successful');
        })
        .catch(function(error) {
            console.log('ServiceWorker registration failed:', error);
        });
    }
}

// Function to fetch user data from the server
function fetchUserData() {
// Make AJAX request to get runner information
fetch('../../database/api/get_runner_info.php')
.then(response => response.json())
.then(data => {
if (data.success) {
    // Set profile information
    runnerName.textContent = "Welcome, " + data.name;
    runnerEmail.textContent = data.email;
    
    // Set profile picture if available
    if (data.profile_pic) {
        profileImage.src = data.profile_pic;
    }
    
    // Set transportation method
    if (data.transportation_method) {
        transportationMethod.textContent = data.transportation_method;
    } else {
        transportationMethod.textContent = "Not specified";
    }
    
    // Set service categories
    if (data.service_categories && data.service_categories.length > 0) {
        let categoriesHTML = '';
        data.service_categories.forEach(category => {
            categoriesHTML += `<span class="badge bg-primary mb-1 me-1">${category}</span>`;
        });
        serviceCategories.innerHTML = categoriesHTML;
    } else {
        serviceCategories.innerHTML = '<span class="badge bg-light text-dark mb-1">No services selected</span>';
    }
    
    // Set runner status
    if (data.runner_status) {
        runnerStatus.textContent = data.runner_status;
    }
    
    // Check if runner is available and set toggle
    if (data.is_available) {
        availabilityToggle.checked = true;
        statusIndicator.classList.remove('status-offline');
        statusIndicator.classList.add('status-online');
        statusText.textContent = 'Online';
        isAvailable = true;
    }
    
    // Enable availability toggle if user is a verified runner (role_id = 2)
    if (data.role_id === 2) {
        availabilityToggle.disabled = false;
    }
    
    // If location data is available, update the map
    if (data.location) {
        currentPosition = {
            lat: parseFloat(data.location.latitude),
            lng: parseFloat(data.location.longitude)
        };
        
        updateLocationBanner(true);
        initMap();
        
        // Set last updated time
        lastUpdated = new Date(data.location.timestamp);
        updateLocationUpdateTime();
        
        // Show location in human-readable format
        locationStatus.textContent = "Your location: " + data.location.address;
    }
} else {
    console.error('Error fetching user data:', data.message);
}
})
.catch(error => {
console.error('Fetch error:', error);
});
}

// Function to send location to server
function sendLocationToServer() {
// Only send if we have a position
if (!currentPosition) return;

// Show spinner
locationSpinner.classList.remove('d-none');

// Prepare data to send
const locationData = {
latitude: currentPosition.lat,
longitude: currentPosition.lng,
timestamp: new Date().toISOString(),
is_available: isAvailable
};

// Send the data
fetch('../../database/api/runner_location.php', {
method: 'POST',
headers: {
    'Content-Type': 'application/json',
},
body: JSON.stringify(locationData)
})
.then(response => response.json())
.then(data => {
if (data.success) {
    // Update last updated time
    lastUpdated = new Date();
    updateLocationUpdateTime();
    
    // Hide spinner
    setTimeout(() => {
        locationSpinner.classList.add('d-none');
    }, 1000);
} else {
    console.error('Error updating location:', data.message);
    locationSpinner.classList.add('d-none');
}
})
.catch(error => {
console.error('Fetch error:', error);
locationSpinner.classList.add('d-none');
});
}

// Function to update runner availability status
function updateRunnerStatus(isAvailable) {
fetch('../../database/api/runner_status.php', {
method: 'POST',
headers: {
    'Content-Type': 'application/json',
},
body: JSON.stringify({
    is_available: isAvailable ? 1 : 0
})
})
.then(response => response.json())
.then(data => {
if (data.success) {
    console.log('Status updated successfully');
} else {
    console.error('Error updating status:', data.message);
}
})
.catch(error => {
console.error('Fetch error:', error);
});
}

// Function to update the "last updated" time display
function updateLocationUpdateTime() {
if (lastUpdated) {
const now = new Date();
const diff = Math.floor((now - lastUpdated) / 60000); // minutes

if (diff < 1) {
    locationUpdateTime.textContent = 'Updated just now';
} else if (diff === 1) {
    locationUpdateTime.textContent = 'Updated 1 minute ago';
} else {
    locationUpdateTime.textContent = `Updated ${diff} minutes ago`;
}
}
}
