document.getElementById('profileForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Prevent default form submission

  const form = e.target;
  const formData = new FormData(form);

  fetch('../database/upload_profile.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: data.message,
        timer: 2000,
        showConfirmButton: false
      });

     // ✅ Update profile image preview in modal with cache-busting
     if (data.newImage) {
      const cacheBuster = '?v=' + new Date().getTime();
      document.getElementById('profilePreview').src = '../' + data.newImage + cacheBuster;

      // ✅ Also update profile image in the header with cache-busting
      const profileDropdownImage = document.getElementById('profileDropdown');
      if (profileDropdownImage) {
        profileDropdownImage.src = '../' + data.newImage + cacheBuster;
      }
    }
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Upload Failed',
        text: data.message
      });
    }
  })
  .catch(error => {
    console.error('Upload error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: 'Something went wrong while uploading the image.'
    });
  });
});


