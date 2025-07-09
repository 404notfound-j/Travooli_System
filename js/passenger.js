document.addEventListener('DOMContentLoaded', function () {
    const baggagePriceEl = document.querySelector('.baggage-price');
    const mealPriceEl = document.querySelector('.meal-price');
    const totalPriceEl = document.querySelector('.total-value');
    
    // Retrieve the flightSearch object from sessionStorage to get the total flight price
    const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');
    const baseTicketPrice = parseFloat(flightSearch.totalPrice || 0); // Use totalPrice from sessionStorage

    const taxPrice = 121; // This initial value will be overwritten by updatePrices
    const discount = 0; // This initial value will be overwritten by updatePrices
    const flightPriceEl = document.querySelector('#flight-price');
    
    if (flightPriceEl) {
        flightPriceEl.textContent = `RM ${baseTicketPrice.toFixed(2)}`; // Set initial flight price from sessionStorage
    }

    function updatePrices() {
        let totalMealCost = 0;
        let totalBaggageCost = 0;
        
        // Calculate passenger count dynamically from the DOM
        let passengerCount = document.querySelectorAll('.passenger-item').length;

        const addonItems = document.querySelectorAll('.passenger-addon-item');
    
        addonItems.forEach(item => {
            const addonValues = item.querySelectorAll('.addon-details .addon-value');
            const mealCost = parseFloat(addonValues[0]?.dataset.price) || 0;
            const baggageCost = parseFloat(addonValues[1]?.dataset.price) || 0;
    
            totalMealCost += mealCost;
            totalBaggageCost += baggageCost;
        });
    
        // The baseTicketPrice retrieved from sessionStorage is already the total for all tickets.
        // So, no need to multiply by passengerCount here for totalTicketCost.
        const totalTicketCost = baseTicketPrice; // Use baseTicketPrice directly as it's already the total

        const subtotal = totalTicketCost + totalMealCost + totalBaggageCost;
        const calculatedTaxPrice = subtotal * 0.06; // Calculate 6% tax
        const calculatedDiscount = 0; // Currently no discount logic
        const total = subtotal + calculatedTaxPrice - calculatedDiscount;

        // Update individual components
        if (flightPriceEl) {
            flightPriceEl.textContent = `RM ${totalTicketCost.toFixed(2)}`;
        }
        mealPriceEl.textContent = `RM ${totalMealCost.toFixed(2)}`;
        baggagePriceEl.textContent = `RM ${totalBaggageCost.toFixed(2)}`;
        totalPriceEl.textContent = `RM ${total.toFixed(2)}`;
        
        // Update tax if element exists
        const taxPriceEl = document.querySelector('.tax-price');
            if (taxPriceEl) {
                taxPriceEl.textContent = `RM ${calculatedTaxPrice.toFixed(2)}`;
        }
    }

    updatePrices(); 
    // Expose updatePrices globally if other scripts need to call it (popupAddFlight.js does)
    window.updatePrices = updatePrices; 
});