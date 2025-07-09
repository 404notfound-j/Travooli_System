// meal_popup.js

// Global state variables for the currently open meal popup's selection
let currentMealPopup_Name = "No meal";
let currentMealPopup_Price = 0;

// Named functions for event handlers
function handleMealOptionClick() {
    document.querySelectorAll('.meal-option').forEach(opt => opt.classList.remove('selected'));
    this.classList.add('selected');
    currentMealPopup_Name = this.querySelector('.meal-name').textContent.trim();
    currentMealPopup_Price = parseFloat(this.dataset.price);
    console.log('Meal option clicked:', currentMealPopup_Name, 'Price:', currentMealPopup_Price);
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
    console.log('Meal popup closed.');
}


// --- Crucial function: Called by popupAddFlight.js when meal popup is opened ---
window.setupMealPopupEvents = function() {
    console.log('setupMealPopupEvents called.');
    const mealOptions = document.querySelectorAll('.meal-option');
    const saveBtn = document.querySelector('.popup-save-btn');
    const closeBtn = document.querySelector('.popup-close');

    // --- Initialization: Set popup state based on current passenger's selection ---
    let activeEditButton = document.querySelector('.edit-meals-btn.active');
    
    let initialMealName = "No meal";
    let initialMealPrice = 0;

    if (activeEditButton) {
        let currentMealValueSpan = activeEditButton.closest('.addon-details').querySelector('.addon-value');
        if (currentMealValueSpan) {
            initialMealName = currentMealValueSpan.textContent.trim();
            initialMealPrice = parseFloat(currentMealValueSpan.dataset.price) || 0;
        }
    }
    console.log('Initial meal state:', {initialMealName, initialMealPrice});

    let matchedOptionFound = false;
    mealOptions.forEach(option => {
        let optionDisplayName = option.querySelector('.meal-name').textContent.trim();
        let optionPrice = parseFloat(option.dataset.price) || 0;

        if (optionDisplayName === initialMealName && optionPrice === initialMealPrice) {
            option.classList.add('selected');
            currentMealPopup_Name = optionDisplayName;
            currentMealPopup_Price = optionPrice;
            matchedOptionFound = true;
        } else {
            option.classList.remove('selected');
        }
    });

    // If no specific meal option matched, default to "No meal"
    if (!matchedOptionFound) {
        mealOptions.forEach(opt => opt.classList.remove('selected'));
        const noMealOption = document.querySelector('.meal-option[data-meal="no-meal"]');
        if (noMealOption) {
            noMealOption.classList.add('selected');
            currentMealPopup_Name = "No meal";
            currentMealPopup_Price = 0;
        }
    }
    console.log('Meal popup initialized. Current state:', {currentMealPopup_Name, currentMealPopup_Price});


    // --- Event Listeners for Popup Controls (re-attach every time popup is loaded) ---

    // Option selection click listener
    mealOptions.forEach(option => {
        option.removeEventListener('click', handleMealOptionClick);
        option.addEventListener('click', handleMealOptionClick);
    });

    // Save button listener
    if (saveBtn) {
        saveBtn.removeEventListener('click', handleMealSaveClick);
        saveBtn.addEventListener('click', handleMealSaveClick);
    }

    // Close button listener
    if (closeBtn) {
        closeBtn.removeEventListener('click', handleMealCloseClick);
        closeBtn.addEventListener('click', handleMealCloseClick);
    }
};