/* 
Programmer Name: Mr.Chua Siong Zheng, Group Leader & Project Manager
Project Name: sign.js
Description: To handle the sign in and sign up page
Date first written: 10-May-2025
Date last modified: 6-Jul-2025 
 */


/**
 * Slide-in Message Animation System for Sign In/Sign Up Pages
 * Creates animated notification messages that slide in from the right
 */

function showSlideMessage(message, type = 'success', duration = 4000) {
    // Create the message element
    const messageElement = document.createElement('div');
    messageElement.textContent = message;
    messageElement.className = `slide-message ${type}`;
    
    // Add to page
    document.body.appendChild(messageElement);
    
    // Remove after animation completes
    setTimeout(() => {
        if (document.body.contains(messageElement)) {
            document.body.removeChild(messageElement);
        }
    }, duration);
}

// Convenience functions for different message types
function showSuccessMessage(message, duration = 4000) {
    showSlideMessage(message, 'success', duration);
}

function showErrorMessage(message, duration = 4000) {
    showSlideMessage(message, 'error', duration);
}

// Function to check for PHP messages and convert them to slide-in messages
function initSignMessages() {
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Check for specific signup error message
        const signupErrorMessage = document.getElementById('signup-error-message');
        if (signupErrorMessage) {
            const messageText = signupErrorMessage.textContent.trim();
            if (messageText) {
                // Hide the original PHP message
                signupErrorMessage.classList.add('hide-original-message');
                
                // Show the slide-in error message
                setTimeout(() => {
                    showErrorMessage(messageText);
                }, 100);
            }
        }
        
        // Check for specific signup success message
        const signupSuccessMessage = document.getElementById('signup-success-message');
        if (signupSuccessMessage) {
            const messageText = signupSuccessMessage.textContent.trim();
            if (messageText) {
                // Hide the original PHP message
                signupSuccessMessage.classList.add('hide-original-message');
                
                // Check if this is a redirect case
                if (messageText === 'redirect_to_profile') {
                    // Show success message and redirect to profile
                    setTimeout(() => {
                        showSuccessMessage('Account created successfully! Redirecting to complete your profile...');
                        
                        // Redirect after showing the message for 2.5 seconds
                        setTimeout(() => {
                            window.location.href = 'profile.php';
                        }, 2500);
                    }, 100);
                } else {
                    // Show the regular success message
                    setTimeout(() => {
                        showSuccessMessage(messageText);
                    }, 100);
                }
            }
        }
        
        // Check for specific signin error message
        const signinErrorMessage = document.getElementById('signin-error-message');
        if (signinErrorMessage) {
            const messageText = signinErrorMessage.textContent.trim();
            if (messageText) {
                // Hide the original PHP message
                signinErrorMessage.classList.add('hide-original-message');
                
                // Show the slide-in error message
                setTimeout(() => {
                    showErrorMessage(messageText);
                }, 100);
            }
        }
        
        // Fallback: Look for any PHP error/success messages by style
        const allPhpMessages = document.querySelectorAll('.signin-form p[style*="color:"], .signin-form p[style*="color "]');
        
        allPhpMessages.forEach(function(messageElement) {
            // Skip if already processed by ID
            if (messageElement.id === 'signup-error-message' || 
                messageElement.id === 'signup-success-message' || 
                messageElement.id === 'signin-error-message') {
                return;
            }
            
            const messageText = messageElement.textContent.trim();
            
            if (messageText) {
                // Determine message type based on color
                const styleAttribute = messageElement.getAttribute('style') || '';
                const isError = styleAttribute.includes('red');
                const messageType = isError ? 'error' : 'success';
                
                // Hide the original PHP message
                messageElement.classList.add('hide-original-message');
                
                // Show the slide-in message
                setTimeout(() => {
                    showSlideMessage(messageText, messageType);
                }, 100);
            }
        });
    });
}

// Auto-initialize when script loads
initSignMessages();

// Initialize form validation
initFormValidation();

// Export functions for global use
window.showSlideMessage = showSlideMessage;
window.showSuccessMessage = showSuccessMessage;
window.showErrorMessage = showErrorMessage;

// Password validation function
function validatePassword(password) {
    const errors = [];
    
    // Check length (8-20 characters)
    if (password.length < 8 || password.length > 20) {
        errors.push("Password must be between 8-20 characters");
    }
    
    // Check for at least 1 number
    if (!/\d/.test(password)) {
        errors.push("Password must contain at least 1 number");
    }
    
    // Check for spaces
    if (/\s/.test(password)) {
        errors.push("Password cannot contain spaces");
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Name validation function
function validateName(name, fieldName) {
    const errors = [];
    
    // Check if empty
    if (!name || name.trim() === '') {
        errors.push(`${fieldName} is required`);
    } else if (!/^[A-Za-z]+$/.test(name.trim())) {
        errors.push(`${fieldName} can only contain letters (no numbers, symbols, or spaces)`);
    }
    
    return {
        isValid: errors.length === 0,
        errors: errors
    };
}

// Form validation on submit
function initFormValidation() {
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.querySelector('.signin-form');
        
        if (signupForm) {
            // Add input restrictions for name fields
            initNameInputRestrictions();
            
            signupForm.addEventListener('submit', function(e) {
                const firstNameInput = document.getElementById('firstName');
                const lastNameInput = document.getElementById('lastName');
                const passwordInput = document.getElementById('password');
                
                // Validate first name
                if (firstNameInput) {
                    const firstName = firstNameInput.value;
                    const validation = validateName(firstName, 'First name');
                    
                    if (!validation.isValid) {
                        e.preventDefault();
                        showErrorMessage(validation.errors[0]);
                        firstNameInput.focus();
                        return false;
                    }
                }
                
                // Validate last name
                if (lastNameInput) {
                    const lastName = lastNameInput.value;
                    const validation = validateName(lastName, 'Last name');
                    
                    if (!validation.isValid) {
                        e.preventDefault();
                        showErrorMessage(validation.errors[0]);
                        lastNameInput.focus();
                        return false;
                    }
                }
                
                // Validate password
                if (passwordInput) {
                    const password = passwordInput.value;
                    const validation = validatePassword(password);
                    
                    if (!validation.isValid) {
                        e.preventDefault(); // Stop form submission
                        
                        // Show first error with slide-in animation
                        showErrorMessage(validation.errors[0]);
                        
                        // Focus back to password field
                        passwordInput.focus();
                        return false;
                    }
                }
            });
        }
    });
}

// Initialize input restrictions for name fields
function initNameInputRestrictions() {
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');
    
    // Restrict first name input to letters only
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
    
    // Restrict last name input to letters only
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
}

 
// Testing function is console
// Test slide in message
// showSlideMessage('Custom message text', 'success')
// showSlideMessage('Custom error text', 'error')

// Test individual passwords
// validatePassword('short1')           // Too short
// validatePassword('validPass123')     // Valid
// validatePassword('noNumbers')        // Missing numbers
// validatePassword('password 123')     // Has spaces
// validatePassword('toolongpasswordwithmorethan20characters')  // Too long