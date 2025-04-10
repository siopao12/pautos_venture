document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
  
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
  
      const formData = new FormData(loginForm);
  
      fetch('../database/db_login.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Welcome!',
              text: data.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.href = data.redirect;
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Login Failed',
              text: data.message
            });
          }
        })
        .catch(err => {
          console.error('Fetch error:', err);
          Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Something went wrong.'
          });
        });
    });
  });
  