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