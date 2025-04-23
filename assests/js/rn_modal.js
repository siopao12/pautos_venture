/**
 * Runner Display Script - Handles displaying runners in both modal and dashboard
 */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Runner display script loaded');
  
  // Cache DOM elements
  const elements = {
    // Modal elements
    modal: document.getElementById('onlineRunnersModal'),
    modalSearchInput: document.getElementById('runnerSearchInput'),
    refreshButton: document.getElementById('refreshRunnersBtn'),
    runnersList: document.getElementById('runnersList'),
    loadingIndicator: document.getElementById('runnersLoadingIndicator'),
    noRunnersMessage: document.getElementById('noRunnersMessage'),
    
    // Main page elements
    mainContainer: document.getElementById('mainRunnersContainer'),
    mainLoadingIndicator: document.getElementById('mainLoadingIndicator'),
    mainNoRunnersMessage: document.getElementById('mainNoRunnersMessage'),
    mainSearchInput: document.querySelector('input[placeholder="Search runners..."]'),
    sortSelect: document.querySelector('select[aria-label="Sort runners"]')
  };
  
  // Store runner data
  let allRunners = [];
  let currentFilter = '';
  let currentSortOption = 'recommended';
  
  // Initialize event listeners
  function initEventListeners() {
    // Modal search
    if (elements.modalSearchInput) {
      elements.modalSearchInput.addEventListener('input', function() {
        currentFilter = this.value.toLowerCase();
        displayFilteredRunners();
      });
    }
    
    // Refresh button
    if (elements.refreshButton) {
      elements.refreshButton.addEventListener('click', fetchRunners);
    }
    
    // Main page search
    if (elements.mainSearchInput) {
      elements.mainSearchInput.addEventListener('input', function() {
        currentFilter = this.value.toLowerCase();
        displayFilteredRunners();
      });
    }
    
    // Sort select
    if (elements.sortSelect) {
      elements.sortSelect.addEventListener('change', function() {
        currentSortOption = this.value;
        displayFilteredRunners();
      });
    }
    
    // Modal events
    if (elements.modal) {
      elements.modal.addEventListener('show.bs.modal', fetchRunners);
    }
  }
  
  // Fetch runners from the API
  function fetchRunners() {
    showLoading(true);
    
    // API path - corrected for folder structure
    const apiPath = '../database/api/get_online_runners.php';
    console.log('Fetching runners from:', apiPath);
    
    fetch(apiPath)
      .then(response => {
        if (!response.ok) {
          throw new Error(`Network response error: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          allRunners = data.runners || [];
          console.log(`Loaded ${allRunners.length} runners:`, allRunners);
          displayFilteredRunners();
        } else {
          throw new Error(data.message || 'Failed to fetch runners');
        }
      })
      .catch(error => {
        console.error('Error fetching runners:', error);
        showError(error.message);
      })
      .finally(() => {
        showLoading(false);
      });
  }
  
  // Display filtered and sorted runners
  function displayFilteredRunners() {
    // First filter the runners
    let filteredRunners = filterRunners(allRunners, currentFilter);
    
    // Then sort them
    filteredRunners = sortRunners(filteredRunners, currentSortOption);
    
    // Display the results
    displayRunners(filteredRunners);
  }
  
  // Filter runners based on search text
  function filterRunners(runners, searchText) {
    if (!searchText || searchText === '') {
      return runners;
    }
    
    return runners.filter(runner => {
      // Search by name
      if (runner.name?.toLowerCase().includes(searchText)) {
        return true;
      }
      
      // Search by transportation method
      if (runner.transportation_method?.toLowerCase().includes(searchText)) {
        return true;
      }
      
      // Search by vehicle type
      if (runner.vehicle_type?.toLowerCase().includes(searchText)) {
        return true;
      }
      
      // Search by services
      if (runner.services_array?.some(service => 
        service.toLowerCase().includes(searchText))) {
        return true;
      }
      
      // Search by location
      if (runner.formatted_address?.toLowerCase().includes(searchText)) {
        return true;
      }
      
      return false;
    });
  }
  
  // Sort runners based on option
  function sortRunners(runners, sortOption) {
    let sortedRunners = [...runners];
    
    switch (sortOption) {
      case 'rating':
        // Placeholder for future rating implementation
        // For now, just keep the default order (by distance)
        sortedRunners.sort((a, b) => {
          if (!a.distance && !b.distance) return 0;
          if (!a.distance) return 1;
          if (!b.distance) return -1;
          return a.distance - b.distance;
        });
        break;
        
      case 'price':
        // Sort by estimated price (based on distance)
        sortedRunners.sort((a, b) => {
          if (!a.distance && !b.distance) return 0;
          if (!a.distance) return 1;
          if (!b.distance) return -1;
          return a.distance - b.distance; // Lower distance = lower price
        });
        break;
        
      case 'recommended':
      default:
        // Sort by distance
        sortedRunners.sort((a, b) => {
          if (!a.distance && !b.distance) return 0;
          if (!a.distance) return 1;
          if (!b.distance) return -1;
          return a.distance - b.distance;
        });
        break;
    }
    
    return sortedRunners;
  }
  
  // Display runners in the appropriate container
  function displayRunners(runners) {
    // Clear existing runner cards in both containers
    clearRunnerContainers();
    
    if (!runners || runners.length === 0) {
      showNoRunnersMessage(true);
      return;
    }
    
    showNoRunnersMessage(false);
    
    // Create runner cards and add them to the containers
    runners.forEach(runner => {
      // Add to modal if available
      if (elements.runnersList) {
        const runnerCard = createRunnerCard(runner, 'modal');
        elements.runnersList.appendChild(runnerCard);
      }
      
      // Add to main container if available
      if (elements.mainContainer) {
        const runnerCard = createRunnerCard(runner, 'main');
        elements.mainContainer.appendChild(runnerCard);
      }
    });
  }
  
  // Calculate estimated price based on distance
  function calculateEstimatedPrice(distance, transportMethod, vehicleType) {
    // Base price
    let basePrice = 50; // ₱50 minimum
    
    if (!distance) {
      return {
        price: `₱${basePrice}+`,
        timeRange: "20-30 minutes"
      };
    }
    
    // Calculate price based on distance
    let pricePerKm;
    
    // Use vehicle_type if available, otherwise fall back to transportation_method
    const transportType = vehicleType ? vehicleType.toLowerCase() : 
                         (transportMethod ? transportMethod.toLowerCase() : 'default');
    
    // Different rates based on transportation method
    switch(transportType) {
      case 'motorcycle':
        pricePerKm = 15;
        break;
      case 'e-bike':
        pricePerKm = 12;
        break;
      case 'bicycle':
        pricePerKm = 10;
        break;
      case 'car':
        pricePerKm = 20;
        break;
      case 'van':
        pricePerKm = 25;
        break;
      case 'walking':
        pricePerKm = 8;
        break;
      case 'vehicle':
        pricePerKm = 18; // Generic vehicle rate
        break;
      default:
        pricePerKm = 15; // Default rate
    }
    
    // Calculate total price
    let totalPrice = basePrice + (distance * pricePerKm);
    
    // Round to nearest 5 pesos
    totalPrice = Math.ceil(totalPrice / 5) * 5;
    
    // Estimate delivery time (rough calculation)
    let estimatedTime;
    if (distance < 2) {
      estimatedTime = "15-25";
    } else if (distance < 5) {
      estimatedTime = "20-30";
    } else if (distance < 10) {
      estimatedTime = "30-45";
    } else {
      estimatedTime = "45-60";
    }
    
    return {
      price: `₱${totalPrice}`,
      timeRange: `${estimatedTime} minutes`
    };
  }
  
  // Create a runner card
  function createRunnerCard(runner, containerType) {
    const col = document.createElement('div');
    
    // Adjust column size based on container
    if (containerType === 'modal') {
      col.classList.add('col-md-6');
    } else {
      col.classList.add('col-md-6', 'col-lg-4');
    }
    
    // Default profile image if none available
    const profileImg = runner.profile_pic || '../public/assests/image/uploads/profile_pictures/profile.jpg';
    
    // Format distance
    const distanceText = runner.distance ? `${runner.distance} km away` : 'Distance unknown';
    
    // Get transportation method for display
    const transportMethod = runner.vehicle_type || runner.transportation_method || 'Not specified';
    
    // Calculate estimated price and delivery time
    const estimate = calculateEstimatedPrice(runner.distance, runner.transportation_method, runner.vehicle_type);
    
    // Format services
    const servicesBadges = runner.services_array && runner.services_array.length > 0
      ? runner.services_array.map(service => 
          `<span class="badge bg-light text-dark me-1 mb-1">${service}</span>`
        ).join('')
      : '<span class="badge bg-light text-muted">No services listed</span>';
    
    // Availability indicator
    const availabilityBadge = runner.is_available 
      ? '<span class="badge bg-success rounded-pill ms-2"><i class="bi bi-circle-fill me-1"></i>Available</span>'
      : '';
    
    col.innerHTML = `
      <div class="card h-100 runner-card">
        <div class="card-body">
          <div class="d-flex gap-3 mb-3">
            <img src="${profileImg}" alt="${runner.name} profile" class="runner-img rounded-circle" 
                 style="width: 60px; height: 60px; object-fit: cover;" 
                 onerror="this.src='../public/assests/image/uploads/profile_pictures/profile.jpg'">
            <div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h3 class="mb-0 fw-semibold fs-5">${runner.name || 'Unknown Runner'}</h3>
                <span class="badge bg-primary rounded-pill">
                  <i class="bi bi-patch-check-fill me-1"></i>Verified
                </span>
                ${availabilityBadge}
              </div>
              <div class="d-flex align-items-center text-muted small mt-1">
                <i class="bi bi-geo-alt me-1"></i>
                <span>${distanceText}</span>
              </div>
              <div class="d-flex align-items-center mt-1">
                <i class="bi bi-star-fill text-warning me-1"></i>
                <span class="small">4.8 (150 reviews)</span>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="small fw-medium mb-1">Services:</div>
            <div class="d-flex flex-wrap">
              ${servicesBadges}
            </div>
          </div>
          <div class="mb-2">
            <div class="small fw-medium mb-1">Transport:</div>
            <div class="small text-muted">
              <i class="bi bi-${getTransportIcon(transportMethod)} me-1"></i>
              ${capitalizeFirstLetter(transportMethod)}
            </div>
          </div>
          <div>
            <div class="small fw-medium mb-1">Estimated:</div>
            <div class="small text-muted">${estimate.price} | ${estimate.timeRange}</div>
          </div>
        </div>
        <div class="card-footer bg-white border-top-0 pt-0">
          <button class="btn btn-primary w-100" data-runner-id="${runner.runner_id}" onclick="bookRunner(${runner.runner_id}, '${(runner.name || 'Runner').replace(/'/g, "\\'")}')">
            Book Now
          </button>
        </div>
      </div>
    `;
    
    return col;
  }
  
  // Helper function to get appropriate icon for transport method
  function getTransportIcon(transportMethod) {
    const type = transportMethod.toLowerCase();
    if (type.includes('motorcycle')) return 'bicycle';
    if (type.includes('car')) return 'car-front';
    if (type.includes('van')) return 'truck';
    if (type.includes('bicycle') || type.includes('bike')) return 'bicycle';
    if (type.includes('walking')) return 'person-walking';
    return 'arrow-right-circle'; // default
  }
  
  // Helper function to capitalize first letter
  function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
  
  // Helper functions
  function clearRunnerContainers() {
    // Clear modal container
    if (elements.runnersList) {
      elements.runnersList.innerHTML = '';
    }
    
    // Clear main container
    if (elements.mainContainer) {
      // Only remove runner cards, not the loading/no runners messages
      const existingCards = elements.mainContainer.querySelectorAll('.col-md-6, .col-lg-4');
      existingCards.forEach(card => card.remove());
    }
  }
  
  function showLoading(isLoading) {
    // Modal loading
    if (elements.loadingIndicator) {
      elements.loadingIndicator.classList.toggle('d-none', !isLoading);
    }
    
    // Main page loading
    if (elements.mainLoadingIndicator) {
      elements.mainLoadingIndicator.classList.toggle('d-none', !isLoading);
    }
  }
  
  function showNoRunnersMessage(show) {
    // Modal message
    if (elements.noRunnersMessage) {
      elements.noRunnersMessage.classList.toggle('d-none', !show);
    }
    
    // Main page message
    if (elements.mainNoRunnersMessage) {
      elements.mainNoRunnersMessage.classList.toggle('d-none', !show);
    }
  }
  
  function showError(errorMessage) {
    // Update error message and show it in modal
    if (elements.noRunnersMessage) {
      elements.noRunnersMessage.innerHTML = `
        <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
        <p class="mt-2">Error loading runners: ${errorMessage}</p>
        <button class="btn btn-outline-primary mt-2" onclick="fetchRunners()">
          <i class="bi bi-arrow-clockwise me-1"></i> Retry
        </button>
      `;
      elements.noRunnersMessage.classList.remove('d-none');
    }
    
    // Update error message on main page too
    if (elements.mainNoRunnersMessage) {
      elements.mainNoRunnersMessage.innerHTML = `
        <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
        <p class="mt-2">Error loading runners: ${errorMessage}</p>
        <button class="btn btn-outline-primary mt-2" onclick="fetchRunners()">
          <i class="bi bi-arrow-clockwise me-1"></i> Retry
        </button>
      `;
      elements.mainNoRunnersMessage.classList.remove('d-none');
    }
  }
  
  // Book runner function (global for onclick access)
  window.bookRunner = function(runnerId, runnerName) {
    console.log(`Booking runner: ${runnerName} (ID: ${runnerId})`);
    
    // Close modal if it's open
    const modal = bootstrap.Modal.getInstance(document.getElementById('onlineRunnersModal'));
    if (modal) {
      modal.hide();
    }
    
    // Show booking confirmation
    alert(`You selected ${runnerName}. Booking functionality will be implemented in the future.`);
    
    // For future implementation, you could redirect:
    // window.location.href = `/booking.php?runner_id=${runnerId}`;
  };
  
  // Initialize and start fetching
  initEventListeners();
  fetchRunners();
});