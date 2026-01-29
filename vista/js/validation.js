/**
 * Validation Helper Functions
 */

// Validate Colombian phone number: 10 digits, starts with 3
function validatePhone(phone) {
    const re = /^3[0-9]{9}$/;
    return re.test(phone);
}

// Validate Email address
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate Not Empty
function validateNotEmpty(value) {
    return value && value.trim() !== '';
}

// Validate Numeric
function validateNumeric(value) {
    return !isNaN(parseFloat(value)) && isFinite(value);
}

// Show error alert using SweetAlert2
function showErrorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error de validación',
        text: message,
    });
}

// Show loading alert
function showLoading(title = 'Procesando...') {
    Swal.fire({
        title: title,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}
