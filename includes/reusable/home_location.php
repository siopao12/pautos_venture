<!-- Location Modal -->
<div class="modal fade" id="locationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationModalLabel">Share Your Location</h5>
      </div>
      <div class="modal-body">
        <p>To help you find the closest runners in your area, we need your location.</p>
        
        <!-- Location Information Alert -->
        <div id="locationInfo" class="alert alert-info mb-3" style="display: none;">
          <i class="bi bi-info-circle me-2"></i><span id="locationText">Default map location shown. Please select your exact location.</span>
        </div>
        
        <!-- Map Container - Positioned at the top for immediate visibility -->
        <div id="locationMap" style="height: 300px; width: 100%; margin-bottom: 15px; border-radius: 5px; display: none;" class="border"></div>
        
        <!-- Instructions for pin dragging -->
        <div class="alert alert-primary mb-3">
          <i class="bi bi-hand-index-thumb me-2"></i>You can drag the pin to your exact location on the map.
        </div>
        
        <p>Select your location by:</p>
        <div class="d-grid gap-3 mb-3">
          <button type="button" id="autoLocationBtn" class="btn btn-primary">
            <i class="bi bi-geo-alt me-2"></i>Use My Current Location
          </button>
          
          <div class="input-group">
            <input type="text" id="manualLocationInput" class="form-control" placeholder="Or enter your address manually...">
            <button class="btn btn-outline-secondary" type="button" id="submitManualLocation">Submit</button>
          </div>
          
          <!-- Confirm button for when user has adjusted the pin -->
          <button type="button" id="confirmPinLocationBtn" class="btn btn-success" style="display: none;">
            <i class="bi bi-check-circle me-2"></i>Confirm This Location
          </button>
        </div>
        
        <div class="alert alert-info small">
          <i class="bi bi-info-circle me-2"></i>Your location helps us show you the closest runners. You can update it anytime from your profile.
        </div>
      </div>
    </div>
  </div>
</div>