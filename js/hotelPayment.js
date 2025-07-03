document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (!form) return;

    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const creditCardOption = document.getElementById('credit_card');
    const cardDetailsInputs = document.querySelectorAll('.card-form input');
    
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
    // The form submit handler has been removed to allow normal form submission.
});