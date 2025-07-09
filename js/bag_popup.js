// bag_popup.js
let currentBagPopup_PricePerPiece = 0;
let currentBagPopup_Quantity = 0;
let currentBagPopup_WeightText = "0kg";
let currentBagPopup_Id = null; // Store the ID (e.g., 'BG10', 'BG25', 'BG50')


// Named functions for event handlers
function handleBagOptionClick() {
    document.querySelectorAll('.bag-option').forEach(opt => opt.classList.remove('selected'));
    this.classList.add('selected');
    currentBagPopup_PricePerPiece = parseFloat(this.dataset.price);
    currentBagPopup_WeightText = this.dataset.bag;
    currentBagPopup_Id = this.dataset.bagId; // Use data-bag-id from HTML
    console.log('Bag option clicked:', this.dataset.bag, 'Price per piece:', currentBagPopup_PricePerPiece, 'ID:', currentBagPopup_Id);
}

function handleQuantityDecrement() {
    let quantityDisplay = document.querySelector('.quantity-display');
    let currentQuantity = parseInt(quantityDisplay.textContent, 10);
    if (currentQuantity > 0) {
        quantityDisplay.textContent = currentQuantity - 1;
        currentBagPopup_Quantity = currentQuantity - 1;
        console.log('Quantity decremented to:', currentBagPopup_Quantity);
    }
}

function handleQuantityIncrement() {
    let quantityDisplay = document.querySelector('.quantity-display');
    let currentQuantity = parseInt(quantityDisplay.textContent, 10);
    quantityDisplay.textContent = currentQuantity + 1;
    currentBagPopup_Quantity = currentQuantity + 1;
    console.log('Quantity incremented to:', currentBagPopup_Quantity);
}

function handleBagSaveClick() {
    let activeEditButton = document.querySelector('.edit-baggage-btn.active');
    console.log('Save button clicked. Active edit button:', activeEditButton);

    if (activeEditButton) {
        let addonValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
        if (!addonValueSpan) {
            console.error('Error: addon-value span not found for active passenger.');
            return;
        }
        
        const finalCalculatedPrice = currentBagPopup_PricePerPiece * currentBagPopup_Quantity;
        
        addonValueSpan.textContent = `${currentBagPopup_Quantity} piece, ${currentBagPopup_WeightText}`;
        addonValueSpan.dataset.price = finalCalculatedPrice.toFixed(2);
        console.log('Updating main page baggage:', addonValueSpan.textContent, 'Price:', addonValueSpan.dataset.price);

        // --- NEW: Update baggageId in allPassengersDetails ---
        const passengerIndex = parseInt(sessionStorage.getItem('editingAddonPassengerIndex'));
        const allPassengersDetails = JSON.parse(sessionStorage.getItem('allPassengersDetails') || '[]');
        if (!isNaN(passengerIndex) && allPassengersDetails[passengerIndex]) {
            allPassengersDetails[passengerIndex].baggageId = currentBagPopup_Id; // Store the ID
            sessionStorage.setItem('allPassengersDetails', JSON.stringify(allPassengersDetails)); // Persist updated data
            console.log('Passenger', passengerIndex, 'baggageId updated to:', currentBagPopup_Id);
        } else {
            console.warn("Could not find passenger in allPassengersDetails to update baggageId.");
        }
        sessionStorage.removeItem('editingAddonPassengerIndex'); // Clean up

        if (window.updatePrices && typeof window.updatePrices === 'function') {
            window.updatePrices();
            console.log('window.updatePrices called successfully.');
        } else {
            console.warn("window.updatePrices is not defined. Price card may not update. Check script loading order.");
        }
    } else {
        console.warn('No active baggage edit button found. Cannot save.');
    }
    const closeBtn = document.querySelector('.popup-close');
    if(closeBtn) closeBtn.click();
}

function handleBagCloseClick() {
    let activeEditButton = document.querySelector('.edit-baggage-btn.active');
    if (activeEditButton) {
        activeEditButton.classList.remove('active');
        console.log('Active class removed from baggage edit button.');
    }
    this.closest('.popup-bg').classList.add('hidden');
    document.body.classList.remove('blurred');
    sessionStorage.removeItem('editingAddonPassengerIndex'); // Clean up on close
    console.log('Baggage popup closed.');
}

window.setupBagPopupEvents = function() {
    console.log('setupBagPopupEvents called.');
    const bagOptions = document.querySelectorAll('.bag-option');
    const quantityDisplay = document.querySelector('.quantity-display');
    const saveBtn = document.querySelector('.popup-save-btn');
    const closeBtn = document.querySelector('.popup-close');

    // --- Initialization: Set popup state based on current passenger's selection ---
    let activeEditButton = document.querySelector('.edit-baggage-btn.active');
    
    let initialQuantity = 0;
    let initialWeightText = "0kg";
    let initialTotalPrice = 0;
    let initialBagId = null;

    if (activeEditButton) {
        const passengerIndex = parseInt(sessionStorage.getItem('editingAddonPassengerIndex'));
        const allPassengersDetails = JSON.parse(sessionStorage.getItem('allPassengersDetails') || '[]');
        const pax = allPassengersDetails[passengerIndex];

        if (pax && pax.baggageId) { // Check if baggageId is set in our data array
            initialBagId = pax.baggageId;
            // Find the bag option by its data-bag-id
            const correspondingOption = Array.from(bagOptions).find(opt => opt.dataset.bagId === initialBagId);

            if (correspondingOption) {
                initialWeightText = correspondingOption.dataset.bag;
                currentBagPopup_PricePerPiece = parseFloat(correspondingOption.dataset.price);
                // Also get quantity from existing text on the main page to initialize popup quantity
                let currentBagValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
                let currentBagText = currentBagValueSpan ? currentBagValueSpan.textContent.trim() : '0 piece, 0kg';
                let parts = currentBagText.match(/(\d+)\s*piece(?:s)?/i);
                initialQuantity = parseInt(parts ? parts[1] : '0', 10);
            }
        } else { // Fallback to current DOM value if no specific ID in data, or default
             let currentBagValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
             if (currentBagValueSpan) {
                let currentBagText = currentBagValueSpan.textContent.trim();
                initialTotalPrice = parseFloat(currentBagValueSpan.dataset.price) || 0;
                let parts = currentBagText.match(/(\d+)\s*piece(?:s)?,\s*(\d+)\s*(kg)/i);
                if (parts && parts.length >= 4) {
                    initialQuantity = parseInt(parts[1], 10);
                    initialWeightText = `${parts[2]}${parts[3]}`;
                }
             }
        }
    }
    
    currentBagPopup_Quantity = (initialQuantity === 0 && initialTotalPrice === 0) ? 1 : initialQuantity;
    quantityDisplay.textContent = currentBagPopup_Quantity;
    console.log('Initial baggage state:', {initialQuantity, initialWeightText, initialTotalPrice, initialBagId, effectiveQuantity: currentBagPopup_Quantity});

    let matchedOptionFound = false;
    bagOptions.forEach(option => {
        let optionWeight = option.dataset.bag;
        let optionPricePerPiece = parseFloat(option.dataset.price) || 0;
        let optionId = option.dataset.bagId; // Get ID from data-bag-id

        // Match by weight and ID, or if quantity > 0 and price matches
        if (optionWeight === initialWeightText && (optionId === initialBagId || (currentBagPopup_Quantity > 0 && (initialTotalPrice / currentBagPopup_Quantity).toFixed(2) === optionPricePerPiece.toFixed(2)))) {
            option.classList.add('selected');
            currentBagPopup_PricePerPiece = optionPricePerPiece;
            currentBagPopup_WeightText = optionWeight;
            currentBagPopup_Id = optionId;
            matchedOptionFound = true;
        } else {
            option.classList.remove('selected');
        }
    });

    if (!matchedOptionFound) {
        bagOptions.forEach(opt => opt.classList.remove('selected'));
        const defaultOption = document.querySelector('.bag-option[data-bag="10kg"]');
        if (defaultOption) {
            defaultOption.classList.add('selected');
            currentBagPopup_PricePerPiece = 0;
            currentBagPopup_WeightText = "10kg";
            currentBagPopup_Id = 'BG10'; // Default ID
            currentBagPopup_Quantity = 0; // Quantity is 0 for 10kg free by default
            quantityDisplay.textContent = 0;
        }
    }
    console.log('Baggage popup initialized. Current state:', {currentBagPopup_Quantity, currentBagPopup_WeightText, currentBagPopup_PricePerPiece, currentBagPopup_Id});

    bagOptions.forEach(option => {
        option.removeEventListener('click', handleBagOptionClick);
        option.addEventListener('click', handleBagOptionClick);
    });

    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');

    if (minusBtn) {
        minusBtn.removeEventListener('click', handleQuantityDecrement);
        minusBtn.addEventListener('click', handleQuantityDecrement);
    }
    if (plusBtn) {
        plusBtn.removeEventListener('click', handleQuantityIncrement);
        plusBtn.addEventListener('click', handleQuantityIncrement);
    }

    if (saveBtn) {
        saveBtn.removeEventListener('click', handleBagSaveClick);
        saveBtn.addEventListener('click', handleBagSaveClick);
    }

    if (closeBtn) {
        closeBtn.removeEventListener('click', handleBagCloseClick);
        closeBtn.addEventListener('click', handleBagCloseClick);
    }
};