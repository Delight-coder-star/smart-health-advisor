/**
 * Profile Page Scripts
 */

document.addEventListener('DOMContentLoaded', function () {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('passwordStrength');

    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', function () {
            const val = this.value;
            let strength = 0;

            if (val.length >= 8) strength += 25;
            if (/[a-z]/.test(val)) strength += 25;
            if (/[A-Z]/.test(val)) strength += 25;
            if (/[0-9!@#$%^&*]/.test(val)) strength += 25;

            strengthBar.style.width = strength + '%';
            strengthBar.className = 'progress-bar ' +
                (strength < 50 ? 'bg-danger' : strength < 75 ? 'bg-warning' : 'bg-success');
        });
    }
});