document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const runnerForm = document.getElementById('runnerApplicationForm');
    const progressBar = document.getElementById('progressBar');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitRunnerBtn');
    
    // Step indicators
    const step1Indicator = document.getElementById('step1Indicator');
    const step2Indicator = document.getElementById('step2Indicator');
    const step3Indicator = document.getElementById('step3Indicator');
    
    // Step content divs
    const step1Content = document.getElementById('step1');
    const step2Content = document.getElementById('step2');
    const step3Content = document.getElementById('step3');
    
    // File upload fields
    const idPhoto = document.getElementById('idPhoto');
    const selfiePhoto = document.getElementById('selfiePhoto');
    const vehiclePhoto = document.getElementById('vehiclePhoto');
    
    // Transportation method sections
    const transportationMethod = document.getElementById('transportationMethod');
    const vehicleDetailsSection = document.getElementById('vehicleDetailsSection');
    const walkingDetailsSection = document.getElementById('walkingDetailsSection');
    const commuteDetailsSection = document.getElementById('commuteDetailsSection');
    
    // Current step tracker
    let currentStep = 1;
    const totalSteps = 3;
    
    // Initialize subcategories data
    const subcategories = {
        'cleaning': [
            'House Cleaning',
            'Office Cleaning',
            'Deep Cleaning',
            'Window Cleaning',
            'Carpet Cleaning'
        ],
        'shopping-delivery': [
            'Grocery Shopping',
            'Food Delivery',
            'Package Pickup',
            'Medicine Delivery',
            'Gift Shopping'
        ],
        'babysitter': [
            'Daytime Childcare',
            'Evening Babysitting',
            'Infant Care',
            'Homework Help',
            'School Drop-off/Pickup'
        ],
        'personal-assistant': [
            'Administrative Tasks',
            'Event Planning',
            'Research',
            'Bookkeeping',
            'Scheduling'
        ],
        'senior-assistance': [
            'Companion Care',
            'Medication Reminders',
            'Light Housekeeping',
            'Meal Preparation',
            'Transportation'
        ],
        'pet-care': [
            'Dog Walking',
            'Pet Sitting',
            'Feeding',
            'Grooming',
            'Pet Transportation'
        ]
    };
    
    // Selected service subcategories
    const selectedSubcategories = new Set();
    
    // Initialize dropzones for file uploads
    initializeFileUploads();
    
    // Initialize transportation card selection
    initializeTransportationSelection();
    
    // Initialize service category selection
    initializeServiceCategories();
    
    // Navigation buttons event listeners
    prevBtn.addEventListener('click', goToPreviousStep);
    nextBtn.addEventListener('click', goToNextStep);
    
    // Form submission
    runnerForm.addEventListener('submit', handleFormSubmit);
    
    // File upload handling
    function initializeFileUploads() {
        // ID Photo Upload
        idPhoto.addEventListener('change', function(e) {
            handleFileUpload(e, 'idPreview', 'idUploadContainer');
        });
        
        // Selfie Photo Upload
        selfiePhoto.addEventListener('change', function(e) {
            handleFileUpload(e, 'selfiePreview', 'selfieUploadContainer');
        });
        
        // Vehicle Photo Upload
        vehiclePhoto && vehiclePhoto.addEventListener('change', function(e) {
            handleFileUpload(e, 'vehiclePhotoPreview', 'vehiclePhotoUploadContainer');
        });
        
        // Initialize drag and drop for upload containers
        const uploadContainers = document.querySelectorAll('.upload-container');
        uploadContainers.forEach(container => {
            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            container.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });
            
            container.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput && e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            });
        });
    }
    
    // Handle file uploads and preview
    function handleFileUpload(e, previewId, containerId) {
        const file = e.target.files[0];
        const previewContainer = document.getElementById(previewId);
        const uploadContainer = document.getElementById(containerId);
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                previewContainer.innerHTML = `
                    <div class="position-relative">
                        <img src="${event.target.result}" class="img-thumbnail" alt="Preview">
                        <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle p-1 m-1" 
                            aria-label="Remove" onclick="removeUpload('${e.target.id}', '${previewId}', '${containerId}')"></button>
                    </div>
                `;
                previewContainer.classList.remove('d-none');
                uploadContainer.querySelector('i').classList.add('d-none');
                uploadContainer.querySelector('p').classList.add('d-none');
                uploadContainer.querySelector('label').classList.add('d-none');
            };
            
            reader.readAsDataURL(file);
        }
    }
    
    // Remove uploaded file
    window.removeUpload = function(inputId, previewId, containerId) {
        const fileInput = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewId);
        const uploadContainer = document.getElementById(containerId);
        
        fileInput.value = '';
        previewContainer.innerHTML = '';
        previewContainer.classList.add('d-none');
        uploadContainer.querySelector('i').classList.remove('d-none');
        uploadContainer.querySelector('p').classList.remove('d-none');
        uploadContainer.querySelector('label').classList.remove('d-none');
    };
    
    // Initialize transportation method selection
    function initializeTransportationSelection() {
        const transportationCards = document.querySelectorAll('.transportation-card');
        
        transportationCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class from all cards
                transportationCards.forEach(c => c.classList.remove('active-card'));
                
                // Add active class to selected card
                this.classList.add('active-card');
                
                // Set transportation method value
                transportationMethod.value = this.dataset.method;
                
                // Show relevant details section
                showTransportationDetails(this.dataset.method);
            });
        });
    }
    
    // Show transportation details section based on selection
    function showTransportationDetails(method) {
        // Hide all details sections
        vehicleDetailsSection.classList.add('d-none');
        walkingDetailsSection.classList.add('d-none');
        commuteDetailsSection.classList.add('d-none');
        
        // Disable required attributes on all input fields in transportation sections
        toggleRequiredFields(vehicleDetailsSection, false);
        toggleRequiredFields(walkingDetailsSection, false);
        toggleRequiredFields(commuteDetailsSection, false);
        
        // Show selected method details and enable required fields only for visible section
        if (method === 'vehicle') {
            vehicleDetailsSection.classList.remove('d-none');
            toggleRequiredFields(vehicleDetailsSection, true);
        } else if (method === 'walking') {
            walkingDetailsSection.classList.remove('d-none');
            toggleRequiredFields(walkingDetailsSection, true);
        } else if (method === 'commute') {
            commuteDetailsSection.classList.remove('d-none');
            toggleRequiredFields(commuteDetailsSection, true);
        }
    }
    
    // Toggle required attribute on all inputs within a container
    function toggleRequiredFields(container, isRequired) {
        if (!container) return;
        
        const formControls = container.querySelectorAll('input, select, textarea');
        formControls.forEach(element => {
            if (isRequired && element.hasAttribute('data-required')) {
                element.setAttribute('required', '');
            } else {
                element.removeAttribute('required');
            }
        });
    }
    
    // Initialize service categories selection
    function initializeServiceCategories() {
        const serviceCards = document.querySelectorAll('.service-category-card');
        const selectedMainCategory = document.getElementById('selectedMainCategory');
        
        // Create subcategories checkboxes
        Object.keys(subcategories).forEach(category => {
            const container = document.querySelector(`#${category}DetailsSection .subcategories-list`);
            if (container) {
                let html = '';
                
                subcategories[category].forEach((subcategory, index) => {
                    const id = `subcategory-${category}-${index}`;
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="${id}" value="${subcategory}" 
                                data-category="${category}" data-subcategory="${subcategory}">
                            <label class="form-check-label" for="${id}">${subcategory}</label>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
                
                // Add event listeners to checkboxes
                container.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCategories);
                });
            }
        });
        
        // Service category card selection
        serviceCards.forEach(card => {
            card.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Toggle active class
                card.classList.toggle('active-card');
                
                // Show/hide details section
                const detailsSection = document.getElementById(`${category}DetailsSection`);
                if (detailsSection) {
                    if (card.classList.contains('active-card')) {
                        detailsSection.classList.remove('d-none');
                    } else {
                        detailsSection.classList.add('d-none');
                        
                        // Uncheck all checkboxes in this category
                        detailsSection.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        
                        // Update selected categories
                        updateSelectedCategories();
                    }
                }
                
                selectedMainCategory.value = Array.from(document.querySelectorAll('.service-category-card.active-card'))
                    .map(activeCard => activeCard.dataset.category)
                    .join(',');
            });
        });
    }
    
    // Update selected categories display
    function updateSelectedCategories() {
        const selectedCategoriesContainer = document.getElementById('selectedCategoriesContainer');
        selectedSubcategories.clear();
        
        // Get all checked subcategories
        document.querySelectorAll('.subcategories-list input[type="checkbox"]:checked').forEach(checkbox => {
            selectedSubcategories.add({
                category: checkbox.dataset.category,
                subcategory: checkbox.dataset.subcategory
            });
        });
        
        // Display selected subcategories
        if (selectedSubcategories.size > 0) {
            let html = '<ul class="list-group">';
            
            // Group by category
            const categorizedSelections = {};
            selectedSubcategories.forEach(item => {
                if (!categorizedSelections[item.category]) {
                    categorizedSelections[item.category] = [];
                }
                categorizedSelections[item.category].push(item.subcategory);
            });
            
            // Generate HTML
            Object.keys(categorizedSelections).forEach(category => {
                const formattedCategory = category.split('-').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                
                html += `
                    <li class="list-group-item">
                        <strong>${formattedCategory}:</strong> 
                        ${categorizedSelections[category].join(', ')}
                    </li>
                `;
            });
            
            html += '</ul>';
            selectedCategoriesContainer.innerHTML = html;
        } else {
            selectedCategoriesContainer.innerHTML = '<div class="text-muted">No categories selected</div>';
        }
    }
    
    // Go to previous step
    function goToPreviousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
        }
    }
    
    // Go to next step
    function goToNextStep() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStepDisplay();
            }
        }
    }
    
    // Update step display
    function updateStepDisplay() {
        // Hide all steps
        step1Content.classList.add('d-none');
        step2Content.classList.add('d-none');
        step3Content.classList.add('d-none');
        
        // Reset active indicators
        step1Indicator.classList.remove('active');
        step2Indicator.classList.remove('active');
        step3Indicator.classList.remove('active');
        
        // Show current step
        if (currentStep === 1) {
            step1Content.classList.remove('d-none');
            step1Indicator.classList.add('active');
            progressBar.style.width = '33%';
            progressBar.setAttribute('aria-valuenow', '33');
            prevBtn.disabled = true;
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        } else if (currentStep === 2) {
            step2Content.classList.remove('d-none');
            step2Indicator.classList.add('active');
            progressBar.style.width = '66%';
            progressBar.setAttribute('aria-valuenow', '66');
            prevBtn.disabled = false;
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        } else if (currentStep === 3) {
            step3Content.classList.remove('d-none');
            step3Indicator.classList.add('active');
            progressBar.style.width = '100%';
            progressBar.setAttribute('aria-valuenow', '100');
            prevBtn.disabled = false;
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        }
    }
    
    // Validate current step
    function validateCurrentStep() {
        if (currentStep === 1) {
            // Validate ID and selfie photos
            if (!idPhoto.files || !idPhoto.files[0]) {
                showAlert('Please upload your ID photo', 'error');
                return false;
            }
            
            if (!selfiePhoto.files || !selfiePhoto.files[0]) {
                showAlert('Please upload your selfie with ID', 'error');
                return false;
            }
            
            return true;
        } else if (currentStep === 2) {
            // Validate transportation method
            if (!transportationMethod.value) {
                showAlert('Please select a transportation method', 'error');
                return false;
            }
            
            // Validate transportation details based on selected method
            if (transportationMethod.value === 'vehicle') {
                const vehicleType = document.getElementById('vehicleType');
                if (vehicleType.value === '') {
                    showAlert('Please select a vehicle type', 'error');
                    return false;
                }
            } else if (transportationMethod.value === 'walking') {
                const serviceRadius = document.getElementById('serviceRadius');
                if (!serviceRadius.value) {
                    showAlert('Please enter your service radius', 'error');
                    return false;
                }
            } else if (transportationMethod.value === 'commute') {
                const transitType = document.getElementById('transitType');
                const transitRadius = document.getElementById('transitRadius');
                
                if (transitType.value === '') {
                    showAlert('Please select a transit type', 'error');
                    return false;
                }
                
                if (!transitRadius.value) {
                    showAlert('Please enter your transit service radius', 'error');
                    return false;
                }
            }
            
            return true;
        } else if (currentStep === 3) {
            // Validate at least one service category is selected
            if (selectedSubcategories.size === 0) {
                showAlert('Please select at least one service subcategory', 'error');
                return false;
            }
            
            return true;
        }
        
        return true;
    }
    
    // Handle form submission
    function handleFormSubmit(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        // Create FormData object
        const formData = new FormData(runnerForm);
        
        // Add selected subcategories to form data
        let subcategoriesArray = [];
        selectedSubcategories.forEach(item => {
            subcategoriesArray.push(`${item.category}:${item.subcategory}`);
        });
        
        formData.append('selected_subcategories', JSON.stringify(Array.from(subcategoriesArray)));
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // AJAX form submission
        fetch('../database/db_runner.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Application';
            
            if (data.success) {
                showAlert('Your runner application has been submitted successfully!', 'success');
                setTimeout(() => {
                    // Close modal and reset form
                    const modal = bootstrap.Modal.getInstance(document.getElementById('verifyRunnerModal'));
                    if (modal) {
                        modal.hide();
                    }
                    runnerForm.reset();
                    currentStep = 1;
                    updateStepDisplay();
                }, 2000);
            } else {
                showAlert(data.message || 'There was an error processing your application. Please try again.', 'error');
            }
        })
        .catch(error => {
            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Application';
            
            console.error('Error:', error);
            showAlert('There was an error connecting to the server. Please try again later.', 'error');
        });
    }
    
    // Show Sweet Alert
    function showAlert(message, type) {
        const icon = type === 'success' ? 'success' : 'error';
        
        Swal.fire({
            icon: icon,
            title: type === 'success' ? 'Success!' : 'Error!',
            text: message,
            confirmButtonColor: '#0d6efd'
        });
    }
    
    // Initialize: call showTransportationDetails once to set up initial state
    if (transportationMethod.value) {
        showTransportationDetails(transportationMethod.value);
    }
  });