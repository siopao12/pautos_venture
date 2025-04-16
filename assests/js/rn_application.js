document.addEventListener('DOMContentLoaded', function () {
  // Variables
  let currentStep = 1;
  const totalSteps = 3;
  
  // Elements
  const prevBtn = document.getElementById('prevStep');
  const nextBtn = document.getElementById('nextStep');
  const submitBtn = document.getElementById('submitRunnerBtn');
  const progressBar = document.getElementById('progressBar');
  const stepIndicators = document.querySelectorAll('.step-indicator');
  
  // File upload preview functionality
  setupFileUploadPreview('idPhoto', 'idPreview', 'idUploadContainer');
  setupFileUploadPreview('selfiePhoto', 'selfiePreview', 'selfieUploadContainer');
  setupFileUploadPreview('vehiclePhoto', 'vehiclePhotoPreview', 'vehiclePhotoUploadContainer');
  
  // Transportation method selection
  const transportationCards = document.querySelectorAll('.transportation-card');
  const transportationMethodInput = document.getElementById('transportationMethod');
  
  transportationCards.forEach(card => {
    card.addEventListener('click', function() {
      // Remove active class from all cards
      transportationCards.forEach(c => c.classList.remove('active-transport'));
      
      // Add active class to selected card
      this.classList.add('active-transport');
      
      // Get and set the selected transportation method
      const method = this.getAttribute('data-method');
      transportationMethodInput.value = method;
      
      // Hide all detail sections
      document.getElementById('vehicleDetailsSection').classList.add('d-none');
      document.getElementById('walkingDetailsSection').classList.add('d-none');
      document.getElementById('commuteDetailsSection').classList.add('d-none');
      
      // Show appropriate section based on selection
      if (method === 'vehicle') {
        document.getElementById('vehicleDetailsSection').classList.remove('d-none');
        // Make vehicle fields required
        document.getElementById('vehicleType').required = true;
        document.getElementById('registrationNumber').required = true;
        document.getElementById('licenseNumber').required = true;
        document.getElementById('vehiclePhoto').required = true;
        
        // Make other fields not required
        document.getElementById('serviceRadius').required = false;
        document.getElementById('walkingZipcode').required = false;
        document.getElementById('transitType').required = false;
        document.getElementById('transitRadius').required = false;
      } 
      else if (method === 'walking') {
        document.getElementById('walkingDetailsSection').classList.remove('d-none');
        // Make walking fields required
        document.getElementById('serviceRadius').required = true;
        document.getElementById('walkingZipcode').required = true;
        
        // Make other fields not required
        document.getElementById('vehicleType').required = false;
        document.getElementById('registrationNumber').required = false;
        document.getElementById('licenseNumber').required = false;
        document.getElementById('vehiclePhoto').required = false;
        document.getElementById('transitType').required = false;
        document.getElementById('transitRadius').required = false;
      } 
      else if (method === 'commute') {
        document.getElementById('commuteDetailsSection').classList.remove('d-none');
        // Make commute fields required
        document.getElementById('transitType').required = true;
        document.getElementById('transitRadius').required = true;
        
        // Make other fields not required
        document.getElementById('vehicleType').required = false;
        document.getElementById('registrationNumber').required = false;
        document.getElementById('licenseNumber').required = false;
        document.getElementById('vehiclePhoto').required = false;
        document.getElementById('serviceRadius').required = false;
        document.getElementById('walkingZipcode').required = false;
      }
    });
  });
  
  // Form validation
  const form = document.getElementById('runnerApplicationForm');
  
  // Navigate to next step with validation
  nextBtn.addEventListener('click', () => {
    if (validateCurrentStep(currentStep)) {
      if (currentStep < totalSteps) {
        currentStep++;
        updateStepDisplay();
      }
    }
  });
  
  // Navigate to previous step
  prevBtn.addEventListener('click', () => {
    if (currentStep > 1) {
      currentStep--;
      updateStepDisplay();
    }
  });
  
  // Form submission
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (validateCurrentStep(currentStep)) {
      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...';
      
      // Simulate form submission (replace with actual submission)
      setTimeout(() => {
        // Show success message
        const modalBody = document.querySelector('.modal-body');
        modalBody.innerHTML = `
          <div class="text-center py-5">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            </div>
            <h4 class="mb-3">Application Submitted Successfully!</h4>
            <p class="mb-4">Your application has been received and is being reviewed. You'll receive a notification once it's approved.</p>
            <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
              Got it!
            </button>
          </div>
        `;
        
        // Hide footer
        document.querySelector('.modal-footer').classList.add('d-none');
      }, 2000);
    }
  });
  
  // Initialize the first step
  updateStepDisplay();
  
  // Function to update step display
  function updateStepDisplay() {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
    
    // Show current step
    document.getElementById('step' + currentStep).classList.remove('d-none');
    
    // Update progress bar
    const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
    progressBar.style.width = progressPercentage + '%';
    progressBar.setAttribute('aria-valuenow', progressPercentage);
    
    // Update step indicators
    stepIndicators.forEach((indicator, index) => {
      if (index + 1 < currentStep) {
        indicator.classList.add('active', 'completed');
        indicator.classList.remove('current');
      } else if (index + 1 === currentStep) {
        indicator.classList.add('active', 'current');
        indicator.classList.remove('completed');
      } else {
        indicator.classList.remove('active', 'completed', 'current');
      }
    });
    
    // Update buttons
    prevBtn.disabled = currentStep === 1;
    if (currentStep === totalSteps) {
      nextBtn.classList.add('d-none');
      submitBtn.classList.remove('d-none');
    } else {
      nextBtn.classList.remove('d-none');
      submitBtn.classList.add('d-none');
    }
  }
  
  // Function to validate current step
  function validateCurrentStep(step) {
    let isValid = true;
    
    switch(step) {
      case 1:
        // Validate ID uploads
        if (!document.getElementById('idPhoto').files.length) {
          showValidationError('idUploadContainer', 'Please upload your ID');
          isValid = false;
        }
        
        if (!document.getElementById('selfiePhoto').files.length) {
          showValidationError('selfieUploadContainer', 'Please upload your selfie with ID');
          isValid = false;
        }
        break;
        
      case 2:
        // Validate transportation method is selected
        if (!transportationMethodInput.value) {
          showValidationError(document.querySelector('.row.g-3'), 'Please select a transportation method');
          isValid = false;
          break;
        }
        
        // Validate specific transportation details based on selection
        if (transportationMethodInput.value === 'vehicle') {
          const vehicleType = document.getElementById('vehicleType');
          const regNumber = document.getElementById('registrationNumber');
          const licenseNumber = document.getElementById('licenseNumber');
          const vehiclePhoto = document.getElementById('vehiclePhoto');
          
          if (!vehicleType.value) {
            showValidationError(vehicleType.parentElement, 'Please select your vehicle type');
            isValid = false;
          }
          
          if (!regNumber.value.trim()) {
            showValidationError(regNumber.parentElement, 'Please enter registration number');
            isValid = false;
          }
          
          if (!licenseNumber.value.trim()) {
            showValidationError(licenseNumber.parentElement, 'Please enter license number');
            isValid = false;
          }
          
          if (!vehiclePhoto.files.length) {
            showValidationError('vehiclePhotoUploadContainer', 'Please upload a photo of your vehicle');
            isValid = false;
          }
        } 
        else if (transportationMethodInput.value === 'walking') {
          const serviceRadius = document.getElementById('serviceRadius');
          const walkingZipcode = document.getElementById('walkingZipcode');
          
          if (!serviceRadius.value) {
            showValidationError(serviceRadius.parentElement, 'Please enter your service radius');
            isValid = false;
          }
          
          if (!walkingZipcode.value.trim()) {
            showValidationError(walkingZipcode.parentElement, 'Please enter your ZIP/Postal code');
            isValid = false;
          }
        } 
        else if (transportationMethodInput.value === 'commute') {
          const transitType = document.getElementById('transitType');
          const transitRadius = document.getElementById('transitRadius');
          
          if (!transitType.value) {
            showValidationError(transitType.parentElement, 'Please select your preferred transit type');
            isValid = false;
          }
          
          if (!transitRadius.value) {
            showValidationError(transitRadius.parentElement, 'Please enter your service radius');
            isValid = false;
          }
        }
        break;
        
      case 3:
        // Validate at least one category is selected
        const selectedCategories = document.querySelectorAll('input[name="categories[]"]:checked');
        if (selectedCategories.length === 0) {
          const alert = document.querySelector('.alert');
          alert.classList.remove('alert-info');
          alert.classList.add('alert-danger');
          alert.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> Please select at least one service category.';
          
          // Reset alert after 3 seconds
          setTimeout(() => {
            alert.classList.remove('alert-danger');
            alert.classList.add('alert-info');
            alert.innerHTML = '<i class="bi bi-info-circle me-2"></i> Please choose at least one category. You can update your service categories later.';
          }, 3000);
          
          isValid = false;
        }
        break;
    }
    
    return isValid;
  }
  
  // Function to handle file upload preview
  function setupFileUploadPreview(inputId, previewId, containerId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const container = document.getElementById(containerId);
    
    if (!input || !preview || !container) return;
    
    // Handle file upload via browse button
    input.addEventListener('change', function() {
      displayFilePreview(this.files[0], preview, container);
    });
    
    // Handle drag and drop
    container.addEventListener('dragover', function(e) {
      e.preventDefault();
      this.classList.add('border-primary');
    });
    
    container.addEventListener('dragleave', function(e) {
      e.preventDefault();
      this.classList.remove('border-primary');
    });
    
    container.addEventListener('drop', function(e) {
      e.preventDefault();
      this.classList.remove('border-primary');
      
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        displayFilePreview(e.dataTransfer.files[0], preview, container);
      }
    });
  }
  
  // Function to display file preview
  function displayFilePreview(file, previewElement, container) {
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        container.querySelector('p').classList.add('d-none');
        container.querySelector('i').classList.add('d-none');
        container.querySelector('label').classList.add('d-none');
        
        previewElement.classList.remove('d-none');
        previewElement.innerHTML = `
          <div class="position-relative">
            <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 150px;" alt="Preview">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview">
              <i class="bi bi-x"></i>
            </button>
          </div>
        `;
        
        // Add remove button functionality
        previewElement.querySelector('.remove-preview').addEventListener('click', function() {
          previewElement.classList.add('d-none');
          previewElement.innerHTML = '';
          container.querySelector('p').classList.remove('d-none');
          container.querySelector('i').classList.remove('d-none');
          container.querySelector('label').classList.remove('d-none');
          
          // Clear the input
          document.getElementById(container.querySelector('label').getAttribute('for')).value = '';
        });
      };
      
      reader.readAsDataURL(file);
    }
  }
  
// Function to show validation error
function showValidationError(element, message) {
  if (typeof element === 'string') {
    element = document.getElementById(element);
  }
  
  element.classList.add('border-danger');
  
  const errorMsg = document.createElement('div');
  errorMsg.className = 'text-danger small mt-1';
  errorMsg.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
  
  // Remove any existing error message
  const existingError = element.parentElement.querySelector('.text-danger');
  if (existingError) {
    existingError.remove();
  }
  
  element.parentElement.appendChild(errorMsg);
  
  // Remove error state after interaction
  const clearError = () => {
    element.classList.remove('border-danger');
    const error = element.parentElement.querySelector('.text-danger');
    if (error) {
      error.remove();
    }
    
    element.removeEventListener('input', clearError);
    element.removeEventListener('change', clearError);
    element.removeEventListener('click', clearError);
  };
  
  element.addEventListener('input', clearError);
  element.addEventListener('change', clearError);
  element.addEventListener('click', clearError);
}
});