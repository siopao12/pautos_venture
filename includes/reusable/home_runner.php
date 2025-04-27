<section class="runner-search mb-5">
  <div class="row align-items-center mb-4">
    <div class="col-lg-6">
      <?php
        $area = 'your area'; // Default fallback

        if (isset($_SESSION['user_location'])) {
          // Check if we have a formatted address
          if (!empty($_SESSION['user_location']['address'])) {
            // Extract the most relevant part of the address
            $addressParts = explode(',', $_SESSION['user_location']['address']);
            // Use the second part if available (usually city/municipality) or the first part
            $area = htmlspecialchars(trim(isset($addressParts[1]) ? $addressParts[1] : $addressParts[0]));
          } else {
            $area = 'your current location';
          }
        }
      ?>
      <h2 class="fw-semibold">Top Runners in <?= $area ?></h2>
    </div>
    <div class="col-lg-6">
      <div class="d-flex flex-column flex-sm-row gap-2 mt-3 mt-lg-0">
        <input type="text" class="form-control" placeholder="Search runners..." aria-label="Search runners">
        <select class="form-select" style="width: auto;" aria-label="Sort runners">
          <option value="recommended">Recommended</option>
          <option value="rating">Highest Rating</option>
          <option value="price">Lowest Price</option>
        </select>
      </div>
    </div>
  </div>
  
  <!-- Added container for runner cards -->
  <div class="row g-4" id="mainRunnersContainer">
    <!-- Loading indicator -->
    <div class="col-12 text-center py-4" id="mainLoadingIndicator">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading available runners...</p>
    </div>
    
    <!-- No runners message -->
    <div class="col-12 text-center py-4 d-none" id="mainNoRunnersMessage">
      <i class="bi bi-person-x fs-1 text-muted"></i>
      <p class="mt-2">No runners available at the moment</p>
    </div>
    
    <!-- Runner cards will be dynamically inserted here -->
  </div>
</section>

<!-- Online Runners Modal -->
<div class="modal fade" id="onlineRunnersModal" tabindex="-1" aria-labelledby="onlineRunnersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="onlineRunnersModalLabel">Available Runners</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Search bar -->
        <div class="mb-3">
          <div class="input-group">
            <input type="text" class="form-control" id="runnerSearchInput" placeholder="Search by name, service, or location...">
            <button class="btn btn-outline-secondary" type="button" id="refreshRunnersBtn">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
        </div>
        
        <!-- Runners container -->
        <div id="runnersListContainer">
          <!-- Loading indicator -->
          <div class="text-center py-4" id="runnersLoadingIndicator">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading available runners...</p>
          </div>
          
          <!-- No runners message -->
          <div class="text-center py-4 d-none" id="noRunnersMessage">
            <i class="bi bi-person-x fs-1 text-muted"></i>
            <p class="mt-2">No runners available at the moment</p>
          </div>
          
          <!-- Runner cards will be inserted here -->
          <div class="row g-3" id="runnersList">
            <!-- Dynamic content goes here -->
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Errand Task Booking Modal -->
<div class="modal fade" id="errandTaskModal" tabindex="-1" aria-labelledby="errandTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="errandTaskModalLabel">
          <i class="bi bi-clipboard-check me-2"></i>Create an Errand Task
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4">
        <!-- Progress bar -->
        <div class="progress mb-4" style="height: 5px;">
          <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="bookingProgressBar"></div>
        </div>
        
        <p class="text-muted d-flex align-items-center mb-4">
          <i class="bi bi-info-circle-fill text-primary me-2 fs-5"></i>
          Fill in the details below to create your errand task
        </p>
        
        <form id="errandTaskForm">
          <div class="row">
            <!-- Left column -->
            <div class="col-md-6">
              <!-- Errand Title -->
              <div class="mb-4">
                <label for="errandTitle" class="form-label fw-semibold">
                  <i class="bi bi-tag me-1 text-primary"></i>Errand Title
                </label>
                <input type="text" class="form-control form-control-lg border-0 bg-light" id="errandTitle" required placeholder="e.g., Grocery Shopping">
              </div>
              
              <!-- Task Description -->
              <div class="mb-4">
                <label for="taskDescription" class="form-label fw-semibold">
                  <i class="bi bi-card-text me-1 text-primary"></i>Task Description
                </label>
                <textarea class="form-control border-0 bg-light" id="taskDescription" rows="4" required placeholder="Describe what you need help with..."></textarea>
              </div>
              
              <!-- Category -->
              <div class="mb-4">
                <label for="taskCategory" class="form-label fw-semibold">
                  <i class="bi bi-grid me-1 text-primary"></i>Category
                </label>
                <select class="form-select border-0 bg-light" id="taskCategory" required>
                  <option value="" selected disabled>Select a category</option>
                  <option value="cleaning">Cleaning</option>
                  <option value="shopping-delivery">Shopping & Delivery</option>
                  <option value="babysitter">Babysitter</option>
                  <option value="personal-assistant">Personal Assistant</option>
                  <option value="senior-assistance">Senior Assistance</option>
                  <option value="pet-care">Pet Care</option>
                </select>
              </div>
              
              <!-- Subcategories -->
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-list-check me-1 text-primary"></i>Subcategories
                </label>
                <div id="subcategoriesContainer" class="border-0 bg-light rounded p-3" style="max-height: 200px; overflow-y: auto;">
                  <p class="text-muted small">Please select a category first</p>
                </div>
              </div>
            </div>
            
            <!-- Right column -->
            <div class="col-md-6">
              <!-- Location -->
              <div class="mb-4">
                <label for="taskLocation" class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1 text-primary"></i>Location
                </label>
                <div class="input-group">
                  <input type="text" class="form-control border-0 bg-light" id="taskLocation" required placeholder="Enter task location">
                  <button class="btn btn-primary" type="button" id="useMyLocationBtn">
                    <i class="bi bi-geo-alt-fill me-1"></i>Use My Location
                  </button>
                </div>
              </div>
              
              <!-- Schedule Date/Time -->
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="bi bi-calendar-event me-1 text-primary"></i>Schedule
                </label>
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group mb-2">
                      <span class="input-group-text bg-light border-0"><i class="bi bi-calendar"></i></span>
                      <input type="date" class="form-control border-0 bg-light" id="taskDate" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-group">
                      <span class="input-group-text bg-light border-0"><i class="bi bi-clock"></i></span>
                      <input type="time" class="form-control border-0 bg-light" id="taskTime" required>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Special Instructions -->
              <div class="mb-4">
                <label for="specialInstructions" class="form-label fw-semibold">
                  <i class="bi bi-info-circle me-1 text-primary"></i>Special Instructions
                  <span class="badge bg-warning text-dark rounded-pill ms-1">Optional</span>
                </label>
                <textarea class="form-control border-0 bg-light" id="specialInstructions" rows="3" placeholder="Brand preferences, specific details, etc."></textarea>
              </div>
              
              <!-- Photo Upload -->
              <div class="mb-4">
                <label for="taskPhotoUpload" class="form-label fw-semibold">
                  <i class="bi bi-camera me-1 text-primary"></i>Photos
                  <span class="badge bg-warning text-dark rounded-pill ms-1">Optional</span>
                </label>
                <div class="card border-dashed" style="border: 2px dashed #dee2e6; background-color: #f8f9fa;">
                  <div class="card-body text-center py-3">
                    <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-2"></i>
                    <p class="mb-1">Drag & drop files here or</p>
                    <label for="taskPhotoUpload" class="btn btn-sm btn-outline-primary">Browse Files</label>
                    <input class="d-none" type="file" id="taskPhotoUpload" accept="image/*" multiple>
                    <p class="text-muted small mb-0">Max 5 images, 5MB each</p>
                  </div>
                </div>
                <div id="photoPreviewContainer" class="mt-2 d-flex flex-wrap gap-2"></div>
              </div>
            </div>
          </div>
          
          <!-- Payment section -->
          <div class="mt-4">
            <h5 class="mb-3 border-bottom pb-2">
              <i class="bi bi-credit-card me-2 text-primary"></i>Payment Details
            </h5>
            <div class="row">
              
              <!-- Estimated Cost -->
            <div class="col-md-6 mb-3">
              <label for="estimatedCost" class="form-label fw-semibold">Estimated Cost</label>
              <div class="input-group">
                <span class="input-group-text bg-light border-0">₱</span>
                <input type="number" class="form-control border-0 bg-light" id="estimatedCost" min="0" step="0.01" required>
              </div>
              <div class="mt-2" id="costBreakdown">
                <!-- Content will be populated by JavaScript -->
              </div>
              <small class="text-muted">Amount may change based on actual service</small>
            </div>
              
              <!-- Payment Method -->
              <div class="col-md-6 mb-3">
                <label for="paymentMethod" class="form-label fw-semibold">Payment Method</label>
                <div class="d-flex flex-wrap gap-2">
                  <input type="radio" class="btn-check" name="paymentMethod" id="credit_card" value="credit_card" required>
                  <label class="btn btn-outline-primary" for="credit_card">
                    <i class="bi bi-credit-card me-1"></i>Credit Card
                  </label>
                  
                  <input type="radio" class="btn-check" name="paymentMethod" id="debit_card" value="debit_card">
                  <label class="btn btn-outline-primary" for="debit_card">
                    <i class="bi bi-credit-card me-1"></i>Debit Card
                  </label>
                  
                  <input type="radio" class="btn-check" name="paymentMethod" id="paypal" value="paypal">
                  <label class="btn btn-outline-primary" for="paypal">
                    <i class="bi bi-paypal me-1"></i>PayPal
                  </label>
                  
                  <input type="radio" class="btn-check" name="paymentMethod" id="cash" value="cash">
                  <label class="btn btn-outline-primary" for="cash">
                    <i class="bi bi-cash me-1"></i>Cash
                  </label>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Runner ID (hidden) -->
          <input type="hidden" id="selectedRunnerId">
        </form>
      </div>
      
      <div class="modal-footer border-0 justify-content-between bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        <div>
          <button type="button" class="btn btn-outline-primary me-2" id="saveTaskBtn">
            <i class="bi bi-save me-1"></i>Save Draft
          </button>
          <button type="button" class="btn btn-primary" id="submitTaskBtn">
            <i class="bi bi-check-circle me-1"></i>Submit Request
          </button>
        </div>
      </div>
    </div>
  </div>
  
</div>

<style>
/* Custom styling for the booking modal */
.border-dashed {
  border-style: dashed !important;
  border-width: 2px !important;
  border-color: #dee2e6 !important;
}

/* Custom radio buttons */
.btn-check:checked + .btn-outline-primary {
  background-color: var(--bs-primary);
  color: white;
}

/* Animate progress bar */
@keyframes progressAnimation {
  0% { width: 0%; }
  20% { width: 20%; }
  40% { width: 40%; }
  60% { width: 60%; }
  80% { width: 80%; }
  100% { width: 100%; }
}

#bookingProgressBar {
  animation: progressAnimation 2s ease-out forwards;
}

/* Custom scrollbar for subcategories */
#subcategoriesContainer::-webkit-scrollbar {
  width: 6px;
}

#subcategoriesContainer::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

#subcategoriesContainer::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

#subcategoriesContainer::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Image preview styling */
.image-preview {
  position: relative;
  margin-right: 10px;
  margin-bottom: 10px;
}

.image-preview img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.image-preview .remove-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: #dc3545;
  color: white;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  cursor: pointer;
  border: 2px solid white;
}
</style>