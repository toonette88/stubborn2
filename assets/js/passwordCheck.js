document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('registration_form_plainPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const submitButton = document.getElementById('submitButton');
    const passwordMatchMessage = document.getElementById('passwordMatchMessage');

    function validatePasswords() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (password === confirmPassword && password.length > 0) {
            passwordMatchMessage.style.display = 'none';
            submitButton.disabled = false;
        } else {
            passwordMatchMessage.style.display = 'block';
            submitButton.disabled = true;
        }
    }

    passwordInput.addEventListener('input', validatePasswords);
    confirmPasswordInput.addEventListener('input', validatePasswords);
});