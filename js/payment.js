document.addEventListener('DOMContentLoaded', function () {
    const total = sessionStorage.getItem('total_price') || '0';
    const ticket = sessionStorage.getItem('ticket_price') || '0';
    const baggage = sessionStorage.getItem('baggage_price') || '0';
    const meal = sessionStorage.getItem('meal_price') || '0';
    const label1 = sessionStorage.getItem('ticket_label') || 'Tickets';
    const adults = parseInt(sessionStorage.getItem('adultCount')) || 1;
    const children = parseInt(sessionStorage.getItem('childCount')) || 0;
    // Update the price values
    document.getElementById('flight-price').textContent = `RM ${ticket}`;
    document.querySelector('.meal-price').textContent = `RM ${meal}`;
    document.querySelector('.baggage-price').textContent = `RM ${baggage}`;
    document.getElementById('total').textContent = `RM ${total}`;
    document.getElementById('ticket-count-label').textContent = label1;
    // Generate dynamic passenger label
    let label = 'Tickets (';
    if (adults > 0) label += `${adults} Adult${adults > 1 ? 's' : ''}`;
    if (children > 0) {
      if (adults > 0) label += ', ';
      label += `${children} Child${children > 1 ? 'ren' : ''}`;
    }
    label += ')';
    const ticketLabel = document.getElementById('ticket-count-label');
    

  // Payment method selection
    if (ticketLabel) ticketLabel.textContent = label;
    document.getElementById('ticket-count-label').textContent = label;
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
    document.querySelector('.proceed-btn').addEventListener('click', function () {
        const selectedMethod = document.querySelector('.payment-method.selected .method-name')?.textContent.trim();

        // If Credit/Debit Card selected, validate card fields
        if (selectedMethod === "Debit/Credit Card") {
            const nameInput = document.querySelector('input[placeholder="Name"]');
            const cardInput = document.querySelector('input[placeholder="Card Number"]');
            const expiryInput = document.querySelector('input[placeholder="Expiration Date"]');
            const cvvInput = document.querySelector('input[placeholder="CVV"]');
    
            if (!nameInput.value || !cardInput.value || !expiryInput.value || !cvvInput.value) {
                alert('Please complete all card details before proceeding.');
                return;
            }
        }
        const amountText = document.getElementById('total').textContent;
        const amount = amountText.replace(/[^\d.]/g, '');
        const paymentMethodElement = document.querySelector('.payment-method.selected .method-name');
        const paymentMethod = paymentMethodElement ? paymentMethodElement.textContent.trim() : '';
        const departId = document.getElementById('depart_flight_id')?.value;
        const returnId = document.getElementById('return_flight_id')?.value;
        const classId = document.getElementById('seat_class_field')?.value;
    
        const today = new Date();
        const paymentDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    
        console.log("Preparing payment data:");
        console.log("Amount:", amount);
        console.log("Payment Method:", paymentMethod);
        console.log("Payment Date:", paymentDate);
        console.log("Depart Flight ID:", departId);
        console.log("Return Flight ID:", returnId);
        console.log("Seat Class:", classId);
    
        fetch('insertFlightPayment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                amount,
                payment_method: paymentMethod,
                payment_date: paymentDate,
                depart_id: departId,
                return_id: returnId,
                class_id: classId
            })
        })
        .then(res => res.text())
        .then(response => {
            console.log("Server Response:", response);
            window.location.href = 'payment_complete.php';
        })
        .catch(err => {
            console.error("Error:", err);
            alert("Payment failed. Please try again.");
        });
    });    
});

  
