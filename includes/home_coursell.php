 <!-- How It Works Section with Carousel -->
 <section class="how-it-works mb-5">
  <h2 class="fw-semibold mb-4">How It Works</h2>
  
  <!-- Desktop view (static cards for larger screens) -->
  <div class="d-none d-md-block">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="icon-circle bg-primary-light">
              <i class="bi bi-star-fill text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">1. Choose a Runner</h3>
            <p class="text-muted small">Select from our verified runners based on ratings, price, and skills</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="icon-circle bg-primary-light">
              <i class="bi bi-clock text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">2. Book Instantly</h3>
            <p class="text-muted small">Book right away or schedule your errand for a later time</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="icon-circle bg-primary-light">
              <i class="bi bi-check-circle text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">3. Track & Pay</h3>
            <p class="text-muted small">Chat with your runner, track progress, and pay securely in the app</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Mobile view (carousel for smaller screens) -->
  <div id="howItWorksCarousel" class="carousel slide d-md-none" data-bs-ride="carousel" data-bs-interval="5000" data-bs-touch="true">
    <!-- Carousel indicators -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#howItWorksCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Step 1: Choose a Runner"></button>
      <button type="button" data-bs-target="#howItWorksCarousel" data-bs-slide-to="1" aria-label="Step 2: Book Instantly"></button>
      <button type="button" data-bs-target="#howItWorksCarousel" data-bs-slide-to="2" aria-label="Step 3: Track & Pay"></button>
    </div>
    
    <!-- Carousel items -->
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="card h-100 mx-2">
          <div class="card-body p-4 text-center">
            <div class="icon-circle bg-primary-light mx-auto">
              <i class="bi bi-star-fill text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">1. Choose a Runner</h3>
            <p class="text-muted small">Select from our verified runners based on ratings, price, and skills</p>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="card h-100 mx-2">
          <div class="card-body p-4 text-center">
            <div class="icon-circle bg-primary-light mx-auto">
              <i class="bi bi-clock text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">2. Book Instantly</h3>
            <p class="text-muted small">Book right away or schedule your errand for a later time</p>
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="card h-100 mx-2">
          <div class="card-body p-4 text-center">
            <div class="icon-circle bg-primary-light mx-auto">
              <i class="bi bi-check-circle text-primary"></i>
            </div>
            <h3 class="fs-5 fw-semibold mb-2">3. Track & Pay</h3>
            <p class="text-muted small">Chat with your runner, track progress, and pay securely in the app</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Carousel controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#howItWorksCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#howItWorksCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</section>