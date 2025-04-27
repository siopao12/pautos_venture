/**
 * Enhanced Runner Booking System - Updated with "first subcategory free" model
 */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Enhanced runner booking system loaded');
  
  // Cache DOM elements
  const elements = {
    // Booking modal elements
    errandTaskModal: document.getElementById('errandTaskModal'),
    errandTaskForm: document.getElementById('errandTaskForm'),
    errandTitle: document.getElementById('errandTitle'),
    taskDescription: document.getElementById('taskDescription'),
    taskCategory: document.getElementById('taskCategory'),
    subcategoriesContainer: document.getElementById('subcategoriesContainer'),
    taskLocation: document.getElementById('taskLocation'),
    useMyLocationBtn: document.getElementById('useMyLocationBtn'),
    taskDate: document.getElementById('taskDate'),
    taskTime: document.getElementById('taskTime'),
    specialInstructions: document.getElementById('specialInstructions'),
    taskPhotoUpload: document.getElementById('taskPhotoUpload'),
    photoPreviewContainer: document.getElementById('photoPreviewContainer'),
    estimatedCost: document.getElementById('estimatedCost'),
    costBreakdown: document.getElementById('costBreakdown'), // New element for showing cost breakdown
    paymentMethodRadios: document.querySelectorAll('input[name="paymentMethod"]'),
    selectedRunnerId: document.getElementById('selectedRunnerId'),
    saveTaskBtn: document.getElementById('saveTaskBtn'),
    submitTaskBtn: document.getElementById('submitTaskBtn'),
    progressBar: document.getElementById('bookingProgressBar'),
    
    // Selected runner info
    selectedRunnerInfo: document.getElementById('selectedRunnerInfo'),
    runnerProfilePic: document.getElementById('runnerProfilePic'),
    runnerName: document.getElementById('runnerName'),
    runnerRating: document.getElementById('runnerRating'),
    runnerTransport: document.getElementById('runnerTransport')
  };
  
  // Store subcategory options for each category with pricing
  const subcategories = {
    'cleaning': [
      {name: 'Deep Cleaning', price: 150},
      {name: 'Apartment Cleaning', price: 120},
      {name: 'Regular Cleaning', price: 100},
      {name: 'Office Cleaning', price: 200},
      {name: 'Window Cleaning', price: 80},
      {name: 'Laundry', price: 100},
      {name: 'Car Wash', price: 150}
    ],
    'shopping-delivery': [
      {name: 'Grocery Shopping', price: 100},
      {name: 'Food Pickup', price: 80},
      {name: 'Package Delivery', price: 70},
      {name: 'Pharmacy Pickup', price: 60},
      {name: 'Gift Purchase', price: 100},
      {name: 'Small Item Delivery', price: 50}
    ],
    'babysitter': [
      {name: 'Childcare', price: 200},
      {name: 'Baby Sitting', price: 180},
      {name: 'School Pickup', price: 150},
      {name: 'Afterschool Care', price: 120},
      {name: 'Homework Help', price: 100}
    ],
    'personal-assistant': [
      {name: 'Errands', price: 100},
      {name: 'Wait in Line', price: 80},
      {name: 'Admin Tasks', price: 120},
      {name: 'Schedule Management', price: 150},
      {name: 'Meeting Assistance', price: 120}
    ],
    'senior-assistance': [
      {name: 'Companionship', price: 150},
      {name: 'Medication Pickup', price: 80},
      {name: 'Doctor Visits', price: 200},
      {name: 'Grocery Shopping', price: 100},
      {name: 'Home Safety Check', price: 120}
    ],
    'pet-care': [
      {name: 'Dog Walking', price: 100},
      {name: 'Pet Sitting', price: 150},
      {name: 'Vet Visits', price: 180},
      {name: 'Pet Grooming', price: 200},
      {name: 'Pet Taxi', price: 120}
    ]
  };
  
  // Fixed base price for all services (includes one subcategory)
  const FIXED_BASE_PRICE = 300;
  
  // Store uploaded photos
  let uploadedPhotos = [];
  
  // Store selected runner data
  let selectedRunner = null;
  
  // Store all runners data
  let allRunners = [];
  
  // Initialize event listeners
  function initEventListeners() {
    // Category change event
    if (elements.taskCategory) {
      elements.taskCategory.addEventListener('change', updateSubcategories);
    }
    
    // Photo upload event
    if (elements.taskPhotoUpload) {
      elements.taskPhotoUpload.addEventListener('change', handlePhotoUpload);
    }
    
    // Use my location button
    if (elements.useMyLocationBtn) {
      elements.useMyLocationBtn.addEventListener('click', useMyLocation);
    }
    
    // Save task button
    if (elements.saveTaskBtn) {
      elements.saveTaskBtn.addEventListener('click', saveTaskDraft);
    }
    
    // Submit task button
    if (elements.submitTaskBtn) {
      elements.submitTaskBtn.addEventListener('click', submitTaskRequest);
    }
    
    // Form field validation
    if (elements.errandTaskForm) {
      const requiredFields = elements.errandTaskForm.querySelectorAll('[required]');
      requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
          validateField(this);
        });
      });
    }
    
    // Modal events
    if (elements.errandTaskModal) {
      elements.errandTaskModal.addEventListener('shown.bs.modal', function() {
        // Reset progress bar animation
        if (elements.progressBar) {
          elements.progressBar.style.width = '0%';
          setTimeout(() => {
            elements.progressBar.style.width = '100%';
          }, 100);
        }
        
      });
    }
  }
  
  // Set default date and time
  function setDefaultDateTime() {
    if (elements.taskDate && elements.taskTime) {
      const now = new Date();
      
      // Set default date to today
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      elements.taskDate.value = `${year}-${month}-${day}`;
      
      // Set default time to current time + 1 hour
      now.setHours(now.getHours() + 1);
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      elements.taskTime.value = `${hours}:${minutes}`;
    }
  }
  
  // Update subcategories based on selected category
  function updateSubcategories() {
    const selectedCategory = elements.taskCategory.value;
    const container = elements.subcategoriesContainer;
    
    if (!container) return;
    
    // Clear existing content
    container.innerHTML = '';
    
    if (!selectedCategory || !subcategories[selectedCategory]) {
      container.innerHTML = `
        <div class="text-center py-3">
          <i class="bi bi-grid-3x3-gap fs-3 text-muted"></i>
          <p class="text-muted small mt-2">Please select a category first</p>
        </div>
      `;
      return;
    }
    
    // Add note about first subcategory being included in base price
    const noteDiv = document.createElement('div');
    noteDiv.className = 'alert alert-info mb-3';
    noteDiv.innerHTML = `
      <i class="bi bi-info-circle-fill me-2"></i>
      <small>The ₱300 base price includes your first subcategory selection. Additional subcategories will incur extra charges.</small>
    `;
    container.appendChild(noteDiv);
    
    // Add subcategory checkboxes with enhanced styling and pricing
    const subcategoryList = document.createElement('div');
    subcategoryList.className = 'row g-2';
    
    subcategories[selectedCategory].forEach((subcategory, index) => {
      const checkboxId = `subcategory_${index}`;
      
      const col = document.createElement('div');
      col.className = 'col-md-6';
      
      const checkboxDiv = document.createElement('div');
      checkboxDiv.className = 'form-check d-flex justify-content-between align-items-center';
      checkboxDiv.innerHTML = `
        <div>
          <input class="form-check-input subcategory-checkbox" type="checkbox" 
                value="${subcategory.name}" 
                data-price="${subcategory.price}" 
                id="${checkboxId}">
          <label class="form-check-label" for="${checkboxId}">${subcategory.name}</label>
        </div>
        <span class="badge bg-light text-dark">₱${subcategory.price}</span>
      `;
      
      col.appendChild(checkboxDiv);
      subcategoryList.appendChild(col);
    });
    
    container.appendChild(subcategoryList);
    
    // Add event listeners to all subcategory checkboxes
    const checkboxes = container.querySelectorAll('.subcategory-checkbox');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', updateEstimatedCost);
    });
    
    // Add animation class
    container.classList.add('animate__animated', 'animate__fadeIn');
    setTimeout(() => {
      container.classList.remove('animate__animated', 'animate__fadeIn');
    }, 500);
    
    // Update the estimated cost with the new category
    updateEstimatedCost();
  }
  
    // Add this code to debug the selectedRunner object
// Calculate and update the estimated cost
function updateEstimatedCost() {
  if (!elements.estimatedCost) return;
  
  // Start with fixed base price (which includes one subcategory)
  let totalCost = FIXED_BASE_PRICE;
  
  // Add distance-based cost if runner's distance is available
  let distanceCost = 0;
  let transportationDetails = '';
  
  if (selectedRunner && selectedRunner.distance > 0) {
    const transportMethod = selectedRunner.transportation_method || 'standard transport';
    const distance = parseFloat(selectedRunner.distance);
    
    // Calculate price per km based on transport type
    let pricePerKm = 15; // Default rate
    
    // Adjust rate based on transport type
    const transportType = transportMethod.toLowerCase();
    if (transportType.includes('car')) pricePerKm = 20;
    else if (transportType.includes('motorcycle')) pricePerKm = 15;
    else if (transportType.includes('e-bike')) pricePerKm = 12;
    else if (transportType.includes('bicycle')) pricePerKm = 10;
    else if (transportType.includes('van')) pricePerKm = 25;
    else if (transportType.includes('walking')) pricePerKm = 8;
    
    // Add distance-based cost
    distanceCost = Math.round(distance * pricePerKm);
    totalCost += distanceCost;
    
    // Create transportation details for display
    transportationDetails = `${distance.toFixed(1)} km via ${transportMethod} (₱${pricePerKm}/km)`;
  }
  
  // Get selected subcategories
  const checkboxes = elements.subcategoriesContainer.querySelectorAll('.subcategory-checkbox:checked');
  
  // Calculate additional subcategory costs (first one is free)
  let additionalSubcategoryCost = 0;
  let subcategoriesAdded = [];
  
  checkboxes.forEach((checkbox, index) => {
    const subcategoryName = checkbox.value;
    const subcategoryPrice = parseFloat(checkbox.dataset.price) || 0;
    
    // Only add cost for subcategories beyond the first one
    if (index > 0) {
      additionalSubcategoryCost += subcategoryPrice;
    }
    
    subcategoriesAdded.push({
      name: subcategoryName,
      price: subcategoryPrice,
      isFree: index === 0
    });
  });
  
  totalCost += additionalSubcategoryCost;
  
  // Round to nearest 5 pesos
  totalCost = Math.ceil(totalCost / 5) * 5;
  
  // Update the estimated cost field
  elements.estimatedCost.value = totalCost.toString();
  
  // Create a subtle pulse animation effect
  elements.estimatedCost.classList.add('animate__animated', 'animate__pulse');
  setTimeout(() => {
    elements.estimatedCost.classList.remove('animate__animated', 'animate__pulse');
  }, 500);
  
  // Update cost breakdown if the element exists
  if (elements.costBreakdown) {
    // Create breakdown HTML
    let breakdownHtml = `
      <div class="small text-muted mb-2">Cost Breakdown:</div>
      <ul class="list-unstyled small">
        <li><span class="fw-medium">Base Price:</span> ₱${FIXED_BASE_PRICE} (includes 1 subcategory)</li>
    `;
    
    // Always display transportation fee
    breakdownHtml += `
      <li><span class="fw-medium">Transportation Fee:</span> ₱${distanceCost}</li>
    `;
    
    // Add transportation details if available
    if (transportationDetails) {
      breakdownHtml += `
        <li class="text-muted ms-3 small"><i class="bi bi-geo-alt"></i> ${transportationDetails}</li>
      `;
    }
    
    // Add subcategories section
    if (subcategoriesAdded.length > 0) {
      breakdownHtml += `<li><span class="fw-medium">Subcategories:</span></li>`;
      breakdownHtml += `<ul class="ps-3">`;
      
      subcategoriesAdded.forEach((subcat) => {
        if (subcat.isFree) {
          breakdownHtml += `<li>${subcat.name} - Included in base price</li>`;
        } else {
          breakdownHtml += `<li>${subcat.name} - ₱${subcat.price}</li>`;
        }
      });
      
      breakdownHtml += `</ul>`;
    }
    
    breakdownHtml += `<li class="fw-bold mt-2">Total: ₱${totalCost}</li>`;
    breakdownHtml += `</ul>`;
    
    elements.costBreakdown.innerHTML = breakdownHtml;
  }
}

// Update the bookRunner function to correctly receive and process the distance and transport method
window.bookRunner = function(runnerId, runnerName, distance, transportMethod) {
  console.log(`Booking runner: ${runnerName} (ID: ${runnerId}), Distance: ${distance}, Transport: ${transportMethod}`);
  
  // Close runners modal if it's open
  const runnersModal = bootstrap.Modal.getInstance(document.getElementById('onlineRunnersModal'));
  if (runnersModal) {
    runnersModal.hide();
  }
  
  // Set runner ID in hidden field
  if (elements.selectedRunnerId) {
    elements.selectedRunnerId.value = runnerId;
  }
  
  // Set modal title to include runner name
  const modalTitle = document.getElementById('errandTaskModalLabel');
  if (modalTitle) {
    modalTitle.innerHTML = `<i class="bi bi-clipboard-check me-2"></i>Create an Errand Task with ${runnerName}`;
  }
  
  // Ensure distance is a number, not a string
  const numericDistance = parseFloat(distance) || 0;
  
  // Store the runner data for transportation fee calculation
  selectedRunner = {
    runner_id: runnerId,
    name: runnerName,
    distance: numericDistance,
    transportation_method: transportMethod || 'standard transport'
  };
  
  console.log("Selected runner data:", selectedRunner);
  
  // Set default cost to the base price
  if (elements.estimatedCost) {
    elements.estimatedCost.value = FIXED_BASE_PRICE.toString();
  }
  
  // Reset form
  if (elements.errandTaskForm) {
    elements.errandTaskForm.reset();
    elements.errandTaskForm.classList.remove('was-validated');
    
    // Re-set the runner ID as reset() will clear it
    if (elements.selectedRunnerId) {
      elements.selectedRunnerId.value = runnerId;
    }
  }
  
  // Clear photo previews
  if (elements.photoPreviewContainer) {
    elements.photoPreviewContainer.innerHTML = '';
  }
  uploadedPhotos = [];
  
  // Set default date and time
  setDefaultDateTime();
  
  // Update estimated cost based on the selected runner's distance
  updateEstimatedCost();
  
  // Show the booking modal
  const bookingModal = new bootstrap.Modal(document.getElementById('errandTaskModal'));
  bookingModal.show();
};

  // Handle photo upload with enhanced UI
  function handlePhotoUpload(event) {
    const files = event.target.files;
    
    if (!files || files.length === 0) return;
    
    const previewContainer = elements.photoPreviewContainer;
    if (!previewContainer) return;
    
    // Check file count
    const maxFiles = 5;
    if (uploadedPhotos.length + files.length > maxFiles) {
      alert(`You can upload a maximum of ${maxFiles} images.`);
      return;
    }
    
    // Process each file
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      
      // Validate file is an image
      if (!file.type.startsWith('image/')) {
        alert('Please upload only image files.');
        continue;
      }
      
      // Validate file size (5MB max)
      if (file.size > 5 * 1024 * 1024) {
        alert('Please upload images smaller than 5MB.');
        continue;
      }
      
      // Create file reader to display preview
      const reader = new FileReader();
      
      reader.onload = function(e) {
        // Create preview element with enhanced styling
        const previewDiv = document.createElement('div');
        previewDiv.className = 'image-preview';
        
        const previewImg = document.createElement('img');
        previewImg.src = e.target.result;
        previewImg.className = 'shadow-sm';
        
        const removeBtn = document.createElement('div');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.dataset.filename = file.name;
        removeBtn.addEventListener('click', function() {
          // Add removal animation
          previewDiv.classList.add('animate__animated', 'animate__zoomOut');
          
          setTimeout(() => {
            // Remove the preview
            previewDiv.remove();
            
            // Remove from uploaded photos array
            const filename = this.dataset.filename;
            uploadedPhotos = uploadedPhotos.filter(photo => photo.name !== filename);
          }, 300);
        });
        
        previewDiv.appendChild(previewImg);
        previewDiv.appendChild(removeBtn);
        
        // Add appear animation
        previewDiv.classList.add('animate__animated', 'animate__zoomIn');
        
        previewContainer.appendChild(previewDiv);
        
        // Add to uploaded photos array
        uploadedPhotos.push({
          name: file.name,
          data: e.target.result,
          file: file
        });
      };
      
      reader.readAsDataURL(file);
    }
    
    // Clear the file input to allow selecting same files again
    event.target.value = '';
  }
  
  // Use my location with enhanced UI
  function useMyLocation() {
    // Show loading state
    elements.useMyLocationBtn.disabled = true;
    elements.useMyLocationBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Getting location...';
    
    // Try to get the user's address from the PHP session
    let userAddress = null;
    
    // Get the address using PHP session data exposed to JavaScript as a meta tag
    try {
      const phpSessionData = document.querySelector('meta[name="user-location"]');
      if (phpSessionData && phpSessionData.content) {
        userAddress = phpSessionData.content;
      }
    } catch (e) {
      console.log('No meta tag with user location found');
    }
    
    // Check if we found a user address
    if (userAddress) {
      // Use with a nice animation
      setTimeout(() => {
        elements.taskLocation.value = userAddress;
        elements.taskLocation.classList.add('is-valid');
        elements.useMyLocationBtn.disabled = false;
        elements.useMyLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Location';
      }, 500);
      return;
    }
    
    // Extract data from PHP session if available
    if (typeof window.userLocation !== 'undefined' && window.userLocation.address) {
      setTimeout(() => {
        elements.taskLocation.value = window.userLocation.address;
        elements.taskLocation.classList.add('is-valid');
        elements.useMyLocationBtn.disabled = false;
        elements.useMyLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Location';
      }, 500);
      return;
    }
    
    // If no stored location is available, use browser geolocation
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          // Get coordinates
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          // Use coordinates
          setTimeout(() => {
            elements.taskLocation.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            elements.taskLocation.classList.add('is-valid');
            elements.useMyLocationBtn.disabled = false;
            elements.useMyLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Location';
          }, 500);
        },
        function(error) {
          console.error('Geolocation error:', error);
          showToast('Could not get your location. Please enter it manually.', 'error');
          
          elements.useMyLocationBtn.disabled = false;
          elements.useMyLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Location';
        }
      );
    } else {
      showToast('Geolocation is not supported by your browser. Please enter your location manually.', 'error');
      
      elements.useMyLocationBtn.disabled = false;
      elements.useMyLocationBtn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Use My Location';
    }
  }
  
  // Save task draft with enhanced UI
  function saveTaskDraft() {
    if (!validateForm()) {
      showToast('Please fill in all required fields', 'warning');
      return;
    }
    
    const taskData = collectFormData();
    
    // Show loading state
    elements.saveTaskBtn.disabled = true;
    elements.saveTaskBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    
    // Store in localStorage
    setTimeout(() => {
      const savedTasks = JSON.parse(localStorage.getItem('savedTasks') || '[]');
      savedTasks.push({
        id: 'draft_' + Date.now(),
        data: taskData,
        savedAt: new Date().toISOString()
      });
      
      localStorage.setItem('savedTasks', JSON.stringify(savedTasks));
      
      // Notify user
      showToast('Task saved as draft successfully!', 'success');
      
      // Reset button state
      elements.saveTaskBtn.disabled = false;
      elements.saveTaskBtn.innerHTML = '<i class="bi bi-save me-1"></i>Save Draft';
      
      // Close modal
      const modal = bootstrap.Modal.getInstance(elements.errandTaskModal);
      if (modal) {
        modal.hide();
      }
    }, 1000);
  }
  
  // Submit task request with enhanced UI
  function submitTaskRequest() {
    if (!validateForm()) {
      showToast('Please fill in all required fields', 'warning');
      return;
    }
    
    const taskData = collectFormData();
    
    // Prepare form data for submission
    const formData = new FormData();
    
    // Add task data to form
    Object.keys(taskData).forEach(key => {
      // Handle arrays specially
      if (Array.isArray(taskData[key])) {
        formData.append(key, JSON.stringify(taskData[key]));
      } else {
        formData.append(key, taskData[key]);
      }
    });
    
    // Add photos
    uploadedPhotos.forEach((photo, index) => {
      formData.append(`photo_${index}`, photo.file);
    });
    
    // Show loading state
    elements.submitTaskBtn.disabled = true;
    elements.submitTaskBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
    
    // API path for task submission
    const apiPath = '../database/api/create_errand_task.php';
    
    // Simulate a delay for demo purposes (remove this in production)
    setTimeout(() => {
      fetch(apiPath, {
        method: 'POST',
        body: formData
      })
        .then(response => {
          if (!response.ok) {
            throw new Error(`Network response error: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            // Show success
            showToast('Task submitted successfully!', 'success');
            
            // Add a short delay before redirecting
            setTimeout(() => {
              window.location.href = `task_details.php?id=${data.task_id}`;
            }, 1500);
          } else {
            throw new Error(data.message || 'Failed to submit task');
          }
        })
        .catch(error => {
          console.error('Error submitting task:', error);
          showToast('Failed to submit task: ' + error.message, 'error');
          
          // Reset button
          elements.submitTaskBtn.disabled = false;
          elements.submitTaskBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Submit Request';
        });
    }, 1500);
  }
  
  // Validate form with enhanced UI
  function validateForm() {
    // Check if form exists
    if (!elements.errandTaskForm) {
      return false;
    }
    
    // Add validation class to show validation errors
    elements.errandTaskForm.classList.add('was-validated');
    
    // Check all required fields
    const requiredFields = elements.errandTaskForm.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
      if (!validateField(field)) {
        isValid = false;
      }
    });
    
    // Check if payment method is selected
    let paymentMethodSelected = false;
    elements.paymentMethodRadios.forEach(radio => {
      if (radio.checked) {
        paymentMethodSelected = true;
      }
    });
    
    if (!paymentMethodSelected) {
      isValid = false;
      showToast('Please select a payment method', 'warning');
    }
    
    // Check if at least one subcategory is selected
    const subcategorySelected = elements.subcategoriesContainer.querySelectorAll('.subcategory-checkbox:checked').length > 0;
    if (!subcategorySelected) {
      isValid = false;
      showToast('Please select at least one subcategory', 'warning');
    }
    
    return isValid;
  }
  
  // Validate a single field
  function validateField(field) {
    if (!field.value.trim()) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
      
      // Scroll to the first invalid field
      if (!document.querySelector('.is-invalid:first-of-type').isSameNode(field)) {
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      
      return false;
    } else {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
      return true;
    }
  }
  
  // Collect form data
  function collectFormData() {
    // Get selected subcategories with their prices and free status
    const selectedSubcategories = [];
    const subcategoryPrices = {};
    const subcategoryCheckboxes = elements.subcategoriesContainer.querySelectorAll('.subcategory-checkbox:checked');
    
    subcategoryCheckboxes.forEach((checkbox, index) => {
      const subcategoryName = checkbox.value;
      const subcategoryPrice = parseFloat(checkbox.dataset.price) || 0;
      const isFree = index === 0; // First subcategory is free
      
      selectedSubcategories.push(subcategoryName);
      subcategoryPrices[subcategoryName] = {
        price: subcategoryPrice,
        isFree: isFree
      };
    });
    
    // Get selected payment method
    let paymentMethod = '';
    elements.paymentMethodRadios.forEach(radio => {
      if (radio.checked) {
        paymentMethod = radio.value;
      }
    });
    
    // Calculate breakdown values
    let distanceCost = 0;
    if (selectedRunner && selectedRunner.distance) {
      const transportMethod = selectedRunner.transportation_method || '';
      const vehicleType = selectedRunner.vehicle_type || '';
      const distance = selectedRunner.distance || 0;
      let pricePerKm = 15;
      
      const transportType = vehicleType.toLowerCase() || transportMethod.toLowerCase();
      if (transportType.includes('car')) pricePerKm = 20;
      else if (transportType.includes('motorcycle')) pricePerKm = 15;
      else if (transportType.includes('e-bike')) pricePerKm = 12;
      else if (transportType.includes('bicycle')) pricePerKm = 10;
      else if (transportType.includes('van')) pricePerKm = 25;
      else if (transportType.includes('walking')) pricePerKm = 8;
      
      distanceCost = Math.round(distance * pricePerKm);
    }
    
    // Collect all form data
    return {
      runner_id: elements.selectedRunnerId.value,
      title: elements.errandTitle.value,
      description: elements.taskDescription.value,
      category: elements.taskCategory.value,
      subcategories: selectedSubcategories,
      subcategory_prices: JSON.stringify(subcategoryPrices),
      location: elements.taskLocation.value,
      date: elements.taskDate.value,
      time: elements.taskTime.value,
      special_instructions: elements.specialInstructions.value,
      estimated_cost: elements.estimatedCost.value,
      payment_method: paymentMethod,
      photo_count: uploadedPhotos.length,
      base_price: FIXED_BASE_PRICE.toString(),
      distance_cost: distanceCost.toString(),
      pricing_model: 'one_free_subcategory'
    };
  }
   
  // Show toast notification
  function showToast(message, type = 'info') {
    // Check if toast container exists, if not create it
    let toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${getToastColor(type)} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('id', toastId);
    
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi ${getToastIcon(type)} me-2"></i>
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
      autohide: true,
      delay: 3000
    });
    
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
      this.remove();
    });
  }
  
  // Helper functions for toast
  function getToastColor(type) {
    switch (type) {
      case 'success': return 'success';
      case 'error': return 'danger';
      case 'warning': return 'warning';
      default: return 'primary';
    }
  }
  
  function getToastIcon(type) {
    switch (type) {
      case 'success': return 'bi-check-circle-fill';
      case 'error': return 'bi-exclamation-circle-fill';
      case 'warning': return 'bi-exclamation-triangle-fill';
      default: return 'bi-info-circle-fill';
    }
  }
  
     // Also update the bookRunner function to ensure distance is passed as a number
    window.bookRunner = function(runnerId, runnerName, distance, transportMethod) {
      console.log(`Booking runner: ${runnerName} (ID: ${runnerId}), Distance: ${distance}, Transport: ${transportMethod}`);
      
      // Close runners modal if it's open
      const runnersModal = bootstrap.Modal.getInstance(document.getElementById('onlineRunnersModal'));
      if (runnersModal) {
        runnersModal.hide();
      }
      
      // Set runner ID in hidden field
      if (elements.selectedRunnerId) {
        elements.selectedRunnerId.value = runnerId;
      }
      
      // Set modal title to include runner name
      const modalTitle = document.getElementById('errandTaskModalLabel');
      if (modalTitle) {
        modalTitle.innerHTML = `<i class="bi bi-clipboard-check me-2"></i>Create an Errand Task with ${runnerName}`;
      }
      
      // Ensure distance is a number, not a string
      const numericDistance = parseFloat(distance);
      
      // Store the runner data for transportation fee calculation
      selectedRunner = {
        runner_id: runnerId,
        name: runnerName,
        distance: numericDistance,
        transportation_method: transportMethod
      };
      
      console.log("Selected runner data:", selectedRunner); // Debug log to check data
      
      // Set default cost to the base price
      if (elements.estimatedCost) {
        elements.estimatedCost.value = FIXED_BASE_PRICE.toString();
      }
      
      // Reset form
      if (elements.errandTaskForm) {
        elements.errandTaskForm.reset();
        elements.errandTaskForm.classList.remove('was-validated');
        
        // Re-set the runner ID as reset() will clear it
        if (elements.selectedRunnerId) {
          elements.selectedRunnerId.value = runnerId;
        }
      }
      
      // Clear photo previews
      if (elements.photoPreviewContainer) {
        elements.photoPreviewContainer.innerHTML = '';
      }
      uploadedPhotos = [];
      
      // Set default date and time
      setDefaultDateTime();
      
      // Update estimated cost based on the selected runner's distance
      updateEstimatedCost();
      
      // Show the booking modal
      const bookingModal = new bootstrap.Modal(document.getElementById('errandTaskModal'));
      bookingModal.show();
    };
  // Initialize everything
  initEventListeners();
  
  // Make allRunners available globally
  window.allRunners = window.allRunners || [];
  });