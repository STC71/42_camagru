// Main JavaScript file for Camagru
// Handles common functionality across all pages

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.flash');
    flashMessages.forEach(flash => {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transition = 'opacity 0.5s ease';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    });

    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Form validation helper
    window.validateForm = function(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = 'var(--danger-color)';
                isValid = false;
            } else {
                input.style.borderColor = '';
            }
        });

        return isValid;
    };

    // Password strength indicator
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            // You can add visual feedback here
        });
    });
});

function getPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

// AJAX helper function
function ajax(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                callback(null, response);
            } catch (e) {
                callback(e, null);
            }
        } else {
            callback(new Error('Request failed'), null);
        }
    };
    
    xhr.onerror = function() {
        callback(new Error('Network error'), null);
    };
    
    if (method === 'POST' && data) {
        const formData = Object.keys(data)
            .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
            .join('&');
        xhr.send(formData);
    } else {
        xhr.send();
    }
}
