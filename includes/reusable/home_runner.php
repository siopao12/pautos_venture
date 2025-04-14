<section class="runner-search mb-5">
  <div class="row align-items-center mb-4">
    <div class="col-lg-6">
      <?php
        $area = 'your area'; // Default fallback

        if (isset($_SESSION['user_location'])) {
          $locationType = $_SESSION['user_location']['type'];

          if ($locationType === 'address' && !empty($_SESSION['user_location']['address'])) {
            // Use only the first part of the address (e.g., barangay or street)
            $addressParts = explode(',', $_SESSION['user_location']['address']);
            $area = htmlspecialchars(trim($addressParts[0]));
          } elseif ($locationType === 'coordinates') {
            // Placeholder for reverse geocoding
            $area = 'your location'; // Optionally: "based on your pin"
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
</section>


      <!-- Runners Grid -->
      <div class="row g-4">
        <!-- Runner Cards -->
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="Boy D. Abunda profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">Boy D. Abunda</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.2 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.8 (150 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">View Profile</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="Mia R. Santos profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">Mia R. Santos</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.2 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.8 (150 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                  <span class="badge badge-primary-outline">Fast E-Bike Service</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">Book Now</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="Ash Ford S. Sama profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">Ash Ford S. Sama</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.2 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.8 (150 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                  <span class="badge badge-primary-outline">Fast Motorcycle Service</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">Book Now</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="Datu B. Sanmao profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">Datu B. Sanmao</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.2 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.8 (150 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                  <span class="badge badge-primary-outline">Fast Motorcycle Service</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">Book Now</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="John Doe profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">John Doe</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.5 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.7 (120 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">Book Now</button>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="card h-100 runner-card">
            <div class="card-body">
              <div class="d-flex gap-3 mb-3">
                <img src="../assests/image/profile.jpg" alt="Jane Smith profile" class="runner-img">
                <div>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="mb-0 fw-semibold fs-5">Jane Smith</h3>
                    <span class="verified-badge">
                      <i class="bi bi-patch-check-fill text-primary me-1"></i>Verified
                    </span>
                  </div>
                  <div class="d-flex align-items-center text-muted small mt-1">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span>1.8 km away</span>
                  </div>
                  <div class="d-flex align-items-center mt-1">
                    <i class="bi bi-star-fill star-filled me-1"></i>
                    <span class="small">4.9 (200 reviews)</span>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <div class="small fw-medium mb-1">Services:</div>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge badge-primary-outline">Package Delivery</span>
                  <span class="badge badge-primary-outline">Grocery Shopping</span>
                  <span class="badge badge-primary-outline">Document Delivery</span>
                </div>
              </div>
              <div>
                <div class="small fw-medium mb-1">Estimated:</div>
                <div class="small text-muted">₱50+ | 20-30 minutes</div>
              </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0">
              <button class="btn btn-primary w-100">Book Now</button>
            </div>
          </div>
        </div>
      </div>