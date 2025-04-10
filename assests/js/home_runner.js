
    /**
 * Pa-utos - Main JavaScript
 * Handles sidebar functionality, responsive behavior, and UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
  // Cache DOM elements
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const body = document.body;
  
  // State variables
  let isSidebarOpen = false;
  let isTransitioning = false;
  
  // Detect device type
  const isDesktop = () => window.innerWidth >= 992;
  
  // Set initial state based on device
  if (isDesktop()) {
    isSidebarOpen = true;
    sidebar.classList.add('show');
    sidebar.setAttribute('aria-hidden', 'false');
    sidebarToggle.setAttribute('aria-expanded', 'true');
  } else {
    isSidebarOpen = false;
    sidebar.setAttribute('aria-hidden', 'true');
    sidebarToggle.setAttribute('aria-expanded', 'false');
  }
  
  /**
   * Toggle sidebar visibility
   * @param {boolean|undefined} force - Force a specific state (optional)
   */
  function toggleSidebar(force) {
    // Prevent rapid toggling during transition
    if (isTransitioning) return;
    
    isTransitioning = true;
    
    // Determine new state
    const newState = (force !== undefined) ? force : !isSidebarOpen;
    isSidebarOpen = newState;
    
    // Update UI
    if (isSidebarOpen) {
      sidebar.classList.add('show');
      sidebarToggle.classList.add('active');
      sidebarOverlay.classList.add('show');
      sidebar.setAttribute('aria-hidden', 'false');
      sidebarOverlay.setAttribute('aria-hidden', 'false');
    } else {
      sidebar.classList.remove('show');
      sidebarToggle.classList.remove('active');
      sidebarOverlay.classList.remove('show');
      sidebar.setAttribute('aria-hidden', 'true');
      sidebarOverlay.setAttribute('aria-hidden', 'true');
    }
    
    // Update ARIA attributes
    sidebarToggle.setAttribute('aria-expanded', isSidebarOpen);
    
    // Update body class for desktop
    if (isDesktop()) {
      if (isSidebarOpen) {
        body.classList.remove('sidebar-closed');
      } else {
        body.classList.add('sidebar-closed');
      }
    }
    
    // Store state in localStorage
    try {
      localStorage.setItem('sidebarOpen', isSidebarOpen);
    } catch (e) {
      console.warn('Failed to save sidebar state to localStorage', e);
    }
    
    // Reset transition flag after animation completes
    setTimeout(() => {
      isTransitioning = false;
    }, 310); // Slightly longer than transition duration
  }
  
  // Toggle sidebar when clicking the button
  sidebarToggle.addEventListener('click', function(e) {
    e.preventDefault();
    toggleSidebar();
  });
  
  // Close sidebar when clicking the overlay
  sidebarOverlay.addEventListener('click', function() {
    if (isSidebarOpen) {
      toggleSidebar(false);
    }
  });
  
  // Handle keyboard navigation
  document.addEventListener('keydown', function(e) {
    // Close sidebar on Escape key
    if (e.key === 'Escape' && isSidebarOpen && !isDesktop()) {
      toggleSidebar(false);
    }
  });
  
  // Touch events for swipe to close
  let touchStartX = 0;
  let touchStartY = 0;
  
  sidebar.addEventListener('touchstart', function(e) {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
  }, { passive: true });
  
  sidebar.addEventListener('touchmove', function(e) {
    if (!isSidebarOpen) return;
    
    const touchX = e.touches[0].clientX;
    const touchY = e.touches[0].clientY;
    const diffX = touchStartX - touchX;
    const diffY = Math.abs(touchStartY - touchY);
    
    // Only handle horizontal swipes (ignore vertical scrolling)
    if (diffX > 50 && diffY < 30) {
      e.preventDefault();
      toggleSidebar(false);
    }
  }, { passive: false });
  
  // Handle window resize
  let resizeTimer;
  window.addEventListener('resize', function() {
    // Debounce resize events
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      const wasDesktop = isSidebarOpen;
      const nowDesktop = isDesktop();
      
      // Handle transition between mobile and desktop views
      if (wasDesktop !== nowDesktop) {
        if (nowDesktop) {
          // Transitioning to desktop
          toggleSidebar(true);
        } else {
          // Transitioning to mobile
          toggleSidebar(false);
        }
      }
    }, 100);
  });
  
  // Check for saved state on page load
  try {
    const savedState = localStorage.getItem('sidebarOpen');
    
    if (savedState === 'true' && isDesktop()) {
      toggleSidebar(true);
    } else if (savedState === 'false' && isDesktop()) {
      toggleSidebar(false);
    }
  } catch (e) {
    console.warn('Error loading saved sidebar state', e);
  }
  
  // Initialize any other UI components
  
  // Example: Initialize tooltips if Bootstrap's tooltip is used
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }
});

// Initialize and enhance the How It Works carousel
document.addEventListener('DOMContentLoaded', function() {
  // Get the carousel element
  const howItWorksCarousel = document.getElementById('howItWorksCarousel');
  
  if (howItWorksCarousel) {
    // Initialize the carousel with Bootstrap
    const carousel = new bootstrap.Carousel(howItWorksCarousel, {
      interval: 5000,  // Change slides every 5 seconds
      wrap: true,      // Continuous loop
      touch: true,     // Enable touch swiping on mobile
      pause: 'hover'   // Pause on mouse hover
    });
    
    // Pause carousel when page is not visible to save resources
    document.addEventListener('visibilitychange', function() {
      if (document.hidden) {
        carousel.pause();
      } else {
        carousel.cycle();
      }
    });
    
    // Add swipe support for mobile with better sensitivity
    let touchStartX = 0;
    let touchEndX = 0;
    
    howItWorksCarousel.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    howItWorksCarousel.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
      const swipeThreshold = 50; // Minimum distance for a swipe
      if (touchEndX < touchStartX - swipeThreshold) {
        // Swipe left, go to next slide
        carousel.next();
      } else if (touchEndX > touchStartX + swipeThreshold) {
        // Swipe right, go to previous slide
        carousel.prev();
      }
    }
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
      if (document.activeElement === howItWorksCarousel || 
          howItWorksCarousel.contains(document.activeElement)) {
        if (e.key === 'ArrowLeft') {
          carousel.prev();
          e.preventDefault();
        } else if (e.key === 'ArrowRight') {
          carousel.next();
          e.preventDefault();
        }
      }
    });
    
    // Add focus management for accessibility
    const carouselControls = howItWorksCarousel.querySelectorAll('.carousel-control-prev, .carousel-control-next');
    carouselControls.forEach(control => {
      control.addEventListener('focus', function() {
        carousel.pause();
      });
      
      control.addEventListener('blur', function() {
        carousel.cycle();
      });
    });
    
    // Add animation class when slide changes
    howItWorksCarousel.addEventListener('slide.bs.carousel', function() {
      const activeCard = howItWorksCarousel.querySelector('.carousel-item.active .card');
      if (activeCard) {
        activeCard.classList.add('animate__animated', 'animate__pulse');
        setTimeout(() => {
          activeCard.classList.remove('animate__animated', 'animate__pulse');
        }, 1000);
      }
    });
  }
  
  // geolocation upon login
  
});

// Add this to your existing scripts.js file
