document.addEventListener('DOMContentLoaded', function() {
    // Show Success Toast
    var successToastElement = document.getElementById('successToast');
    if (successToastElement) {
        var successMessage = successToastElement.querySelector('.toast-body').getAttribute('data-message');
        if (successMessage) {
            var toast = new bootstrap.Toast(successToastElement);
            var toastBody = successToastElement.querySelector('.toast-body');
            toastBody.textContent = successMessage;
            toast.show();
        }
    }

    // Show Error Toast
    var errorToastElement = document.getElementById('errorToast');
    if (errorToastElement) {
        var errorMessage = errorToastElement.querySelector('.toast-body').getAttribute('data-message');
        if (errorMessage) {
            var toast = new bootstrap.Toast(errorToastElement);
            var toastBody = errorToastElement.querySelector('.toast-body');
            toastBody.textContent = errorMessage;
            toast.show();
        }
    }
});

