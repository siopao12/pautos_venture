console.log("✅ validate.js is loaded!");
document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');

    if (!registerForm) {
        console.warn("Register form not found!");
        return;
    }

    const password = document.getElementById('registerPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    // Password match check (live)
    confirmPassword.addEventListener('input', function () {
        if (confirmPassword.value !== password.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });

    // Handle submit with fetch
    registerForm.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (!registerForm.checkValidity()) {
            registerForm.classList.add('was-validated');
            return;
        }

        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
            registerForm.classList.add('was-validated');
            return;
        }

        confirmPassword.setCustomValidity('');
        registerForm.classList.add('was-validated');

        const formData = new FormData(registerForm);

        fetch('../database/db_registration.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Registered!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registration Failed',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Something went wrong. Please try again later.'
            });
        });
    });

    // Reset form when modal closes
    const registerModal = document.getElementById('registerModal');
    if (registerModal) {
        registerModal.addEventListener('hidden.bs.modal', function () {
            registerForm.reset();
            registerForm.classList.remove('was-validated');
            confirmPassword.setCustomValidity('');
        });
    }
});
