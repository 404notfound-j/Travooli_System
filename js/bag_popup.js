// bag_popup.js

// Global state variables for the currently open bag popup's selection
let currentBagPopup_PricePerPiece = 0;
let currentBagPopup_Quantity = 0;
let currentBagPopup_WeightText = "0kg";

// Named functions for event handlers (defined outside setup function to be consistent for remove/add)
function handleBagOptionClick() {
    document.querySelectorAll('.bag-option').forEach(opt => opt.classList.remove('selected'));
    this.classList.add('selected');
    currentBagPopup_PricePerPiece = parseFloat(this.dataset.price);
    currentBagPopup_WeightText = this.dataset.bag;
    console.log('Bag option clicked:', this.dataset.bag, 'Price per piece:', currentBagPopup_PricePerPiece);
}

function handleQuantityDecrement() {
    let quantityDisplay = document.querySelector('.quantity-display');
    let currentQuantity = parseInt(quantityDisplay.textContent, 10);
    if (currentQuantity > 1) {
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
        
        // Calculate total price for this specific baggage add-on
        const finalCalculatedPrice = currentBagPopup_PricePerPiece * currentBagPopup_Quantity;
        
        addonValueSpan.textContent = `${currentBagPopup_Quantity} piece, ${currentBagPopup_WeightText}`;
        addonValueSpan.dataset.price = finalCalculatedPrice.toFixed(2); // Store the calculated total price for this item
        console.log('Updating main page baggage:', addonValueSpan.textContent, 'Price:', addonValueSpan.dataset.price);

        // Call the global updatePrices function from popupAddFlight.js
        if (window.updatePrices && typeof window.updatePrices === 'function') {
            window.updatePrices();
            console.log('window.updatePrices called successfully.');
        } else {
            console.warn("window.updatePrices is not defined. Price card may not update. Check script loading order.");
        }
    } else {
        console.warn('No active baggage edit button found. Cannot save.');
    }
    // Programmatically click the close button
    const closeBtn = document.querySelector('.popup-close');
    if(closeBtn) closeBtn.click();
}

function handleBagCloseClick() {
    let activeEditButton = document.querySelector('.edit-baggage-btn.active');
    if (activeEditButton) {
        activeEditButton.classList.remove('active'); // Remove active class from the edit button when closing
        console.log('Active class removed from baggage edit button.');
    }
    // Assuming popup-bg is the outer container with the hidden class
    this.closest('.popup-bg').classList.add('hidden');
    document.body.classList.remove('blurred'); // Remove blur from body
    console.log('Baggage popup closed.');
}


// --- Crucial function: Called by popupAddFlight.js when baggage popup is opened ---
window.setupBagPopupEvents = function() {
    console.log('setupBagPopupEvents called.');
    const bagOptions = document.querySelectorAll('.bag-option');
    const quantityDisplay = document.querySelector('.quantity-display');
    const saveBtn = document.querySelector('.popup-save-btn');
    const closeBtn = document.querySelector('.popup-close');

    // --- Initialization: Set popup state based on current passenger's selection ---
    let activeEditButton = document.querySelector('.edit-baggage-btn.active');
    
    let initialQuantity = 0; // Default before parsing
    let initialWeightText = "0kg";
    let initialTotalPrice = 0;

    if (activeEditButton) {
        let currentBagValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
        if (currentBagValueSpan) {
            let currentBagText = currentBagValueSpan.textContent.trim();
            initialTotalPrice = parseFloat(currentBagValueSpan.dataset.price) || 0;

            let parts = currentBagText.match(/(\d+)\s*piece(?:s)?,\s*(\d+)\s*(kg)/i);
            if (parts && parts.length >= 4) {
                initialQuantity = parseInt(parts[1], 10);
                initialWeightText = `${parts[2]}${parts[3]}`;
            } else {
                initialQuantity = 0; // Fallback to 0 if parsing fails
                initialWeightText = "10kg"; // Assume default for 0 price
            }
        }
    }

    // Set popup quantity display - if initialQuantity is 0, default to 1, otherwise keep initial.
    currentBagPopup_Quantity = (initialQuantity === 0 && initialTotalPrice === 0) ? 1 : initialQuantity; // NEW Default to 1 if no existing paid bag
    quantityDisplay.textContent = currentBagPopup_Quantity;
    console.log('Initial baggage state:', {initialQuantity, initialWeightText, initialTotalPrice, effectiveQuantity: currentBagPopup_Quantity});

    let matchedOptionFound = false;
    bagOptions.forEach(option => {
        let optionWeight = option.dataset.bag;
        let optionPricePerPiece = parseFloat(option.dataset.price) || 0;

        // If this is the "10kg Free" option
        if (optionWeight === "10kg" && optionPricePerPiece === 0) {
            // Select "10kg Free" if total price is 0 AND initial quantity was 0, or if it was explicitly 10kg free
            if (initialTotalPrice === 0 && (initialQuantity === 0 || initialWeightText === "10kg")) {
                option.classList.add('selected');
                currentBagPopup_PricePerPiece = 0;
                currentBagPopup_WeightText = "10kg";
                matchedOptionFound = true;
            } else {
                option.classList.remove('selected');
            }
        } else {
            // For paid options: match by weight and ensure the price per piece aligns if quantity > 0
            if (currentBagPopup_Quantity > 0 && optionWeight === initialWeightText && (initialTotalPrice / currentBagPopup_Quantity).toFixed(2) === optionPricePerPiece.toFixed(2)) {
                 option.classList.add('selected');
                 currentBagPopup_PricePerPiece = optionPricePerPiece;
                 currentBagPopup_WeightText = optionWeight;
                 matchedOptionFound = true;
            } else {
                option.classList.remove('selected');
            }
        }
    });

    // If no option was specifically matched (e.g., a new passenger, or first time selecting),
    // ensure a default paid option (e.g., 25kg) is selected and quantity is 1 if it wasn't the free 10kg.
    if (!matchedOptionFound) {
        bagOptions.forEach(opt => opt.classList.remove('selected'));
        const paidOption = document.querySelector('.bag-option[data-bag="25kg"]'); // Default to 25kg if nothing else matches
        if (paidOption) {
            paidOption.classList.add('selected');
            currentBagPopup_PricePerPiece = parseFloat(paidOption.dataset.price);
            currentBagPopup_WeightText = paidOption.dataset.bag;
            // Quantity is already set to 1 or initial quantity above.
        } else { // Fallback if no 25kg option
            const free10kgOption = document.querySelector('.bag-option[data-bag="10kg"]');
            if (free10kgOption) {
                free10kgOption.classList.add('selected');
                currentBagPopup_PricePerPiece = 0;
                currentBagPopup_WeightText = "10kg";
                currentBagPopup_Quantity = 0; // If 10kg free, quantity starts at 0 unless user adds it
                quantityDisplay.textContent = 0;
            }
        }
    }
    console.log('Baggage popup initialized. Current state:', {currentBagPopup_Quantity, currentBagPopup_WeightText, currentBagPopup_PricePerPiece});


    // --- Event Listeners for Popup Controls (re-attach every time popup is loaded) ---
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