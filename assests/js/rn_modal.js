document.addEventListener('DOMContentLoaded', function() {
    // Main service categories
    const mainCategories = {
      'cleaning': [
        'House Cleaning', 'Disinfecting Services', 'Office Cleaning',
        'Post-Construction Cleaning', 'Carpet Cleaning', 'Window Cleaning',
        'Deep Clean Services'
      ],
      'shopping-delivery': [
        'Grocery Shopping', 'Medicine Pickup & Delivery', 'Food Pick-Up/Delivery',
        'Gift Shopping', 'Package/Mail Delivery', 'Laundry Pick-Up/Delivery',
        'Document Delivery'
      ],
      'babysitter': [
        'Child Care', 'Baby Sitting', 'Homework Help for Children',
        'Meal Prep for Kids', 'Children\'s Activities', 'Child Transportation',
        'After School Care'
      ],
      'personal-assistant': [
        'Admin Tasks', 'Event Planning', 'Travel Arrangements', 
        'Online Research', 'IT Setup & Troubleshooting', 'Appointment Scheduling',
        'Personal Shopping'
      ],
      'senior-assistance': [
        'Medication Reminders', 'Companionship', 'Grocery Shopping & Delivery',
        'Light Housekeeping', 'Meal Preparation', 'Transportation Services',
        'Medical Appointments'
      ],
      'pet-care': [
        'Pet Sitting', 'Dog Walking', 'Pet Grooming',
        'Pet Food Delivery', 'Vet Appointments', 'Pet Transportation',
        'Medication Administration'
      ]
    };
    
    // Selected categories tracking
    const selectedMainCategory = document.getElementById('selectedMainCategory');
    const selectedCategoriesContainer = document.getElementById('selectedCategoriesContainer');
    
    // Function to create subcategory checkboxes in the details section
    function createSubcategoryCheckboxes(categoryId, subcategories) {
      // Hide all detail sections first
      document.querySelectorAll('.category-details').forEach(section => {
        section.classList.add('d-none');
      });
      
      // Show the container for the selected category
      const detailsSection = document.getElementById(categoryId + 'DetailsSection');
      detailsSection.classList.remove('d-none');
      
      // Clear existing subcategories
      const container = detailsSection.querySelector('.subcategories-list');
      container.innerHTML = '';
      
      // Set the hidden input value
      selectedMainCategory.value = categoryId;
      
      // Create subcategory checkboxes
      subcategories.forEach(subcategory => {
        const div = document.createElement('div');
        div.className = 'form-check mb-2';
        
        const input = document.createElement('input');
        input.className = 'form-check-input';
        input.type = 'checkbox';
        input.name = 'categories[]';
        input.value = subcategory.toLowerCase().replace(/\s+/g, '-');
        input.id = 'category-' + input.value;
        
        const label = document.createElement('label');
        label.className = 'form-check-label';
        label.htmlFor = 'category-' + input.value;
        label.textContent = subcategory;
        
        // Add change event to track selected categories
        input.addEventListener('change', function() {
          updateSelectedCategories();
        });
        
        div.appendChild(input);
        div.appendChild(label);
        container.appendChild(div);
      });
    }
    
    // Function to update the selected categories summary
    function updateSelectedCategories() {
      const selectedCheckboxes = document.querySelectorAll('input[name="categories[]"]:checked');
      selectedCategoriesContainer.innerHTML = '';
      
      if (selectedCheckboxes.length === 0) {
        selectedCategoriesContainer.innerHTML = '<div class="text-muted">No categories selected</div>';
        return;
      }
      
      selectedCheckboxes.forEach(checkbox => {
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary me-1 mb-1';
        badge.textContent = checkbox.nextElementSibling.textContent;
        selectedCategoriesContainer.appendChild(badge);
      });
    }
  
    // Function to handle main category selection
    function handleCategorySelection() {
      const serviceCategoryCards = document.querySelectorAll('.service-category-card');
      
      serviceCategoryCards.forEach(card => {
        card.addEventListener('click', function() {
          // Remove active class from all cards
          serviceCategoryCards.forEach(c => c.classList.remove('active-card'));
          
          // Add active class to selected card
          this.classList.add('active-card');
          
          // Get category ID
          const categoryId = this.getAttribute('data-category');
          
          // Load subcategories
          if (mainCategories[categoryId]) {
            createSubcategoryCheckboxes(categoryId, mainCategories[categoryId]);
            updateSelectedCategories();
          }
        });
      });
    }
  
    // Initialize the main category selection functionality
    handleCategorySelection();
  });