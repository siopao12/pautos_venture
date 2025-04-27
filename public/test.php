<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enhanced Errand Request Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="container my-5">
  <form id="errandForm" enctype="multipart/form-data">
    <h3 class="mb-4">Create an Errand Task</h3>

    <!-- Errand Title -->
    <div class="mb-3">
      <label for="errandTitle" class="form-label">Errand Title</label>
      <input type="text" class="form-control" id="errandTitle" required>
    </div>

    <!-- Task Description -->
    <div class="mb-3">
      <label for="taskDescription" class="form-label">Task Description</label>
      <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
    </div>

    <!-- Category -->
    <div class="mb-3">
      <label for="category" class="form-label">Category</label>
      <select class="form-select" id="category" required>
        <option value="">Select Category</option>
        <option value="1">Cleaning</option>
        <option value="2">Shopping + Delivery</option>
        <option value="3">Babysitter</option>
        <option value="4">Personal Assistant</option>
        <option value="5">Senior Assistance</option>
        <option value="6">Pet Care</option>
      </select>
    </div>

    <!-- Subcategories -->
    <div class="mb-3">
      <label class="form-label">Select Subcategories</label>
      <div id="subcategories" class="row"></div>
    </div>

    <!-- Location -->
    <div class="mb-3">
      <label for="location" class="form-label">Location</label>
      <input type="text" class="form-control" id="location" placeholder="e.g. 123 Mabini Street, Davao City" required>
    </div>

    <!-- Scheduled Date & Time -->
    <div class="mb-3">
      <label for="schedule" class="form-label">Scheduled Date & Time</label>
      <input type="datetime-local" class="form-control" id="schedule" required>
    </div>

    <!-- Special Instructions -->
    <div class="mb-3">
      <label for="specialInstructions" class="form-label">Special Instructions</label>
      <textarea class="form-control" id="specialInstructions" rows="4" placeholder="e.g., Buy size 9, white Adidas Ultraboost only at Abreeza branch. Include receipt if possible." required></textarea>
    </div>

    <!-- Upload Photo -->
    <div class="mb-3">
      <label for="uploadPhoto" class="form-label">Upload Photo (Optional)</label>
      <input type="file" class="form-control" id="uploadPhoto" accept="image/*">
      <small class="form-text text-muted">Attach a photo of the product, store, or document if needed.</small>
    </div>

    <!-- Estimated Cost & Payment Method -->
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="estimatedCost" class="form-label">Estimated Cost (₱)</label>
        <input type="number" class="form-control" id="estimatedCost" placeholder="₱0.00" required>
      </div>
      <div class="col-md-6">
        <label for="paymentMethod" class="form-label">Payment Method</label>
        <select class="form-select" id="paymentMethod" required>
          <option value="">Select Payment Method</option>
          <option value="gcash">GCash</option>
          <option value="cash">Cash</option>
          <option value="maya">Maya</option>
        </select>
      </div>
    </div>

    <!-- Submit Buttons -->
    <div class="d-flex justify-content-end gap-2">
      <button type="reset" class="btn btn-secondary">Reset</button>
      <button type="submit" class="btn btn-primary">Submit Task</button>
    </div>

  </form>
</div>

<script>
  const subcategoriesData = {
    1: ['House Cleaning', 'Office Cleaning', 'Deep Cleaning', 'Window Cleaning', 'Carpet Cleaning'],
    2: ['Grocery Shopping', 'Food Delivery', 'Package Pickup', 'Medicine Delivery', 'Gift Shopping'],
    3: ['Daytime Childcare', 'Evening Babysitting', 'Infant Care', 'Homework Help', 'School Drop-off/Pickup'],
    4: ['Administrative Tasks', 'Event Planning', 'Research', 'Bookkeeping', 'Scheduling'],
    5: ['Companion Care', 'Medication Reminders', 'Light Housekeeping', 'Meal Preparation', 'Transportation'],
    6: ['Dog Walking', 'Pet Sitting', 'Feeding', 'Grooming', 'Pet Transportation']
  };

  $('#category').on('change', function () {
    const selectedCat = $(this).val();
    const subcatContainer = $('#subcategories');
    subcatContainer.empty();

    if (subcategoriesData[selectedCat]) {
      subcategoriesData[selectedCat].forEach((name, index) => {
        subcatContainer.append(`
          <div class="form-check col-md-4">
            <input class="form-check-input" type="checkbox" name="subcategories[]" value="${name}" id="subcat${index}">
            <label class="form-check-label" for="subcat${index}">${name}</label>
          </div>
        `);
      });
    }
  });
</script>

</body>
</html>
