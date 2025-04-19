<!-- Become a Runner Modal -->
<div class="modal fade" id="verifyRunnerModal" tabindex="-1" aria-labelledby="verifyRunnerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 shadow">
      <form id="runnerApplicationForm" enctype="multipart/form-data">
        <div class="modal-header border-bottom-0 pb-0">
          <h4 class="modal-title fw-bold text-primary" id="verifyRunnerModalLabel">Become a Runner</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body px-4">
          <!-- Step Progress Bar -->
          <div class="mb-4">
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-primary" role="progressbar" id="progressBar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <span class="step-indicator active" id="step1Indicator">ID Verification</span>
              <span class="step-indicator" id="step2Indicator">Transportation</span>
              <span class="step-indicator" id="step3Indicator">Service Categories</span>
            </div>
          </div>

          <!-- Step 1: Upload Valid ID -->
          <div class="step-content" id="step1">
            <h5 class="mb-4 text-secondary">Step 1: ID Verification</h5>
            <div class="mb-4">
              <div class="form-floating mb-2">
                <div class="upload-container p-4 border rounded-3 text-center position-relative" id="idUploadContainer">
                  <i class="bi bi-card-image fs-1 text-secondary mb-2"></i>
                  <p class="mb-2">Drop your ID (front view) here or</p>
                  <label for="idPhoto" class="btn btn-outline-primary px-4">Browse Files</label>
                  <input type="file" class="form-control d-none" id="idPhoto" name="id_photo" accept="image/*" required>
                  <div class="preview-container d-none" id="idPreview"></div>
                </div>
              </div>
              <small class="text-muted">Upload a clear photo of your valid government-issued ID</small>
            </div>
            
            <div class="mb-4">
              <div class="form-floating mb-2">
                <div class="upload-container p-4 border rounded-3 text-center position-relative" id="selfieUploadContainer">
                  <i class="bi bi-person-badge fs-1 text-secondary mb-2"></i>
                  <p class="mb-2">Drop your selfie with ID here or</p>
                  <label for="selfiePhoto" class="btn btn-outline-primary px-4">Browse Files</label>
                  <input type="file" class="form-control d-none" id="selfiePhoto" name="selfie_photo" accept="image/*" required>
                  <div class="preview-container d-none" id="selfiePreview"></div>
                </div>
              </div>
              <small class="text-muted">Upload a clear selfie of yourself holding your ID</small>
            </div>
          </div>

          <!-- Step 2: Transportation Method -->
          <div class="step-content d-none" id="step2">
            <h5 class="mb-4 text-secondary">Step 2: Transportation Method</h5>
            
            <!-- Transportation Method Selection -->
            <div class="mb-4">
              <p class="fw-bold mb-3">How will you provide your services?</p>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="card transportation-card" data-method="vehicle">
                    <div class="card-body text-center py-4">
                      <i class="bi bi-car-front fs-1 text-primary mb-3"></i>
                      <h6 class="fw-bold">Vehicle</h6>
                      <p class="small text-muted mb-0">Car, motorcycle, or other vehicle</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card transportation-card" data-method="walking">
                    <div class="card-body text-center py-4">
                      <i class="bi bi-person-walking fs-1 text-primary mb-3"></i>
                      <h6 class="fw-bold">Walking</h6>
                      <p class="small text-muted mb-0">On foot within your service area</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card transportation-card" data-method="commute">
                    <div class="card-body text-center py-4">
                      <i class="bi bi-bus-front fs-1 text-primary mb-3"></i>
                      <h6 class="fw-bold">Public Transit</h6>
                      <p class="small text-muted mb-0">Bus, train, or other public transportation</p>
                    </div>
                  </div>
                </div>
              </div>
              <input type="hidden" id="transportationMethod" name="transportation_method" required>
            </div>
            
            <!-- Vehicle Details (initially hidden) -->
            <div id="vehicleDetailsSection" class="d-none mt-4 pt-3 border-top">
              <h6 class="mb-4">Vehicle Details</h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select class="form-select" id="vehicleType" name="vehicle_type">
                      <option value="" selected disabled>Select one</option>
                      <option value="Motorcycle">Motorcycle</option>
                      <option value="E-Bike">E-Bike</option>
                      <option value="Bicycle">Bicycle</option>
                      <option value="Car">Car</option>
                      <option value="Van">Van</option>
                    </select>
                    <label for="vehicleType">Vehicle Type</label>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="registrationNumber" name="registration_number" placeholder="Registration Number">
                    <label for="registrationNumber">Registration Number</label>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="licenseNumber" name="license_number" placeholder="License Number">
                    <label for="licenseNumber">Driver's License Number</label>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="vehiclePhone" name="vehicle_phone" placeholder="Phone Number">
                    <label for="vehiclePhone">Phone Used with Vehicle</label>
                  </div>
                </div>
                
                <div class="col-12">
                  <div class="mb-3">
                    <div class="upload-container p-4 border rounded-3 text-center position-relative" id="vehiclePhotoUploadContainer">
                      <i class="bi bi-car-front-fill fs-1 text-secondary mb-2"></i>
                      <p class="mb-2">Drop a photo of your vehicle here or</p>
                      <label for="vehiclePhoto" class="btn btn-outline-primary px-4">Browse Files</label>
                      <input type="file" class="form-control d-none" id="vehiclePhoto" name="vehicle_photo" accept="image/*">
                      <div class="preview-container d-none" id="vehiclePhotoPreview"></div>
                    </div>
                    <small class="text-muted">Upload a clear photo of your vehicle</small>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Walking Details (initially hidden) -->
            <div id="walkingDetailsSection" class="d-none mt-4 pt-3 border-top">
              <h6 class="mb-4">Service Area Details</h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <!-- After -->
<input type="number" class="form-control" id="serviceRadius" name="service_radius" placeholder="Service Radius" min="1" max="50" data-required>

                    <label for="serviceRadius">Service Radius (km)</label>
                  </div>
                  <small class="text-muted">Maximum distance you're willing to travel on foot</small>
                </div>
              </div>
            </div>
            
            <!-- Commute Details (initially hidden) -->
            <div id="commuteDetailsSection" class="d-none mt-4 pt-3 border-top">
              <h6 class="mb-4">Public Transit Details</h6>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <select class="form-select" id="transitType" name="transit_type">
                      <option value="" selected disabled>Select preferred transit type</option>
                      <option value="Motorcycle">Motorcycle</option>
                      <option value="Tricycle">Tricycle</option>
                      <option value="Jeepney">Jeepney</option>
                      <option value="Taxi">Taxi</option>
                      <option value="Multiple">Multiple Transit Types</option>
                    </select>
                    <label for="transitType">Preferred Transit Type</label>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="transitRadius" name="transit_radius" placeholder="Service Radius" min="1" max="100" data-required>
                    <!-- After -->
                    <label for="transitRadius">Service Radius (km)</label>
                  </div>
                  <small class="text-muted">Maximum distance you're willing to travel via public transit</small>
                </div>
              </div>
            </div>
          </div>

      <!-- Add this to step 3 of your runner application form -->
<div id="step3" class="step-content d-none">
  <h4 class="mb-4">Service Categories</h4>
  
  <div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i> Please choose at least one category. You can update your service categories later.
  </div>
  
  <!-- Hidden input to track selected main category -->
  <input type="hidden" id="selectedMainCategory" name="selectedMainCategory" value="">
  
  <div class="row g-3 mb-4">
    <!-- Main Category Cards - Using the same styling as transportation cards -->
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="cleaning">
        <div class="card-body text-center p-3">
          <i class="bi bi-house-check text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Cleaning</h5>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="shopping-delivery">
        <div class="card-body text-center p-3">
          <i class="bi bi-cart3 text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Shopping + Delivery</h5>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="babysitter">
        <div class="card-body text-center p-3">
          <i class="bi bi-people text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Babysitter</h5>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="personal-assistant">
        <div class="card-body text-center p-3">
          <i class="bi bi-briefcase text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Personal Assistant</h5>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="senior-assistance">
        <div class="card-body text-center p-3">
          <i class="bi bi-heart-pulse text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Senior Assistance</h5>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 col-sm-6">
      <div class="card service-category-card h-100" data-category="pet-care">
        <div class="card-body text-center p-3">
          <i class="bi bi-piggy-bank text-primary mb-3" style="font-size: 2rem;"></i>
          <h5 class="card-title mb-0">Pet Care</h5>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Category Detail Sections - Following the same pattern as transportation details -->
  <!-- Cleaning Details Section -->
  <div id="cleaningDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Cleaning Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific cleaning services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Shopping & Delivery Details Section -->
  <div id="shopping-deliveryDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Shopping & Delivery Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific shopping and delivery services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Babysitter Details Section -->
  <div id="babysitterDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Babysitting Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific childcare services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Personal Assistant Details Section -->
  <div id="personal-assistantDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Personal Assistant Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific assistant services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Senior Assistance Details Section -->
  <div id="senior-assistanceDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Senior Assistance Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific senior care services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Pet Care Details Section -->
  <div id="pet-careDetailsSection" class="category-details d-none mb-4">
    <div class="card border-primary">
      <div class="card-header bg-primary bg-opacity-10 border-primary">
        <h5 class="mb-0">Pet Care Services</h5>
      </div>
      <div class="card-body">
        <p class="small text-muted mb-3">Select the specific pet care services you can provide:</p>
        <div class="subcategories-list">
          <!-- Checkboxes will be dynamically added here -->
        </div>
      </div>
    </div>
  </div>
  
  <!-- Selected Categories Summary -->
  <div class="card mt-4">
    <div class="card-header bg-light">
      <h5 class="mb-0">Selected Services</h5>
    </div>
    <div class="card-body">
      <div id="selectedCategoriesContainer">
        <div class="text-muted">No categories selected</div>
      </div>
    </div>
  </div>
</div>

        <!-- Modal Footer -->
        <div class="modal-footer border-top-0">
          <button type="button" class="btn btn-outline-secondary px-4" id="prevStep" disabled>
            <i class="bi bi-arrow-left me-1"></i> Back
          </button>
          <button type="button" class="btn btn-primary px-4" id="nextStep">
            Next <i class="bi bi-arrow-right ms-1"></i>
          </button>
          <button type="submit" class="btn btn-success px-4 d-none" id="submitRunnerBtn">
            <i class="bi bi-check-circle me-1"></i> Submit Application
          </button>
        </div>
      </form>
    </div>
  </div>
</div>