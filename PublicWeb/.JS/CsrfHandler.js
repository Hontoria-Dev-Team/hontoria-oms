/**
 * CSRF Token Management
 * Provides utilities for handling CSRF tokens in AJAX and dynamically created forms
 */

// Get the CSRF token from the page (stored in a hidden input)
function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="_csrf_token"]');
    return tokenInput ? tokenInput.value : null;
}

/**
 * Add CSRF token to a form that's being dynamically set up
 * @param {HTMLFormElement} form - The form to add the CSRF token to
 */
function addCsrfTokenToForm(form) {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        console.warn('CSRF token not found on page');
        return;
    }

    // Remove existing CSRF token if any
    const existingToken = form.querySelector('input[name="_csrf_token"]');
    if (existingToken) {
        existingToken.remove();
    }

    // Create and add new CSRF token input
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_csrf_token';
    csrfInput.value = csrfToken;
    form.insertBefore(csrfInput, form.firstChild);
}

/**
 * Add CSRF token as a hidden input to the confirmation form before submission
 * This function should be called before setting confirmationForm.action
 */
function ensureCsrfTokenInConfirmationForm() {
    const confirmationForm = document.getElementById('confirmationForm');
    if (confirmationForm) {
        addCsrfTokenToForm(confirmationForm);
    }
}
