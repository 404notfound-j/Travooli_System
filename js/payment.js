document.addEventListener('DOMContentLoaded', function () {
    const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');

    const total = parseFloat(flightSearch.finalTotalPrice || 0);
    const ticket = parseFloat(flightSearch.ticketPrice || 0);
    const baggage = parseFloat(flightSearch.baggagePrice || 0);
    const meal = parseFloat(flightSearch.mealPrice || 0);
    const taxPrice = parseFloat(flightSearch.taxPrice || 0);

    const activeAdults = parseInt(flightSearch.activeAdults) || 1;
    const activeChildren = parseInt(flightSearch.activeChildren) || 0;
    const numPassengers = activeAdults + activeChildren;

    const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
    
    const departId = flightSearch.depart || flightSearch.selectedFlight || '';
    const classId = flightSearch.classId || '';
    const flightDate = flightSearch.departDate || '';
    const trip = flightSearch.trip || '';
    const tripType = trip === 'one' ? 'one way' : 'round' ? 'round trip' : trip;


    const userId = window.currentUserId || null;
    if (!userId || userId === 'null') {
        alert('User not logged in. Please log in to complete the booking.');
        return;
    }

    // Display pricing
    document.getElementById('flight-price').textContent = `RM ${ticket.toFixed(2)}`;
    document.querySelector('.baggage-price').textContent = `RM ${baggage.toFixed(2)}`;
    document.querySelector('.meal-price').textContent = `RM ${meal.toFixed(2)}`;
    const taxesFeesEl = document.querySelector('.price-item span[data-tax-display]');
    if (taxesFeesEl) {
        taxesFeesEl.textContent = `RM ${taxPrice.toFixed(2)}`;
    } else {
        const defaultTaxesEl = document.querySelector('.price-item:nth-child(4) span:nth-child(2)');
        if (defaultTaxesEl) defaultTaxesEl.textContent = `RM ${taxPrice.toFixed(2)}`;
    }
    document.getElementById('total').textContent = `RM ${total.toFixed(2)}`;

    // Update ticket label
    let label = 'Tickets (';
    if (activeAdults > 0) label += `${activeAdults} Adult${activeAdults > 1 ? 's' : ''}`;
    if (activeChildren > 0) {
        if (activeAdults > 0) label += ', ';
        label += `${activeChildren} Child${activeChildren > 1 ? 'ren' : ''}`;
    }
    label += ')';
    const ticketLabel = document.getElementById('ticket-count-label');
    if (ticketLabel) ticketLabel.textContent = label;

    // Payment method and card form setup
    const selectedMethod = document.querySelector('.payment-method.selected')?.dataset.method || 'unknown';
    const radioButtons = document.querySelectorAll('.radio-button');
    const proceedBtn = document.querySelector('.proceed-btn');
    const cardFields = document.querySelectorAll('.card-input');

    function setCardFieldsEnabled(enabled) {
        cardFields.forEach(input => {
            input.disabled = !enabled;
            input.style.opacity = enabled ? '1' : '0.5';
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
        proceedBtn.classList.toggle('disabled', proceedBtn.disabled);
    }

    const paymentMethods = document.querySelectorAll('.payment-method');


    paymentMethods.forEach((method) => {
        method.addEventListener('click', function () {
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

    const cardNumberInput = document.querySelector('input[placeholder="Card Number"]');
    const expiryInput = document.querySelector('input[placeholder="Expiration Date"]');
    const cvvInput = document.querySelector('input[placeholder="CVV"]');

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
            e.target.value = value.match(/.{1,4}/g)?.join(' ').substring(0, 19) || value;
        });
    }

    if (expiryInput) {
        expiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) value = value.substring(0, 2) + '/' + value.substring(2, 4);
            e.target.value = value;
        });
    }

    if (cvvInput) {
        cvvInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.substring(0, 3);
        });
    }

    setCardFieldsEnabled(false);
    updateProceedButtonState();

    proceedBtn?.addEventListener('click', function () {
        const selectedMethod = document.querySelector('.payment-method.selected')?.dataset.method || 'unknown';
        if (!selectedMethod) {
            alert("Please select a payment method.");
            return;
        }
        console.log("✅ Final selected payment method:", selectedMethod);


        if (selectedMethod === "Debit/Credit Card" && !checkCardValidity()) {
            alert("Please complete all card details.");
            return;
        }

        const paymentDate = new Date().toISOString().split('T')[0];
        const passengerDetails = JSON.parse(sessionStorage.getItem('passenger_details') || '[]');

        const bookingData = {
            user_id: userId,
            flight_id: departId,
            trip_type: tripType,
            booking_date: paymentDate,
            status: 'confirmed',
            class_id: classId,
            selected_seats: selectedSeats,
            total_amount: total,
            ticket: ticket,
            baggage: baggage,
            meal: meal,
            taxPrice: taxPrice,
            activeAdults: activeAdults,
            activeChildren: activeChildren,
            num_passenger: numPassengers,
            flight_date: flightDate,
            passengers: passengerDetails,
            payment_method: selectedMethod
            
        };

        console.log("📦 Sending bookingData:", bookingData);

        fetch('insertFlightPayment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        })
        .then(res => res.text())
        .then(text => {
            console.log("Raw Response:", text);
            const response = JSON.parse(text);
        
            if (response.success) {
                alert('✅ Payment successful and booking confirmed!');
                sessionStorage.clear();
                window.location.href = `payment_complete.php?bookingId=${response.bookingId}`;
            } else {
                alert('❌ Payment failed: ' + (response.error || 'Unknown error.'));
            }
        })
        .catch(err => {
            console.error("❌ Network error:", err);
            alert("A server error occurred. Please try again.");
        });        
    });
});
