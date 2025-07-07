document.addEventListener('DOMContentLoaded', function () {
    const baggagePriceEl = document.querySelector('.baggage-price');
    const mealPriceEl = document.querySelector('.meal-price');
    const totalPriceEl = document.querySelector('.total-value');
    const urlParams = new URLSearchParams(window.location.search);
    const baseTicketPrice = parseFloat(urlParams.get('price')) || 0;    
    const taxPrice = 121;
    const discount = 0;
    const flightPriceEl = document.querySelector('#flight-price');
    if (flightPriceEl) {
        flightPriceEl.textContent = `RM ${baseTicketPrice.toFixed(0)}`;
    }
    function updatePrices() {
        let totalMealCost = 0;
        let totalBaggageCost = 0;
        let passengerCount = document.querySelectorAll('.passenger-item').length;
        const addonItems = document.querySelectorAll('.passenger-addon-item');
    
        addonItems.forEach(item => {
            const addonValues = item.querySelectorAll('.addon-details .addon-value');
            const mealCost = parseFloat(addonValues[0]?.dataset.price) || 0;
            const baggageCost = parseFloat(addonValues[1]?.dataset.price) || 0;
    
            totalMealCost += mealCost;
            totalBaggageCost += baggageCost;
        });
    
        const totalTicketCost = baseTicketPrice * passengerCount;
        const subtotal = totalTicketCost + totalMealCost + totalBaggageCost;
        const taxPrice = subtotal * 0.06;
        const discount = 0;
        const total = subtotal + taxPrice - discount;
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
                taxPriceEl.textContent = `RM ${taxPrice.toFixed(2)}`;
        }
    }

    updatePrices(); 
    window.updatePrices = updatePrices;
});
