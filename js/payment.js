document.addEventListener('DOMContentLoaded', function () {
    const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');

    const total = parseFloat(flightSearch.finalTotalPrice || 0);
    const ticket = parseFloat(flightSearch.ticketPrice || 0);
    const baggage = parseFloat(flightSearch.baggagePrice || 0);
    const meal = parseFloat(flightSearch.mealPrice || 0);
    const taxPrice = parseFloat(flightSearch.taxPrice || 0);
    // const discount = parseFloat(flightSearch.discount || 0); // REMOVED: No longer need to retrieve discount

    const activeAdults = parseInt(flightSearch.activeAdults) || 1;
    const activeChildren = parseInt(flightSearch.children) || 0;
    const numPassengers = activeAdults + activeChildren;

    const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
    
    const departId = flightSearch.depart || flightSearch.selectedFlight || '';
    const returnId = flightSearch.return || '';
    const classId = flightSearch.classId || '';

    const flightDate = flightSearch.departDate || ''; // Get the departure date

    const userId = window.currentUserId || null;

    document.getElementById('flight-price').textContent = `RM ${ticket.toFixed(2)}`;
    document.querySelector('.baggage-price').textContent = `RM ${baggage.toFixed(2)}`;
    document.querySelector('.meal-price').textContent = `RM ${meal.toFixed(2)}`;
    
    const taxesFeesEl = document.querySelector('.price-item span[data-tax-display]');
    if (taxesFeesEl) {
        taxesFeesEl.textContent = `RM ${taxPrice.toFixed(2)}`;
    } else {
        const defaultTaxesEl = document.querySelector('.price-item:nth-child(4) span:nth-child(2)');
        if(defaultTaxesEl) defaultTaxesEl.textContent = `RM ${taxPrice.toFixed(2)}`;
    }

    document.getElementById('total').textContent = `RM ${total.toFixed(2)}`;

    let label = 'Tickets (';
    if (activeAdults > 0) label += `${activeAdults} Adult${activeAdults > 1 ? 's' : ''}`;
    if (activeChildren > 0) {
      if (activeAdults > 0) label += ', ';
      label += `${activeChildren} Child${activeChildren > 1 ? 'ren' : ''}`;
    }
    label += ')';

    const ticketLabel = document.getElementById('ticket-count-label');
    if (ticketLabel) ticketLabel.textContent = label;

    const paymentMethods = document.querySelectorAll('.payment-method');
    const radioButtons = document.querySelectorAll('.radio-button');
    const proceedBtn = document.querySelector('.proceed-btn');
    const cardFields = document.querySelectorAll('.card-input');

    function setCardFieldsEnabled(enabled) {
        cardFields.forEach(input => {
            input.disabled = !enabled;
            input.style.opacity = enabled ? '1' : '0.5';
        });
    }

    const cardNumberInput = document.querySelector('input[placeholder="Card Number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue.substring(0, 19);
        });
    }

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

    const cvvInput = document.querySelector('input[placeholder="CVV"]');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.substring(0, 3);
        });
    }

    function checkCardValidity() {
        const selectedMethod = document.querySelector('.payment-method.selected .method-name')?.textContent.trim();
        if (!selectedMethod) return false;
        if (selectedMethod === "Debit/Credit Card") {
            return Array.from(cardFields).every(input => input.value.trim() !== '');
        }
        return true;
    }

    function updateProceedButtonState() {
        proceedBtn.disabled = !checkCardValidity();
        if (proceedBtn.disabled) {
            proceedBtn.classList.add('disabled');
          } else {
            proceedBtn.classList.remove('disabled');
          }          
    }

    paymentMethods.forEach((method) => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('selected'));
            radioButtons.forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('.radio-button').classList.add('selected');

            const isCard = this.querySelector('.method-name')?.textContent.includes("Card");
            setCardFieldsEnabled(isCard);
            updateProceedButtonState();
        });
    });

    cardFields.forEach(input => {
        input.addEventListener('input', updateProceedButtonState);
    });

    setCardFieldsEnabled(false); // Initially disable card fields if not default selected
    updateProceedButtonState(); // Update button state on load

    document.querySelector('.proceed-btn')?.addEventListener('click', function () {
        const selectedMethod = document.querySelector('.payment-method.selected .method-name')?.textContent.trim();

        if (selectedMethod === "Debit/Credit Card" && !checkCardValidity()) {
            alert('Please complete all card details before proceeding.');
            return;
        }

        if (!userId) {
            alert('User not logged in. Please log in to complete the booking.');
            return;
        }

        const amount = total; 
        const paymentMethod = selectedMethod;
        const today = new Date();
        const paymentDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

        const bookingData = {
            user_id: userId,
            flight_id: departId,
            booking_date: paymentDate,
            status: 'Confirmed', 
            class_id: classId, 
            selected_seats: selectedSeats, 
            total_amount: amount,
            
            ticket: ticket, 
            baggage: baggage,
            meal: meal,
            taxPrice: taxPrice,
            // REMOVED: discount: discount, // No longer send discount data
            activeAdults: activeAdults, 
            activeChildren: activeChildren, 
            num_passenger: numPassengers, 
            flight_date: flightDate 
        };

        console.log("DEBUG in payment.js: Sending bookingData:", bookingData);

        fetch('insertFlightPayment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
        .then(res => res.json())
        .then(response => {
            console.log("Server Response:", response);
            if (response.success) {
                alert('Payment successful and booking confirmed!');
                sessionStorage.removeItem('flightSearch'); 
                sessionStorage.removeItem('selectedSeats'); 
                
                sessionStorage.removeItem('lastBookingDetails'); 
                sessionStorage.removeItem('lastSelectedSeats'); 
                sessionStorage.removeItem('lastTotalAmount'); 
        
                window.location.href = `payment_complete.php?bookingId=${response.bookingId}`; 
            } else {
                alert('Payment failed: ' + (response.error || 'Unknown error.'));
            }
        })
        .catch(err => {
            console.error("Error:", err);
            alert("An error occurred during payment. Please try again.");
        });
    });
});