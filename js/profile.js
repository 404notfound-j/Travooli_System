/**
 * Profile Page JavaScript - Form validation and slide-in messages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile messages
    initProfileMessages();
    
    // Initialize form validation
    initProfileFormValidation();
    
    // Initialize field highlighting
    initFieldHighlighting();
});

// Handle profile messages (success/error from PHP)
function initProfileMessages() {
    const profileMessage = document.getElementById('profile-message');
    if (profileMessage) {
        const messageText = profileMessage.textContent.trim();
        const messageType = profileMessage.getAttribute('data-type');
        
        if (messageText) {
            // Hide the original PHP message
            profileMessage.classList.add('hide-original-message');
            
            // Show the slide-in message
            setTimeout(() => {
                if (messageType === 'success') {
                    showSuccessMessage(messageText);
                } else {
                    showErrorMessage(messageText);
                }
            }, 100);
        }
    }
}

// Form validation for profile completion
function initProfileFormValidation() {
    const profileForm = document.querySelector('.profile-form');
    const cancelButton = document.querySelector('.btn-secondary');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            if (!validateProfileCompletion()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Handle cancel button click
    if (cancelButton) {
        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!validateProfileCompletion()) {
                showErrorMessage('Please complete your phone number and country before leaving.');
                return false;
            }
            
            // Use location.replace to avoid form resubmission issues
            window.location.replace('U_dashboard.php');
        });
    }
}

// Validate profile completion
function validateProfileCompletion() {
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const country = countrySelect ? countrySelect.value.trim() : '';
    
    if (!phone) {
        showErrorMessage('Please provide your phone number to complete your profile.');
        phoneInput.focus();
        return false;
    }
    
    if (!country) {
        showErrorMessage('Please select your country to complete your profile.');
        countrySelect.focus();
        return false;
    }
    
    return true;
}

// Real-time field highlighting removal
function initFieldHighlighting() {
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    const profileContainer = document.querySelector('.profile-container');
    
    // Remove highlighting when user starts filling required fields
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            if (this.value.trim()) {
                updateProfileCompletion();
            }
        });
    }
    
    if (countrySelect) {
        countrySelect.addEventListener('change', function() {
            if (this.value.trim()) {
                updateProfileCompletion();
            }
        });
    }
}

// Update profile completion status
function updateProfileCompletion() {
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    const profileContainer = document.querySelector('.profile-container');
    
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const country = countrySelect ? countrySelect.value.trim() : '';
    
    if (phone && country && profileContainer) {
        // Remove incomplete class
        profileContainer.classList.remove('profile-incomplete');
        profileContainer.classList.add('profile-complete');
    }
} 