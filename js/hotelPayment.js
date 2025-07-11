document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (!form) return;

    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const creditCardOption = document.getElementById('credit_card');
    const cardDetailsInputs = document.querySelectorAll('.card-form input');
    
    // Card input elements
    const cardNameInput = document.getElementById('card-name');
    const cardNumberInput = document.getElementById('card-number');
    const cardExpiryInput = document.getElementById('card-expiry');
    const cardCvvInput = document.getElementById('card-cvv');
    
    // Debug: Log all payment method elements
    console.log('Payment method elements:', paymentMethods);
    
    // Show/hide card details based on payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Debug: Log when payment method changes
            console.log('Payment method changed:', method.id, method.checked);
            
            const isCreditCard = creditCardOption.checked;
            document.querySelector('.card-form').style.display = isCreditCard ? 'block' : 'none';
            document.querySelector('.card-details-header').style.display = isCreditCard ? 'flex' : 'none';
            
            // If credit card is selected, add a visual indicator to required fields
            if (isCreditCard) {
                // Add a subtle highlight to all required fields
                document.querySelectorAll('.card-form input[required]').forEach(input => {
                    input.classList.add('highlight-required');
                    
                    // Remove highlight when user starts typing
                    input.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            this.classList.remove('highlight-required');
                        } else {
                            this.classList.add('highlight-required');
                        }
                    }, { once: false });
                });
                
                // Focus the first input field for better UX
                setTimeout(() => {
                    cardNameInput.focus();
                }, 100);
            } else {
                // For non-credit card payment methods, enable direct payment
                // Remove required attribute from credit card fields
                document.querySelectorAll('.card-form input[required]').forEach(input => {
                    input.removeAttribute('required');
                });
            }
        });
    });
    
    // Initially hide card details
    document.querySelector('.card-form').style.display = 'none';
    document.querySelector('.card-details-header').style.display = 'none';
    
    // Fix radio button styling and functionality
    paymentMethods.forEach(radio => {
        const methodContainer = radio.closest('.payment-method');
        if (methodContainer) {
            methodContainer.addEventListener('click', function() {
                // Uncheck all radios first
                paymentMethods.forEach(r => r.checked = false);
                
                // Check the clicked one
                radio.checked = true;
                
                // Debug: Log the selection
                console.log('Selected payment method:', radio.id, radio.checked);
                
                // Trigger the change event
                const event = new Event('change');
                radio.dispatchEvent(event);
            });
        }
    });

    // Format card number with spaces for better readability
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            
            // Limit to 16 digits
            if (value.length > 16) {
                value = value.substring(0, 16);
            }
            
            const formattedValue = value.replace(/\d{4}(?=.)/g, '$& ');
            this.value = formattedValue;
            
            // Simple validation - check if it's 16 digits
            if (value.length > 0 && value.length < 16) {
                this.classList.add('error');
                
                // Find parent container
                const parent = this.parentNode;
                
                // Remove existing error message if any
                const existingError = parent.querySelector('.error-message');
                if (existingError) existingError.remove();
                
                // Add error message
                const errorMessage = document.createElement('span');
                errorMessage.className = 'error-message';
                errorMessage.textContent = 'Card number must be 16 digits';
                parent.appendChild(errorMessage);
            } else {
                this.classList.remove('error');
                
                // Remove error message if exists
                const parent = this.parentNode;
                const errorMessage = parent.querySelector('.error-message');
                if (errorMessage) errorMessage.remove();
            }
            
            validateCardField(this, 'card_number');
        });
    }
    
    // Format expiration date input (MM/YY)
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
            validateCardField(this, 'card_expiry');
        });
    }
    
    // Limit CVV to 3-4 digits
    if (cardCvvInput) {
        cardCvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
            validateCardField(this, 'card_cvv');
        });
    }
    
    // Validate card name
    if (cardNameInput) {
        cardNameInput.addEventListener('blur', function() {
            validateCardField(this, 'card_name');
        });
    }
    
    // Form submission handler with AJAX validation
    form.addEventListener('submit', function(e) {
        // If credit card is not selected, allow form to submit normally
        if (!creditCardOption.checked) {
            console.log('Non-credit card payment method selected, submitting directly');
            return; // Allow normal form submission
        }
        
        // Check if all required fields are filled
        let emptyFields = [];
        
        if (!cardNameInput.value.trim()) {
            emptyFields.push('Name on card');
            showError(cardNameInput, 'Please enter the name on card');
        }
        
        if (!cardNumberInput.value.trim()) {
            emptyFields.push('Card number');
            showError(cardNumberInput, 'Please enter card number');
        }
        
        if (!cardExpiryInput.value.trim()) {
            emptyFields.push('Expiration date');
            showError(cardExpiryInput, 'Please enter expiration date');
        }
        
        if (!cardCvvInput.value.trim()) {
            emptyFields.push('CVV');
            showError(cardCvvInput, 'Please enter CVV');
        }
        
        // If any fields are empty, show reminder and prevent submission
        if (emptyFields.length > 0) {
            e.preventDefault();
            
            // Create a message listing all empty fields
            const fieldList = emptyFields.join(', ');
            const message = `Please fill in the following required fields: ${fieldList}`;
            
            // Show alert with the message
            alert(message);
            
            // Focus the first empty field
            document.querySelector('.form-input.error').focus();
            
            return;
        }
        
        // Prevent default form submission
        e.preventDefault();
        
        // Show loading state - try to find button by ID first, then by type
        let submitBtn = document.getElementById('payment-submit-btn');
        if (!submitBtn) {
            submitBtn = form.querySelector('button[type="submit"]');
        }
        
        if (!submitBtn) {
            console.error('Submit button not found, submitting form directly');
            setTimeout(() => {
                form.submit();
            }, 0);
            return;
        }
        
        const originalBtnText = submitBtn.textContent || 'Proceed to Payment';
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
        
        // Collect card data
        const cardData = {
            action: 'validate_card',
            card_name: cardNameInput.value,
            card_number: cardNumberInput.value,
            card_expiry: cardExpiryInput.value,
            card_cvv: cardCvvInput.value
        };
        
        // Send AJAX request to validate card
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(cardData)
        })
        .then(response => response.json())
        .then(data => {
            // Reset all error messages
            clearAllErrors();
            
            if (data.valid) {
                // Card is valid, submit the form
                console.log('Card validation successful, submitting form...');
                submitBtn.textContent = 'Redirecting...';
                
                setTimeout(() => {
                    form.submit();
                }, 0);
            } else {
                // Show validation errors
                for (const field in data.errors) {
                    const inputElement = document.getElementById('card-' + field.replace('card_', ''));
                    if (inputElement) {
                        showError(inputElement, data.errors[field]);
                    }
                }
                
                // Reset button state
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error validating card:', error);
            alert('An error occurred while validating your card. Attempting to submit form directly.');
            
            // Reset button state
            submitBtn.textContent = originalBtnText;
            submitBtn.disabled = false;
            
            // Try direct form submission as fallback
            setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
    
    // Function to validate a single card field via AJAX
    function validateCardField(inputElement, fieldName) {
        // Only validate if credit card is selected
        if (!creditCardOption.checked) return;
        
        // Don't validate empty fields on input (only on blur/submit)
        if (inputElement.value.trim() === '' && fieldName !== 'card_name') return;
        
        const data = {
            action: 'validate_card'
        };
        data[fieldName] = inputElement.value;
        
        // For card number, we need to remove spaces
        if (fieldName === 'card_number') {
            data[fieldName] = data[fieldName].replace(/\s+/g, '');
        }
        
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(data => {
            // Clear previous error for this field
            clearError(inputElement);
            
            // Show error if any
            if (data.errors && data.errors[fieldName]) {
                showError(inputElement, data.errors[fieldName]);
            }
        })
        .catch(error => {
            console.error('Error validating field:', error);
        });
    }
    
    // Function to show error message
    function showError(inputElement, message) {
        // Remove existing error message if any
        clearError(inputElement);
        
        // Add error class to input
        inputElement.classList.add('error');
        
        // Create error message element
        const errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        
        // Insert after input or its parent (for grouped inputs)
        const parent = inputElement.closest('.expiry-field') || 
                       inputElement.closest('.cvv-field') || 
                       inputElement.parentNode;
        parent.appendChild(errorElement);
    }
    
    // Function to clear error message
    function clearError(inputElement) {
        // Remove error class
        inputElement.classList.remove('error');
        
        // Find parent container
        const parent = inputElement.closest('.expiry-field') || 
                       inputElement.closest('.cvv-field') || 
                       inputElement.parentNode;
        
        // Remove error message if exists
        const errorElement = parent.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    // Function to clear all error messages
    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
    }
});