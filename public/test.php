<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Google Map in Modal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #map {
      height: 400px;
      width: 100%;
    }
  </style>
</head>
<body>

<!-- Trigger Button -->
<div class="text-center mt-5">
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mapModal">Open Google Map</button>
</div>

<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mapModalLabel">Google Map</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>

<!-- Google Maps API (replace YOUR_API_KEY) -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCdjhhHxrq3PAHP2Om2wLcyGYCn9v8mqyk"></script>

<!-- Bootstrap + Map Init -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let map;

  const initMap = () => {
    map = new google.maps.Map(document.getElementById("map"), {
      center: { lat: 7.1907, lng: 125.4553 }, // Davao City as example
      zoom: 12,
    });
  };

  // Reinitialize map when modal opens
  const modal = document.getElementById('mapModal');
  modal.addEventListener('shown.bs.modal', () => {
    initMap();
  });
</script>

</body>
</html>
