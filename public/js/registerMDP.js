document.addEventListener('DOMContentLoaded', () => {
    const passwordField = document.getElementById('registrationForm_plainPassword'); // Vérifie bien que l'ID est correct
    const submitButton = document.getElementById('submit-button');
    const passwordRequirements = document.getElementById('password-requirements');

    passwordField.addEventListener('input', function () {
        const password = passwordField.value;
        console.log(password);

        // Validation des règles de sécurité pour le mot de passe
        const minLength = 12;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        // Construction du message d'erreur
        let message = 'Le mot de passe doit contenir :<br>';
        let isValid = true;

        if (password.length < minLength) {
            message += `- Au moins ${minLength} caractères<br>`;
            isValid = false;
        }
        if (!hasUpperCase) {
            message += '- Une lettre majuscule<br>';
            isValid = false;
        }
        if (!hasLowerCase) {
            message += '- Une lettre minuscule<br>';
            isValid = false;
        }
        if (!hasNumber) {
            message += '- Un chiffre<br>';
            isValid = false;
        }
        if (!hasSpecialChar) {
            message += '- Un caractère spécial (e.g. !@#$%^&*)<br>';
            isValid = false;
        }

        // Mise à jour du message d'erreur et de l'état du bouton
        passwordRequirements.innerHTML = isValid ? '' : message;
        submitButton.disabled = !isValid;
    });
});
