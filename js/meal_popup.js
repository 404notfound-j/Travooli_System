// meal_popup.js
let currentMealPopup_Name = "No meal";
let currentMealPopup_Price = 0;
let currentMealPopup_Id = null; // Store the ID (e.g., 'ML01', 'ML02', or null for No Meal)


// Named functions for event handlers
function handleMealOptionClick() {
    document.querySelectorAll('.meal-option').forEach(opt => opt.classList.remove('selected'));
    this.classList.add('selected');
    currentMealPopup_Name = this.querySelector('.meal-name').textContent.trim();
    currentMealPopup_Price = parseFloat(this.dataset.price);
    currentMealPopup_Id = this.dataset.meal === 'no-meal' ? null : this.dataset.meal; // Get meal ID
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

        // --- NEW: Update mealId in allPassengersDetails ---
        const passengerIndex = parseInt(sessionStorage.getItem('editingAddonPassengerIndex'));
        const allPassengersDetails = JSON.parse(sessionStorage.getItem('allPassengersDetails') || '[]');
        if (!isNaN(passengerIndex) && allPassengersDetails[passengerIndex]) {
            allPassengersDetails[passengerIndex].mealId = currentMealPopup_Id;
            sessionStorage.setItem('allPassengersDetails', JSON.stringify(allPassengersDetails)); // Persist updated data
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
            const correspondingOption = Array.from(mealOptions).find(opt => opt.dataset.meal === initialMealId);
            if (correspondingOption) {
                initialMealName = correspondingOption.querySelector('.meal-name').textContent.trim();
                initialMealPrice = parseFloat(correspondingOption.dataset.price);
            }
        } else { // Fallback to current DOM value if no specific ID in data, or No Meal
             let currentMealValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
             if (currentMealValueSpan) {
                initialMealName = currentMealValueSpan.textContent.trim();
                initialMealPrice = parseFloat(currentMealValueSpan.dataset.price) || 0;
             }
        }
    }
    console.log('Initial meal state:', {initialMealName, initialMealPrice, initialMealId});

    let matchedOptionFound = false;
    mealOptions.forEach(option => {
        let optionDisplayName = option.querySelector('.meal-name').textContent.trim();
        let optionPrice = parseFloat(option.dataset.price) || 0;
        let optionId = option.dataset.meal === 'no-meal' ? null : option.dataset.meal; // Get meal ID from data-meal

        if (optionDisplayName === initialMealName && optionPrice === initialMealPrice || optionId === initialMealId) {
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
        const noMealOption = document.querySelector('.meal-option[data-meal="no-meal"]');
        if (noMealOption) {
            noMealOption.classList.add('selected');
            currentMealPopup_Name = "No meal";
            currentMealPopup_Price = 0;
            currentMealPopup_Id = null;
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