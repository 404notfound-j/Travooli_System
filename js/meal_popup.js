// meal_popup.js
let currentMealPopup_Name = "No meal";
let currentMealPopup_Price = 0;
let currentMealPopup_Id = null; // Store the ID (e.g., 'M01', 'ML01', 'ML02')


// Named functions for event handlers
function handleMealOptionClick() {
    document.querySelectorAll('.meal-option').forEach(opt => opt.classList.remove('selected'));
    this.classList.add('selected');
    currentMealPopup_Name = this.querySelector('.meal-name').textContent.trim();
    currentMealPopup_Price = parseFloat(this.dataset.price);
    currentMealPopup_Id = this.dataset.mealId; // MODIFIED: Get ID from data-meal-id
    console.log('Meal option clicked:', currentMealPopup_Name, 'Price:', currentMealPopup_Price, 'ID:', currentMealPopup_Id);
}

function handleMealSaveClick() {
    let activeEditButton = document.querySelector('.edit-meals-btn.active');
    console.log('Save button clicked. Active edit button:', activeEditButton);

    if (activeEditButton) {
        let addonValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
        if (!addonValueSpan) {
            console.error('Error: addon-value span not found for active passenger.');
            return;
        }
        
        addonValueSpan.textContent = currentMealPopup_Name;
        addonValueSpan.dataset.price = currentMealPopup_Price.toFixed(2);
        console.log('Updating main page meal:', addonValueSpan.textContent, 'Price:', addonValueSpan.dataset.price);

        const passengerIndex = parseInt(sessionStorage.getItem('editingAddonPassengerIndex'));
        const allPassengersDetails = JSON.parse(sessionStorage.getItem('allPassengersDetails') || '[]');
        if (!isNaN(passengerIndex) && allPassengersDetails[passengerIndex]) {
            allPassengersDetails[passengerIndex].mealId = currentMealPopup_Id;
            sessionStorage.setItem('allPassengersDetails', JSON.stringify(allPassengersDetails));
            console.log('Passenger', passengerIndex, 'mealId updated to:', currentMealPopup_Id);
        } else {
            console.warn("Could not find passenger in allPassengersDetails to update mealId.");
        }
        sessionStorage.removeItem('editingAddonPassengerIndex'); // Clean up

        if (window.updatePrices && typeof window.updatePrices === 'function') {
            window.updatePrices();
            console.log('window.updatePrices called successfully.');
        } else {
            console.warn("window.updatePrices is not defined. Price card may not update. Check script loading order.");
        }
    } else {
        console.warn('No active meal edit button found. Cannot save.');
    }
    const closeBtn = document.querySelector('.popup-close');
    if(closeBtn) closeBtn.click();
}

function handleMealCloseClick() {
    let activeEditButton = document.querySelector('.edit-meals-btn.active');
    if (activeEditButton) {
        activeEditButton.classList.remove('active');
        console.log('Active class removed from meal edit button.');
    }
    this.closest('.popup-bg').classList.add('hidden');
    document.body.classList.remove('blurred');
    sessionStorage.removeItem('editingAddonPassengerIndex'); // Clean up on close
    console.log('Meal popup closed.');
}

window.setupMealPopupEvents = function() {
    console.log('setupMealPopupEvents called.');
    const mealOptions = document.querySelectorAll('.meal-option');
    const saveBtn = document.querySelector('.popup-save-btn');
    const closeBtn = document.querySelector('.popup-close');

    // --- Initialization: Set popup state based on current passenger's selection ---
    let activeEditButton = document.querySelector('.edit-meals-btn.active');
    
    let initialMealName = "No meal";
    let initialMealPrice = 0;
    let initialMealId = null;

    if (activeEditButton) {
        const passengerIndex = parseInt(sessionStorage.getItem('editingAddonPassengerIndex'));
        const allPassengersDetails = JSON.parse(sessionStorage.getItem('allPassengersDetails') || '[]');
        const pax = allPassengersDetails[passengerIndex];

        if (pax && pax.mealId) { // Check if mealId is set in our data array
            initialMealId = pax.mealId;
            // Find the meal option by ID to get its name and price
            const correspondingOption = Array.from(mealOptions).find(opt => opt.dataset.mealId === initialMealId); // MODIFIED: Match by data-meal-id
            if (correspondingOption) {
                initialMealName = correspondingOption.querySelector('.meal-name').textContent.trim();
                initialMealPrice = parseFloat(correspondingOption.dataset.price);
            }
        } else { // Fallback to current DOM value if no specific ID in data, or No Meal
             let currentMealValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
             if (currentMealValueSpan) {
                initialMealName = currentMealValueSpan.textContent.trim();
                initialMealPrice = parseFloat(currentMealValueSpan.dataset.price) || 0;
                // Also try to derive ID from text or assumed default if no mealId in paxData
                if (initialMealName === "No Meal") initialMealId = "M01"; // Default to M01 if it's 'No Meal' and no ID
             }
        }
    }
    // If no specific meal option matched (or if initialMealId is null/M01),
    // ensure M01 / "No meal" is selected by default if nothing else matches
    let matchedOptionFound = false;
    mealOptions.forEach(option => {
        let optionDisplayName = option.querySelector('.meal-name').textContent.trim();
        let optionPrice = parseFloat(option.dataset.price) || 0;
        let optionId = option.dataset.mealId; // Get meal ID from data-meal-id

        if ((optionDisplayName === initialMealName && optionPrice === initialMealPrice) || (optionId === initialMealId)) {
            option.classList.add('selected');
            currentMealPopup_Name = optionDisplayName;
            currentMealPopup_Price = optionPrice;
            currentMealPopup_Id = optionId;
            matchedOptionFound = true;
        } else {
            option.classList.remove('selected');
        }
    });

    if (!matchedOptionFound) {
        mealOptions.forEach(opt => opt.classList.remove('selected'));
        const noMealOption = document.querySelector('.meal-option[data-meal-id="M01"]'); // MODIFIED: Match by data-meal-id="M01"
        if (noMealOption) {
            noMealOption.classList.add('selected');
            currentMealPopup_Name = "No meal";
            currentMealPopup_Price = 0;
            currentMealPopup_Id = "M01"; // Set to M01 explicitly
        }
    }
    console.log('Meal popup initialized. Current state:', {currentMealPopup_Name, currentMealPopup_Price, currentMealPopup_Id});

    mealOptions.forEach(option => {
        option.removeEventListener('click', handleMealOptionClick);
        option.addEventListener('click', handleMealOptionClick);
    });

    if (saveBtn) {
        saveBtn.removeEventListener('click', handleMealSaveClick);
        saveBtn.addEventListener('click', handleMealSaveClick);
    }

    if (closeBtn) {
        closeBtn.removeEventListener('click', handleMealCloseClick);
        closeBtn.addEventListener('click', handleMealCloseClick);
    }
};