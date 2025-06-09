// Payment method selection
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('.payment-method');
    const radioButtons = document.querySelectorAll('.radio-button');
    
    paymentMethods.forEach((method, index) => {
        method.addEventListener('click', function() {
            // Remove selected class from all methods
            paymentMethods.forEach(m => m.classList.remove('selected'));
            radioButtons.forEach(r => r.classList.remove('selected'));
            
            // Add selected class to clicked method
            this.classList.add('selected');
            this.querySelector('.radio-button').classList.add('selected');
        });
    });
    
    // Form validation and interactions
    const formInputs = document.querySelectorAll('.form-input');
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#605dec';
        });
        
        input.addEventListener('blur', function() {
            if (this.value === '') {
                this.style.borderColor = '#a1afcc';
            }
        });
    });
    
    // Card number formatting
    const cardNumberInput = document.querySelector('input[placeholder="Card Number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (formattedValue.length > 19) {
                formattedValue = formattedValue.substring(0, 19);
            }
            e.target.value = formattedValue;
        });
    }
    
    // Expiration date formatting
    const expiryInput = document.querySelector('input[placeholder="Expiration Date"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
    
    // CVV input restriction
    const cvvInput = document.querySelector('input[placeholder="CVV"]');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.substring(0, 4);
            }
            e.target.value = value;
        });
    }
    
    // Button interactions
    const backButton = document.querySelector('.back-button');
    const confirmButton = document.querySelector('.confirm-button');
    
    if (backButton) {
        backButton.addEventListener('click', function() {
            alert('Navigating back to seat selection...');
        });
    }
    
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            // Basic form validation
            const nameInput = document.querySelector('input[placeholder="Name"]');
            const cardInput = document.querySelector('input[placeholder="Card Number"]');
            const expiryInput = document.querySelector('input[placeholder="Expiration Date"]');
            const cvvInput = document.querySelector('input[placeholder="CVV"]');
            
            if (!nameInput.value || !cardInput.value || !expiryInput.value || !cvvInput.value) {
                alert('Please fill in all required fields.');
                return;
            }
            
            alert('Processing payment... This is a demo, no actual payment will be processed.');
        });
    }
    
    // Smooth scrolling for mobile
    if (window.innerWidth <= 768) {
        const confirmButtonContainer = document.querySelector('.confirm-button-container');
        if (confirmButtonContainer) {
            confirmButtonContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    // Add loading states for buttons
    function addLoadingState(button, originalText) {
        button.disabled = true;
        button.textContent = 'Loading...';
        button.style.opacity = '0.7';
        
        setTimeout(() => {
            button.disabled = false;
            button.textContent = originalText;
            button.style.opacity = '1';
        }, 2000);
    }
    
    // Enhanced button click handlers
    if (confirmButton) {
        confirmButton.addEventListener('click', function() {
            addLoadingState(this, 'Confirm and pay');
        });
    }
});