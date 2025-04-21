// Global variables with better organization
const state = {
    map: null,
    marker: null,
    watchId: null,
    currentPosition: null,
    lastUpdated: null,
    isAvailable: false,
    currentAddress: null, // String representation of address
    addressParts: null, // Structured address data for the server
    timers: {
      locationUpdate: null,
      locationUpdateTimer: null
    }
  };
  
  // DOM elements - using a more organized approach
  const elements = {
    // Modals and buttons
    locationModal: new bootstrap.Modal(document.getElementById('locationModal')),
    allowLocationBtn: document.getElementById('allowLocationBtn'),
    availabilityToggle: document.getElementById('availabilityToggle'),
    refreshRequestsBtn: document.getElementById('refreshRequestsBtn'),
    
    // Status indicators
    statusIndicator: document.getElementById('status-indicator'),
    statusText: document.getElementById('status-text'),
    
    // Location elements
    locationBanner: document.getElementById('location-banner'),
    locationStatus: document.getElementById('location-status'),
    locationUpdateTime: document.getElementById('location-update-time'),
    locationSpinner: document.getElementById('location-spinner'),
    
    // Profile elements
    profileImage: document.getElementById('profile-image'),
    runnerName: document.getElementById('runner-name'),
    runnerEmail: document.getElementById('runner-email'),
    currentDateTime: document.getElementById('current-date-time'),
    transportationMethod: document.getElementById('transportation-method'),
    serviceCategories: document.getElementById('service-categories'),
    runnerStatus: document.getElementById('runner-status')
  };
  
  // Initialize app when DOM is loaded
  document.addEventListener('DOMContentLoaded', initializeApp);
  
  function initializeApp() {
    // Initialize custom CSS for animations
    initCustomCSS();
    
    // Fetch user data from session
    fetchUserData();
    
    // Show location permission modal
    elements.locationModal.show();
    
    // Update current date and time
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Set up event listeners
    setupEventListeners();
  }
  
  function setupEventListeners() {
    // Location permission button
    elements.allowLocationBtn.addEventListener('click', requestLocationPermission);
    
    // Availability toggle
    elements.availabilityToggle.addEventListener('click', toggleAvailability);
    
    // Refresh requests button
    elements.refreshRequestsBtn.addEventListener('click', refreshRequests);
  }
  
  function fetchUserData() {
    // API call to get user data
    fetch('../../database/api/get_runner_info.php')
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          updateUserInterface(data);
        } else {
          console.error('Error fetching user data:', data.message);
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        // Fallback to sample data for development/testing
        useSampleUserData();
      });
  }
  
  function updateUserInterface(userData) {
    // Update profile information
    elements.runnerName.textContent = `Welcome, ${userData.name}`;
    elements.runnerEmail.textContent = userData.email;
    
    // Update profile picture if available
    if (userData.profile_pic) {
      elements.profileImage.src = userData.profile_pic;
    }
    
    // Update transportation method
    elements.transportationMethod.textContent = userData.transportation_method || "Not specified";
    
    // Update service categories
    if (userData.service_categories && userData.service_categories.length > 0) {
      elements.serviceCategories.innerHTML = userData.service_categories
        .map(category => `<span class="badge bg-primary mb-1 me-1">${category}</span>`)
        .join('');
    } else {
      elements.serviceCategories.innerHTML = '<span class="badge bg-light text-dark mb-1">No services selected</span>';
    }
    
    // Update runner status
    elements.runnerStatus.textContent = userData.runner_status || "Runner";
    
    // Set availability state if runner is verified
    if (userData.role_id === 2) {
      elements.availabilityToggle.disabled = false;
      
      if (userData.is_available) {
        setAvailabilityState(true, false); // Set available without starting location updates yet
      }
    }
    
    // If location data exists, initialize map
    if (userData.location) {
      state.currentPosition = {
        lat: parseFloat(userData.location.latitude),
        lng: parseFloat(userData.location.longitude)
      };
      
      state.lastUpdated = new Date(userData.location.timestamp);
      
      // Store the address if available
      if (userData.location.address) {
        state.currentAddress = userData.location.address;
      }
      
      // Store address parts if available
      if (userData.location.address_parts) {
        state.addressParts = userData.location.address_parts;
      }
      
      updateLocationBanner(true);
      updateLocationUpdateTime();
      initMap();
      
      // Show address
      elements.locationStatus.textContent = "Your location: " + (state.currentAddress || "Address not available");
    }
  }
  
  function useSampleUserData() {
    // Sample data for development/testing
    elements.runnerName.textContent = "Welcome, John Doe";
    elements.runnerEmail.textContent = "john.doe@example.com";
    elements.transportationMethod.textContent = "Motorcycle";
    elements.serviceCategories.innerHTML = `
      <span class="badge bg-primary mb-1 me-1">Grocery Shopping</span>
      <span class="badge bg-primary mb-1 me-1">Food Delivery</span>
      <span class="badge bg-primary mb-1 me-1">Package Pickup</span>
    `;
    elements.runnerStatus.textContent = "Verified Runner";
    elements.availabilityToggle.disabled = false;
  }
  
  // Updated function to check for existing location - now uses get_location.php
  function checkForExistingLocation() {
    return fetch('../../database/api/get_location.php')
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success && data.location) {
          // Store existing position
          state.currentPosition = {
            lat: parseFloat(data.location.latitude),
            lng: parseFloat(data.location.longitude)
          };
          state.lastUpdated = new Date(data.location.timestamp);
          
          // Store address if available
          if (data.location.address) {
            state.currentAddress = data.location.address;
          }
          
          // Store address parts if available
          if (data.location.address_parts) {
            state.addressParts = data.location.address_parts;
          }
          
          // Update UI
          updateLocationBanner(true);
          updateLocationUpdateTime();
          initMap();
          
          // Show address
          if (data.location.address) {
            elements.locationStatus.textContent = "Your location: " + data.location.address;
          }
          
          // Hide modal
          elements.locationModal.hide();
          
          return true; // Location exists
        }
        return false; // No existing location
      })
      .catch(error => {
        console.error('Error checking for existing location:', error);
        return false; // Treat as no existing location
      });
  }
  
  function requestLocationPermission() {
    if (!navigator.geolocation) {
      showError("Geolocation is not supported by this browser.");
      return;
    }
    
    // First check if we already have location data
    checkForExistingLocation().then(hasExistingLocation => {
      if (!hasExistingLocation) {
        // If no existing location, request a new one
        navigator.geolocation.getCurrentPosition(
          // Success handler
          position => {
            // Hide modal
            elements.locationModal.hide();
            
            // Enable availability toggle
            elements.availabilityToggle.disabled = false;
            
            // Store position
            state.currentPosition = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            
            // Update UI
            updateLocationBanner(true);
            initMap();
            
            // Get address from coordinates and send location to server
            reverseGeocode(state.currentPosition, true);
          },
          // Error handler
          handleLocationError,
          // Options
          {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
          }
        );
      } else {
        // If we already have location data, enable the toggle
        elements.availabilityToggle.disabled = false;
      }
    });
  }
  
  function handleLocationError(error) {
    let message = "Unable to access your location.";
    
    switch(error.code) {
      case error.PERMISSION_DENIED:
        message = "Location access was denied. Please enable location services to use the runner features.";
        elements.locationBanner.classList.add('error');
        break;
      case error.POSITION_UNAVAILABLE:
        message = "Location information is unavailable. Please try again later.";
        break;
      case error.TIMEOUT:
        message = "The request to get location timed out. Please try again.";
        // Auto-retry for timeout errors
        if (state.isAvailable) {
          setTimeout(() => {
            requestLocationUpdate();
          }, 10000);
        }
        break;
      case error.UNKNOWN_ERROR:
        message = "An unknown error occurred. Please try again later.";
        break;
    }
    
    console.error("Geolocation error:", error.code, message);
    
    // Update UI
    elements.locationStatus.textContent = "Location error: " + message;
    elements.locationSpinner.classList.add('d-none');
    
    // Show alert only for critical errors
    if (error.code === error.PERMISSION_DENIED) {
      alert(message);
    }
  }
  
  function initMap() {
    const mapContainer = document.getElementById('map');
    
    if (!state.currentPosition) {
        mapContainer.innerHTML = `
            <div class="d-flex justify-content-center align-items-center h-100 bg-light">
                <div class="text-center">
                    <i class="bi bi-geo-alt-fill text-secondary" style="font-size: 3rem;"></i>
                    <p class="mt-2">Location not available</p>
                </div>
            </div>
        `;
        return;
    }
    
    // Use Google Maps API
    state.map = new google.maps.Map(document.getElementById('map'), {
      center: state.currentPosition,
      zoom: 15
    });
    
    state.marker = new google.maps.Marker({
      position: state.currentPosition,
      map: state.map,
      title: 'Your Location'
    });
  }
  
  function reverseGeocode(position, sendToServer = false) {
    // Show spinner
    elements.locationSpinner.classList.remove('d-none');
    elements.locationStatus.textContent = "Fetching address...";
    
    // Use Google's Geocoding API directly
    const geocoder = new google.maps.Geocoder();
    
    geocoder.geocode({ location: position }, (results, status) => {
        if (status === 'OK' && results[0]) {
            // Process the address components
            const addressComponents = results[0].address_components;
            const formattedAddress = results[0].formatted_address;
            
            // Extract address parts with improved structure to match our normalized DB schema
            const addressParts = {
                street_number: '',
                street_name: '',
                barangay: '',
                city: '',
                province: '',
                postal_code: '',
                formatted_address: formattedAddress  // We'll still send this for display purposes
            };
            
            // Map Google's address components to our structure
            addressComponents.forEach(component => {
                const types = component.types;
                
                if (types.includes('street_number')) {
                    addressParts.street_number = component.long_name;
                } else if (types.includes('route')) {
                    addressParts.street_name = component.long_name;
                } else if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
                    addressParts.barangay = component.long_name;
                } else if (types.includes('locality')) {
                    addressParts.city = component.long_name;
                } else if (types.includes('administrative_area_level_1')) {
                    addressParts.province = component.long_name;
                } else if (types.includes('postal_code')) {
                    addressParts.postal_code = component.long_name;
                } 
                // Note: We don't need to store country in our DB, but we can log it if needed
            });
            
            // Store the address data
            state.currentAddress = formattedAddress;
            state.addressParts = addressParts;
            
            // Log the parsed address for debugging
            console.log('Parsed address:', addressParts);
            
            // Update the UI
            elements.locationStatus.textContent = "Your location: " + formattedAddress;
            
            // Send to server if requested
            if (sendToServer) {
                sendLocationToServer(position, state.isAvailable);
            }
        } else {
            console.error('Geocoder failed due to: ' + status);
            elements.locationStatus.textContent = `Your location: Lat ${position.lat.toFixed(4)}, Lng ${position.lng.toFixed(4)}`;
            
            // Reset address parts to ensure clean data
            state.addressParts = null;
            state.currentAddress = null;
            
            // Still send location to server even if we couldn't get the address
            if (sendToServer) {
                sendLocationToServer(position, state.isAvailable);
            }
        }
        
        // Hide spinner
        elements.locationSpinner.classList.add('d-none');
    });
}
  
  function toggleAvailability(e) {
    const isAvailable = e.target.checked;
    setAvailabilityState(isAvailable, true);
  }
  
  function setAvailabilityState(isAvailable, updateServer) {
    state.isAvailable = isAvailable;
    
    if (isAvailable) {
      // Set UI to online
      elements.statusIndicator.classList.remove('status-offline');
      elements.statusIndicator.classList.add('status-online');
      elements.statusText.textContent = 'Online';
      
      // Start location updates
      startLocationUpdates();
      
      // Add timeline entry
      addTimelineItem("You marked yourself as Available/On Duty");
      
      // Check for pending requests
      checkForRequests();
    } else {
      // Set UI to offline
      elements.statusIndicator.classList.remove('status-online');
      elements.statusIndicator.classList.add('status-offline');
      elements.statusText.textContent = 'Offline';
      
      // Stop location updates
      stopLocationUpdates();
      
      // Add timeline entry
      addTimelineItem("You marked yourself as Offline");
    }
    
    // Update status in database if requested - this uses the dedicated endpoint
    if (updateServer) {
      updateRunnerStatus(isAvailable);
    }
  }
  
  function startLocationUpdates() {
    // Stop any existing updates first
    stopLocationUpdates();
    
    // Show active tracking indicator
    elements.locationBanner.classList.add('active');
    
    // Start watching position
    if (navigator.geolocation) {
      // Watch for position changes
      state.watchId = navigator.geolocation.watchPosition(
        updatePosition,
        handleLocationError,
        { 
          maximumAge: 30000,  // 30 seconds
          timeout: 10000,     // 10 seconds
          enableHighAccuracy: true 
        }
      );
      
      // Set interval for server updates
      state.timers.locationUpdate = setInterval(() => {
        if (state.currentPosition) {
          sendLocationToServer(state.currentPosition, state.isAvailable);
        }
      }, 60000); // Every minute
      
      // Start timer for "last updated" text
      startLocationUpdateTimer();
      
      // Get immediate position update
      requestLocationUpdate();
    }
  }
  
  function requestLocationUpdate() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        updatePosition,
        error => console.warn("Could not get immediate position:", error),
        { maximumAge: 0, timeout: 5000, enableHighAccuracy: true }
      );
    }
  }
  
  function stopLocationUpdates() {
    // Clear watch
    if (state.watchId) {
      navigator.geolocation.clearWatch(state.watchId);
      state.watchId = null;
    }
    
    // Clear timers
    if (state.timers.locationUpdate) {
      clearInterval(state.timers.locationUpdate);
      state.timers.locationUpdate = null;
    }
    
    if (state.timers.locationUpdateTimer) {
      clearInterval(state.timers.locationUpdateTimer);
      state.timers.locationUpdateTimer = null;
      elements.locationUpdateTime.textContent = '';
    }
    
    // Hide spinner
    elements.locationSpinner.classList.add('d-none');
  }
  
  function updatePosition(position) {
    // Store new position
    state.currentPosition = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };
    
    // Update map
    initMap();
    
    // Update last updated time
    state.lastUpdated = new Date();
    elements.locationUpdateTime.textContent = 'Updated just now';
    
    // Show update indicator
    elements.locationSpinner.classList.remove('d-none');
    setTimeout(() => {
      elements.locationSpinner.classList.add('d-none');
    }, 1000);
    
    // Update location banner
    updateLocationBanner(true);
    
    // Get address and send to server
    reverseGeocode(state.currentPosition, true);
  }
  

// In your sendLocationToServer function in the JS file:
function sendLocationToServer(position, isAvailable) {
    if (!position) return;
    
    // Show spinner
    elements.locationSpinner.classList.remove('d-none');
    
    // Prepare data for server
    const locationData = {
        latitude: position.lat,
        longitude: position.lng,
        timestamp: new Date().toISOString(),
        is_available: isAvailable ? 1 : 0
    };
    
    // Include address data properly formatted to match PHP expectations
    if (state.addressParts) {
        // Direct mapping to match what PHP expects (normalized structure)
        locationData.address = {
            street_number: state.addressParts.street_number || '',
            street_name: state.addressParts.street_name || '',
            barangay: state.addressParts.barangay || '',
            city: state.addressParts.city || '',
            province: state.addressParts.province || '',
            postal_code: state.addressParts.postal_code || '',
            // Remove country field
            formatted_address: state.addressParts.formatted_address || state.currentAddress || ''
        };    
    } else if (state.currentAddress) {
        locationData.address_string = state.currentAddress;
    }
    
    // Log what we're sending to server
    console.log('Sending location data:', JSON.stringify(locationData));
    
    // Send data to server
    fetch('../../database/api/runner_location.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(locationData),
        credentials: 'include' // Ensure cookies (for session) are sent
    })
    .then(response => {
        if (!response.ok) {
            // Try to get more details about the error
            return response.text().then(text => {
                console.error('Server response text:', text);
                try {
                    // Try to parse as JSON
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || 'Network response error');
                } catch (e) {
                    // If can't parse as JSON, use text
                    throw new Error('Network response error: ' + text);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Location update response:', data);
        if (data.success) {
            // Update time
            state.lastUpdated = new Date();
            elements.locationUpdateTime.textContent = 'Updated just now';
            
            // Update address if returned by server
            if (data.address) {
                state.currentAddress = data.address;
                elements.locationStatus.textContent = "Your location: " + data.address;
            }
            
            // Store address parts if returned by server
            if (data.address_parts) {
                state.addressParts = data.address_parts;
            }
            
            // Clear any previous error state
            elements.locationBanner.classList.remove('error');
            
            // Add timeline entry for successful update
            addTimelineItem("Location updated successfully");
        } else {
            // Handle specific error codes if present
            let errorMessage = data.message || 'Unknown error updating location';
            
            // Add specific handling for auth errors
            if (data.error_code === 'AUTH_ERROR') {
                // Redirect to login or show login modal
                showError("Your session has expired. Please log in again.");
                setTimeout(() => {
                    window.location.href = '../../login.php'; // Redirect to login page
                }, 2000);
            }
            
            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error updating location:', error);
        
        // Add timeline entry
        addTimelineItem("Failed to update location: " + error.message);
        
        // Show error banner
        elements.locationStatus.textContent = "Error: " + error.message;
        elements.locationBanner.classList.add('error');
        
        // Provide retry option for network errors
        if (error.message.includes('Network') || error.message.includes('Failed to fetch')) {
            setTimeout(() => {
                if (state.isAvailable && state.currentPosition) {
                    console.log('Retrying location update...');
                    sendLocationToServer(state.currentPosition, state.isAvailable);
                }
            }, 10000); // Retry after 10 seconds
        }
    })
    .finally(() => {
        // Hide spinner after a short delay
        setTimeout(() => {
            elements.locationSpinner.classList.add('d-none');
        }, 500);
    });
}

  function updateRunnerStatus(isAvailable) {
    fetch('../../database/api/runner_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        is_available: isAvailable ? 1 : 0
      })
    })
    .then(response => {
      if (!response.ok) throw new Error('Network response error');
      return response.json();
    })
    .then(data => {
      if (!data.success) {
        throw new Error(data.message || 'Unknown error updating status');
      }
    })
    .catch(error => {
      console.error('Error updating runner status:', error);
      // Add error handling UI feedback
      addTimelineItem("Failed to update availability status");
      // Revert toggle if necessary - this ensures UI matches server state
      if (error && elements.availabilityToggle) {
        elements.availabilityToggle.checked = !isAvailable;
      }
    });
  }
  
  function startLocationUpdateTimer() {
    // Update time display every minute
    state.timers.locationUpdateTimer = setInterval(() => {
      updateLocationUpdateTime();
    }, 60000);
  }
  
  function updateLocationUpdateTime() {
    if (!state.lastUpdated) return;
    
    const now = new Date();
    const diff = Math.floor((now - state.lastUpdated) / 60000); // minutes
    
    if (diff < 1) {
      elements.locationUpdateTime.textContent = 'Updated just now';
    } else if (diff === 1) {
      elements.locationUpdateTime.textContent = 'Updated 1 minute ago';
    } else {
      elements.locationUpdateTime.textContent = `Updated ${diff} minutes ago`;
    }
  }
  
  function updateLocationBanner(isActive) {
    if (isActive) {
      elements.locationBanner.classList.add('active');
    } else {
      elements.locationBanner.classList.remove('active');
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
    elements.currentDateTime.textContent = now.toLocaleDateString('en-US', options);
  }
  
  function addTimelineItem(text) {
    const timeline = document.querySelector('.timeline');
    if (!timeline) return;
    
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
    
    // Add to top of timeline
    timeline.insertBefore(timelineItem, timeline.firstChild);
  }
  
  function checkForRequests() {
    // In a real implementation, you would check for requests from the server
    fetch('../../database/api/get_requests.php')
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          displayRequests(data.requests);
        } else {
          throw new Error(data.message || 'Unknown error getting requests');
        }
      })
      .catch(error => {
        console.error('Error checking for requests:', error);
        
        // For demo purposes, show sample request after delay
        if (state.isAvailable) {
          setTimeout(() => {
            const noRequestsMessage = document.getElementById('no-requests-message');
            const sampleRequest = document.getElementById('sample-request');
            
            if (noRequestsMessage && sampleRequest) {
              noRequestsMessage.classList.add('d-none');
              sampleRequest.classList.remove('d-none');
              addTimelineItem("New request received: Grocery Shopping");
            }
          }, 3000);
        }
      });
  }
  
  function refreshRequests() {
    // Show spinner on button
    const refreshIcon = elements.refreshRequestsBtn.querySelector('i');
    refreshIcon.classList.remove('bi-arrow-clockwise');
    refreshIcon.classList.add('bi-arrow-repeat');
    elements.refreshRequestsBtn.disabled = true;
    
    // In real implementation, you would fetch from server
    fetch('../../database/api/get_requests.php')
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          displayRequests(data.requests);
        } else {
          throw new Error(data.message || 'Unknown error refreshing requests');
        }
      })
      .catch(error => {
        console.error('Error refreshing requests:', error);
        
        // For demo, just delay and show sample
        setTimeout(() => {
          if (state.isAvailable) {
            checkForRequests();
          }
        }, 1500);
      })
      .finally(() => {
        // Reset button
        refreshIcon.classList.remove('bi-arrow-repeat');
        refreshIcon.classList.add('bi-arrow-clockwise');
        elements.refreshRequestsBtn.disabled = false;
        
        // Add timeline entry
        addTimelineItem("You refreshed requests");
      });
  }
  
  function displayRequests(requests) {
    const requestsContainer = document.getElementById('requests-container');
    const noRequestsMessage = document.getElementById('no-requests-message');
    
    if (!requestsContainer || !noRequestsMessage) return;
    
    if (requests && requests.length > 0) {
      // Hide no requests message
      noRequestsMessage.classList.add('d-none');
      
      // Remove existing requests except sample
      const existingRequests = requestsContainer.querySelectorAll('.request-card:not(#sample-request)');
      existingRequests.forEach(request => request.remove());
      
      // Add new requests
      requests.forEach(request => {
        const requestCard = createRequestCard(request);
        requestsContainer.appendChild(requestCard);
      });
    } else {
      // Show no requests message
      noRequestsMessage.classList.remove('d-none');
      
      // Hide sample request
      const sampleRequest = document.getElementById('sample-request');
      if (sampleRequest) {
        sampleRequest.classList.add('d-none');
      }
    }
  }
  
  function createRequestCard(request) {
    const card = document.createElement('div');
    card.classList.add('card', 'request-card', 'mb-3');
    
    // Set icon based on request type
    let icon = 'cart3'; // Default icon
    if (request.type.includes('Food')) icon = 'cup-hot';
    if (request.type.includes('Package')) icon = 'box';
    
    card.innerHTML = `
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">
            <i class="bi bi-${request.icon || icon} category-icon"></i>
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
    console.log('View request details:', requestId);
    
    // In real implementation, you would fetch request details
    fetch(`../../database/api/request_details.php?id=${requestId}`)
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Show request details in a modal
          // For now, just use alert
          alert(`Viewing details for request #${requestId}`);
        } else {
          throw new Error(data.message || 'Unknown error getting request details');
        }
      })
      .catch(error => {
        console.error('Error getting request details:', error);
        alert(`Error getting details for request #${requestId}`);
      });
  }
  
  function acceptRequest(requestId) {
    if (!confirm('Are you sure you want to accept this request?')) {
      return;
    }
    
    // In real implementation, send acceptance to server
    fetch('../../database/api/accept_request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ request_id: requestId })
    })
      .then(response => {
        if (!response.ok) throw new Error('Network response error');
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Update UI - for demo, use sample request
          const request = document.getElementById('sample-request') || document.querySelector(`.request-card[data-id="${requestId}"]`);
          
          if (request) {
            const badge = request.querySelector('.badge');
            if (badge) {
              badge.classList.remove('badge-pending');
              badge.classList.add('badge-success');
              badge.textContent = 'Accepted';
            }
            
            // Disable buttons
            const buttons = request.querySelectorAll('button');
            buttons.forEach(button => {
              button.disabled = true;
            });
          }
          
          // Add timeline entry
          addTimelineItem(`You accepted request #${requestId}`);
        } else {
          throw new Error(data.message || 'Unknown error accepting request');
        }
      })
      .catch(error => {
        console.error('Error accepting request:', error);
        alert(`Error accepting request #${requestId}`);
      });
  }
  
  // Helper function for CSS animations
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
  
  // Helper function to show errors
  function showError(message) {
    console.error(message);
    alert(message);
  }
   
  // Optional - Service worker for background updates
  function setupPeriodicLocationUpdates() {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('../js/location-service-worker.js')
        .then(registration => {
          console.log('ServiceWorker registration successful');
        })
        .catch(error => {
          console.error('ServiceWorker registration failed:', error);
        });
    }
  }