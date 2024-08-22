$(document).ready(function() {
    $('.dropdown-toggle').dropdown();
});

//Pagination
$(document).ready(function() {
    $('#dataTable').DataTable({
        paging: true,
        searching: true,
        info: true,
    });
});

/**
 * Sign up multi-form
 */
document.addEventListener('DOMContentLoaded', function () {
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const steps = document.querySelectorAll('.step');
    const stepHeader = document.getElementById('step-header');
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((el, index) => {
            el.classList.toggle('active', index === step);
        });
        updateHeader(step);
    }

    function updateHeader(step) {
        const headers = ['Step 1: Create Account', 'Step 2: Personal Details'];
        stepHeader.innerText = headers[step];
    }

    function validateStep(step) {
        if (step === 0) {  
            if (!validatePassword() || !validateConfirmPassword()) {
                return false;
            }
        }
    
        const inputs = steps[step].querySelectorAll('input, select');
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                return false;
            }
        }
        return true;
    }
    
    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (document.getElementById('password-error').style.display === 'block') {
                return; 
            }
    
            if (validateStep(currentStep)) {
                currentStep = Math.min(currentStep + 1, steps.length - 1);
                showStep(currentStep);
            }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentStep = Math.max(currentStep - 1, 0);
            showStep(currentStep);
        });
    });

    showStep(currentStep);
});

/**
 * Sign up Multi-form
 */
document.addEventListener('DOMContentLoaded', function () {
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const steps = document.querySelectorAll('.step');
    const stepHeader = document.getElementById('step-header');
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((el, index) => {
            el.classList.toggle('active', index === step);
        });
        updateHeader(step);
    }

    function updateHeader(step) {
        const headers = [
            'Step 1: Create Account',
            'Step 2: Personal Details',
            'Step 3: Additional Details'
        ];
        stepHeader.innerText = headers[step];
    }

    function validateStep(step) {
        const inputs = steps[step].querySelectorAll('input, select');
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                return false;
            }
        }
        return true;
    }

    nextButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (!validateStep(currentStep)) {
                return; 
            }

            currentStep = Math.min(currentStep + 1, steps.length - 1);
            showStep(currentStep);
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentStep = Math.max(currentStep - 1, 0);
            showStep(currentStep);
        });
    });

    showStep(currentStep);
});
/**
 * Login Validation
 */
document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');

    function validatePassword() {
        const password = passwordInput.value;
        let errors = [];

        if (password.length < 8) {
            errors.push('Password must be at least 8 characters long.');
        }

        if (!/[A-Z]/.test(password)) {
            errors.push('Password must contain at least one capital letter.');
        }

        if (!/[a-z]/.test(password)) {
            errors.push('Password must contain at least one lowercase letter.');
        }

        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            errors.push('Password must contain at least one special character.');
        }

        if (errors.length > 0) {
            passwordError.textContent = errors[0];
            passwordError.style.display = 'block';
        } else {
            passwordError.textContent = '';
            passwordError.style.display = 'none';
        }

        return errors.length === 0; 
    }

    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
    
        if (confirmPassword.length > 0 && password !== confirmPassword) {
            confirmPasswordError.textContent = 'Password and confirm password do not match.';
            confirmPasswordError.style.display = 'block';
            return false;
        } else {
            confirmPasswordError.textContent = '';
            confirmPasswordError.style.display = 'none';
            return true;
        }
    }

    passwordInput.addEventListener('input', () => {
        validatePassword();
        validateConfirmPassword();
    });

    confirmPasswordInput.addEventListener('input', () => {
        validateConfirmPassword();
    });
});

/**
 * Birthdate Validation
 */

const birthdayInput = document.getElementById('birthday');
const birthdateError = document.getElementById('birthdate-error');

function validateBirthdate() {
    const today = new Date();
    const birthdate = new Date(birthdayInput.value);
    const age = today.getFullYear() - birthdate.getFullYear();
    const monthDifference = today.getMonth() - birthdate.getMonth();

    if (age < 18 || (age === 18 && monthDifference < 0) || (age === 18 && monthDifference === 0 && today.getDate() < birthdate.getDate())) {
        birthdateError.textContent = 'You must be at least 18 years old.';
        birthdateError.style.display = 'block';
        return false;
    } else {
        birthdateError.textContent = '';
        birthdateError.style.display = 'none';
        return true;
    }
}

birthdayInput.addEventListener('input', validateBirthdate);

/**
 * Phone number validation
 */
const phoneNumberInput = document.getElementById('phone-number');
const phoneNumberError = document.getElementById('phone-number-error');

function validatePhoneNumber() {
    const phoneNumber = phoneNumberInput.value;
    const phoneNumberPattern = /^\d{11}$/; 

    if (!phoneNumberPattern.test(phoneNumber)) {
        phoneNumberError.textContent = 'Phone number must be exactly 11 digits.';
        phoneNumberError.style.display = 'block';
        return false;
    } else {
        phoneNumberError.textContent = '';
        phoneNumberError.style.display = 'none';
        return true;
    }
}

phoneNumberInput.addEventListener('input', validatePhoneNumber);

/**
 * Prevent form from submitting
 */
document.getElementById('submit-btn').addEventListener('click', function(event) {
    const isBirthdateValid = validateBirthdate();
    const isPhoneNumberValid = validatePhoneNumber();
    
    if (!isBirthdateValid || !isPhoneNumberValid) {
        event.preventDefault(); 
    }
});


/**
 * Success Message
 */

