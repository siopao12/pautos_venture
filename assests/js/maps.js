document.addEventListener('DOMContentLoaded', function() {
  let map, marker;
  
  // Initialize map variables
  const mapContainer = document.getElementById('locationMap');
  const locationInfo = document.getElementById('locationInfo');
  const locationText = document.getElementById('locationText');
  const confirmPinBtn = document.getElementById('confirmPinLocationBtn');
  
  // Default coordinates for Davao City center
  const defaultLat = 7.0707;
  const defaultLng = 125.6087;
  
  // Track if location has been manually adjusted
  let locationAdjusted = false;
  
  // Function to initialize the map
  function initMap(lat, lng) {
    if (!map) {
      const location = { lat, lng };
      map = new google.maps.Map(mapContainer, {
        zoom: 15,
        center: location,
        mapTypeControl: false,
        streetViewControl: false
      });
      marker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Your location'
      });
      
      // Allow users to refine location by dragging the marker
      google.maps.event.addListener(marker, 'dragend', function() {
        const position = marker.getPosition();
        updateLocationDisplay(position.lat(), position.lng());
        
        // Store coordinates for submission and show confirm button
        confirmPinBtn.dataset.lat = position.lat();
        confirmPinBtn.dataset.lng = position.lng();
        confirmPinBtn.style.display = 'block';
        locationAdjusted = true;
      });
      
      // Also allow clicking on the map to move the marker
      google.maps.event.addListener(map, 'click', function(event) {
        marker.setPosition(event.latLng);
        updateLocationDisplay(event.latLng.lat(), event.latLng.lng());
        
        // Store coordinates for submission and show confirm button
        confirmPinBtn.dataset.lat = event.latLng.lat();
        confirmPinBtn.dataset.lng = event.latLng.lng();
        confirmPinBtn.style.display = 'block';
        locationAdjusted = true;
      });
    } else {
      // Update existing map
      map.setCenter({ lat, lng });
      marker.setPosition({ lat, lng });
    }
    
    // Show the map
    mapContainer.style.display = 'block';
    
    // Update displayed address
    updateLocationDisplay(lat, lng);
  }
  
  // Function to update the displayed address
  function updateLocationDisplay(lat, lng) {
    // Use reverse geocoding to get address from coordinates
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat, lng } }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const address = results[0].formatted_address;
        locationText.textContent = `Your location: ${address}`;
        locationInfo.style.display = 'block';
        
        // Store the address for the confirm button
        if (confirmPinBtn.style.display !== 'none') {
          confirmPinBtn.dataset.address = address;
        }
      } else {
        locationText.textContent = `Your location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        locationInfo.style.display = 'block';
      }
    });
  }
  
  // Show location modal only if location not set yet
  if (typeof hasLocation !== 'undefined' && !hasLocation) {
    const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
    
    // Initialize map with default Davao City location when modal opens
    locationModal._element.addEventListener('shown.bs.modal', function() {
      // Initialize map with default Davao City coordinates
      initMap(defaultLat, defaultLng);
    });
    
    locationModal.show();
  }
  
  // Auto location handler
  document.getElementById('autoLocationBtn').addEventListener('click', function () {
    const btn = this;
    
    if (!navigator.geolocation) {
      Swal.fire({
        icon: 'error',
        title: 'Location Not Supported',
        text: 'Your browser does not support geolocation.',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    // Show loading state
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Getting location...';
    btn.disabled = true;
    
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        // Update map with the obtained coordinates
        initMap(lat, lng);
        
        // Reset button
        btn.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Use My Current Location';
        btn.disabled = false;
        
        // Store coordinates for "confirm pin" button
        confirmPinBtn.dataset.lat = lat;
        confirmPinBtn.dataset.lng = lng;
        confirmPinBtn.style.display = 'block';
        
        // Show prompt to adjust pin if needed
        Swal.fire({
          icon: 'info',
          title: 'Location Found',
          text: 'We\'ve located you! If this isn\'t exact, you can drag the pin or click on the map to adjust your location.',
          confirmButtonText: 'Got it'
        });
      },
      (error) => {
        let message = "Unable to retrieve your location. ";
        switch (error.code) {
          case error.PERMISSION_DENIED: message += "You denied the request."; break;
          case error.POSITION_UNAVAILABLE: message += "Location info unavailable."; break;
          case error.TIMEOUT: message += "Request timed out."; break;
          default: message += "Unknown error."; break;
        }
        
        Swal.fire({ icon: 'error', title: 'Location Error', text: message, confirmButtonText: 'OK' });
        
        // Reset button
        btn.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Use My Current Location';
        btn.disabled = false;
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
  });
  
  // Manual location handler with autocomplete
  const manualLocationInput = document.getElementById('manualLocationInput');
  
  // Check if the Places library is loaded before using it
  if (typeof google !== 'undefined' && google.maps && google.maps.places) {
    const autocomplete = new google.maps.places.Autocomplete(manualLocationInput, {
      componentRestrictions: { country: 'ph' },  // Restrict to Philippines
      fields: ['formatted_address', 'geometry', 'address_components']
    });
    
    // Handle place selection from autocomplete
    autocomplete.addListener('place_changed', function() {
      const place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }
      
      const lat = place.geometry.location.lat();
      const lng = place.geometry.location.lng();
      
      // Update map with the selected place
      initMap(lat, lng);
      
      // Store coordinates for confirmation button
      confirmPinBtn.dataset.lat = lat;
      confirmPinBtn.dataset.lng = lng;
      confirmPinBtn.dataset.address = place.formatted_address;
      confirmPinBtn.style.display = 'block';
      
      // Show prompt to adjust pin if needed
      Swal.fire({
        icon: 'info',
        title: 'Location Found',
        text: 'We\'ve found your location! If this isn\'t exact, you can drag the pin or click on the map to adjust your location.',
        confirmButtonText: 'Got it'
      });
    });
  } else {
    console.error('Google Places library not loaded. Autocomplete will not work.');
  }
  
  // Manual location submit handler
  document.getElementById('submitManualLocation').addEventListener('click', function () {
    const btn = this;
    const manualLocation = document.getElementById('manualLocationInput').value.trim();
    
    if (manualLocation === '') {
      Swal.fire({ icon: 'warning', title: 'Empty Location', text: 'Please enter your address.', confirmButtonText: 'OK' });
      return;
    }
    
    // Geocode the manual address
    const geocoder = new google.maps.Geocoder();
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Finding location...';
    btn.disabled = true;
    
    geocoder.geocode({ address: manualLocation + ', Davao City, Philippines' }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const lat = results[0].geometry.location.lat();
        const lng = results[0].geometry.location.lng();
        
        // Update map with geocoded coordinates
        initMap(lat, lng);
        
        // Reset button
        btn.innerHTML = 'Submit';
        btn.disabled = false;
        
        // Store data for confirmation button
        confirmPinBtn.dataset.lat = lat;
        confirmPinBtn.dataset.lng = lng;
        confirmPinBtn.dataset.address = results[0].formatted_address;
        confirmPinBtn.style.display = 'block';
        
        // Show prompt to adjust pin if needed
        Swal.fire({
          icon: 'info',
          title: 'Location Found',
          text: 'We\'ve found your location! If this isn\'t exact, you can drag the pin or click on the map to adjust your location.',
          confirmButtonText: 'Got it'
        });
      } else {
        Swal.fire({ 
          icon: 'error', 
          title: 'Location Not Found', 
          text: 'Could not find that address. Please try again or use automatic location.', 
          confirmButtonText: 'OK' 
        });
        
        // Reset button
        btn.innerHTML = 'Submit';
        btn.disabled = false;
      }
    });
  });
  
  // Confirm pin location button handler
  document.getElementById('confirmPinLocationBtn').addEventListener('click', function() {
    const btn = this;
    
    if (!btn.dataset.lat || !btn.dataset.lng) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No location data available. Please try again.',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving location...';
    btn.disabled = true;
    
    // Use 'adjusted' type when the pin has been moved manually
    const locationType = locationAdjusted ? 'adjusted' : 'auto';
    
    // Get the current address from dataset or perform reverse geocoding if missing
    if (!btn.dataset.address) {
      const geocoder = new google.maps.Geocoder();
      const lat = parseFloat(btn.dataset.lat);
      const lng = parseFloat(btn.dataset.lng);
      
      geocoder.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK' && results[0]) {
          btn.dataset.address = results[0].formatted_address;
          sendLocationData();
        } else {
          // If geocoding fails, just send the coordinates without address
          sendLocationData();
        }
      });
    } else {
      sendLocationData();
    }
    
    function sendLocationData() {
      // Create the location data - now including both coordinates AND address
      const locationData = {
        action: 'save_location',
        type: locationType,
        lat: parseFloat(btn.dataset.lat),
        lng: parseFloat(btn.dataset.lng),
        address: btn.dataset.address || '' // Include the human-readable address
      };
      
      // Log the data being sent (helpful for debugging)
      console.log('Sending location data:', locationData);
      
      // Send the request
      fetch('../database/location.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(locationData)
      })
      .then(res => {
        // Log the raw response for debugging
        return res.text().then(text => {
          console.log('Raw response:', text);
          try {
            return JSON.parse(text);
          } catch (e) {
            throw new Error('Invalid JSON response: ' + text);
          }
        });
      })
      .then(data => {
        if (data.success) {
          // Close the modal - use safer method to get modal instance
          const modalElement = document.getElementById('locationModal');
          const modalInstance = bootstrap.Modal.getInstance(modalElement);
          if (modalInstance) {
            modalInstance.hide();
          }
          
          Swal.fire({ 
            icon: 'success', 
            title: 'Location Updated', 
            text: 'Your location has been saved.', 
            confirmButtonText: 'Great!' 
          }).then(() => window.location.reload());
        } else {
          throw new Error(data.message || 'Failed to save location.');
        }
      })
      .catch(error => {
        console.error('Error saving location:', error);
        Swal.fire({ 
          icon: 'error', 
          title: 'Error', 
          text: 'There was a problem: ' + error.message, 
          confirmButtonText: 'OK' 
        });
        
        // Reset button
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Confirm This Location';
        btn.disabled = false;
      });
    }
  });
});