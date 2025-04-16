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
                    <input type="number" class="form-control" id="serviceRadius" name="service_radius" placeholder="Service Radius" min="1" max="50">
                    <label for="serviceRadius">Service Radius (km)</label>
                  </div>
                  <small class="text-muted">Maximum distance you're willing to travel on foot</small>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="walkingZipcode" name="walking_zipcode" placeholder="Primary ZIP/Postal Code">
                    <label for="walkingZipcode">Primary ZIP/Postal Code</label>
                  </div>
                  <small class="text-muted">Your primary service area ZIP/Postal code</small>
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
                      <option value="Bus">Bus</option>
                      <option value="Subway">Subway/Metro</option>
                      <option value="Train">Train</option>
                      <option value="Tram">Tram/Light Rail</option>
                      <option value="Multiple">Multiple Transit Types</option>
                    </select>
                    <label for="transitType">Preferred Transit Type</label>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="transitRadius" name="transit_radius" placeholder="Service Radius" min="1" max="100">
                    <label for="transitRadius">Service Radius (km)</label>
                  </div>
                  <small class="text-muted">Maximum distance you're willing to travel via public transit</small>
                </div>
                
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <textarea class="form-control" id="transitRoutes" name="transit_routes" placeholder="Common Transit Routes" style="height: 100px"></textarea>
                    <label for="transitRoutes">Common Transit Routes/Lines</label>
                  </div>
                  <small class="text-muted">List transit lines or routes you commonly use (e.g., "Red Line, Bus 42, Blue Line")</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 3: Service Categories -->
          <div class="step-content d-none" id="step3">
            <h5 class="mb-4 text-secondary">Step 3: Choose Service Categories</h5>
            <p class="text-muted mb-4">Select the categories of services you're willing to provide as a runner:</p>
            
            <div class="row g-3">
              <!-- Cleaning Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="cleaning" id="cleaningCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="cleaningCategory">Cleaning</label>
                    </div>
                    <p class="text-muted small">House cleaning, disinfecting, office cleaning, etc.</p>
                  </div>
                </div>
              </div>
              
              <!-- Shopping + Delivery Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="delivery" id="deliveryCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="deliveryCategory">Shopping + Delivery</label>
                    </div>
                    <p class="text-muted small">Grocery shopping, medicine pickup, food delivery, etc.</p>
                  </div>
                </div>
              </div>
              
              <!-- Babysitter Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="babysitter" id="babysitterCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="babysitterCategory">Babysitter</label>
                    </div>
                    <p class="text-muted small">Child care, baby sitting, homework help, etc.</p>
                  </div>
                </div>
              </div>
              
              <!-- Personal Assistant Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="assistant" id="assistantCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="assistantCategory">Personal Assistant</label>
                    </div>
                    <p class="text-muted small">Admin tasks, scheduling, research, etc.</p>
                  </div>
                </div>
              </div>
              
              <!-- Senior Citizen Assistance Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="senior" id="seniorCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="seniorCategory">Senior Citizen Assistance</label>
                    </div>
                    <p class="text-muted small">Medication reminders, companionship, meal prep, etc.</p>
                  </div>
                </div>
              </div>
              
              <!-- Pet Care Services Category -->
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 category-card">
                  <div class="card-body">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="petcare" id="petcareCategory" name="categories[]">
                      <label class="form-check-label fw-bold" for="petcareCategory">Pet Care Services</label>
                    </div>
                    <p class="text-muted small">Pet sitting, dog walking, pet grooming, etc.</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="alert alert-info mt-4">
              <i class="bi bi-info-circle me-2"></i>
              Please choose at least one category. You can update your service categories later.
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