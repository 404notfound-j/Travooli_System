/* 
Programmer Name: Mr.Chua Siong Zheng, Group Leader & Project Manager
Project Name: profile.css
Description: To style the profile page
Date first written: 10-May-2025
Date last modified: 6-Jul-2025 
 */


/*
 * Profile Page JavaScript - Form validation and slide-in messages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile messages
    initProfileMessages();
    
    // Initialize form validation
    initProfileFormValidation();
    
    // Initialize field highlighting
    initFieldHighlighting();
    
    // Initialize input restrictions
    initInputRestrictions();
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
            if (!validateProfileForm()) {
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

// Complete form validation including character restrictions
function validateProfileForm() {
    const firstNameInput = document.getElementById('fst_name');
    const lastNameInput = document.getElementById('lst_name');
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    const genderSelect = document.getElementById('gender');
    
    const firstName = firstNameInput ? firstNameInput.value.trim() : '';
    const lastName = lastNameInput ? lastNameInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const country = countrySelect ? countrySelect.value.trim() : '';
    const gender = genderSelect ? genderSelect.value.trim() : '';
    
    // Validate first name
    if (!firstName) {
        showErrorMessage('First name is required.');
        firstNameInput.focus();
        return false;
    }
    
    if (!isValidName(firstName)) {
        showErrorMessage('First name can only contain letters (no numbers, symbols, or spaces).');
        firstNameInput.focus();
        return false;
    }
    
    // Validate last name
    if (!lastName) {
        showErrorMessage('Last name is required.');
        lastNameInput.focus();
        return false;
    }
    
    if (!isValidName(lastName)) {
        showErrorMessage('Last name can only contain letters (no numbers, symbols, or spaces).');
        lastNameInput.focus();
        return false;
    }
    
    // Validate phone number
    if (!phone) {
        showErrorMessage('Please provide your phone number to complete your profile.');
        phoneInput.focus();
        return false;
    }
    
    if (!isValidPhone(phone)) {
        showErrorMessage('Phone number can only contain numbers (no letters, symbols, or spaces).');
        phoneInput.focus();
        return false;
    }
    
    if (phone.length < 8 || phone.length > 15) {
        showErrorMessage('Phone number must be between 8-15 digits.');
        phoneInput.focus();
        return false;
    }
    
    // Alert for gender (optional but recommended)
    if (!gender) {
        showErrorMessage('Please select your gender to complete your profile.');
        genderSelect.focus();
        return false;
    }
    
    // Alert for country (optional but recommended)
    if (!country) {
        showErrorMessage('Please select your country to complete your profile.');
        countrySelect.focus();
        return false;
    }
    
    return true;
}

// Validate profile completion (for cancel button)
function validateProfileCompletion() {
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    const genderSelect = document.getElementById('gender');
    
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const country = countrySelect ? countrySelect.value.trim() : '';
    const gender = genderSelect ? genderSelect.value.trim() : '';
    
    if (!phone) {
        showErrorMessage('Please provide your phone number to complete your profile.');
        phoneInput.focus();
        return false;
    }
    
    if (!gender) {
        showErrorMessage('Please select your gender to complete your profile.');
        genderSelect.focus();
        return false;
    }
    
    if (!country) {
        showErrorMessage('Please select your country to complete your profile.');
        countrySelect.focus();
        return false;
    }
    
    return true;
}

// Check if name contains only letters
function isValidName(name) {
    return /^[A-Za-z]+$/.test(name);
}

// Check if phone contains only numbers
function isValidPhone(phone) {
    return /^[0-9]+$/.test(phone);
}

// Initialize input restrictions and real-time validation
function initInputRestrictions() {
    const firstNameInput = document.getElementById('fst_name');
    const lastNameInput = document.getElementById('lst_name');
    const phoneInput = document.getElementById('phone_no');
    
    // Restrict name inputs to letters only
    if (firstNameInput) {
        firstNameInput.addEventListener('input', function(e) {
            // Remove any non-letter characters
            let value = e.target.value.replace(/[^A-Za-z]/g, '');
            e.target.value = value;
        });
        
        firstNameInput.addEventListener('keypress', function(e) {
            // Prevent typing non-letter characters
            if (!/[A-Za-z]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
    }
    
    if (lastNameInput) {
        lastNameInput.addEventListener('input', function(e) {
            // Remove any non-letter characters
            let value = e.target.value.replace(/[^A-Za-z]/g, '');
            e.target.value = value;
        });
        
        lastNameInput.addEventListener('keypress', function(e) {
            // Prevent typing non-letter characters
            if (!/[A-Za-z]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
    }
    
    // Restrict phone input to numbers only
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;
        });
        
        phoneInput.addEventListener('keypress', function(e) {
            // Prevent typing non-digit characters
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
    }
}

// Real-time field highlighting removal
function initFieldHighlighting() {
    const phoneInput = document.getElementById('phone_no');
    const countrySelect = document.getElementById('country');
    const genderSelect = document.getElementById('gender');
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
    
    if (genderSelect) {
        genderSelect.addEventListener('change', function() {
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
    const genderSelect = document.getElementById('gender');
    const profileContainer = document.querySelector('.profile-container');
    
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const country = countrySelect ? countrySelect.value.trim() : '';
    const gender = genderSelect ? genderSelect.value.trim() : '';
    
    if (phone && country && gender && profileContainer) {
        // Remove incomplete class
        profileContainer.classList.remove('profile-incomplete');
        profileContainer.classList.add('profile-complete');
    }
} 