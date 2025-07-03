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
    
        const totalTicketCost = baseTicketPrice * passengerCount; //Multiply by passenger count
        const total = totalTicketCost + totalMealCost + totalBaggageCost + taxPrice - discount;
        if (flightPriceEl) {
            flightPriceEl.textContent = `RM ${totalTicketCost}`;
        }
        mealPriceEl.textContent = `RM ${totalMealCost}`;
        baggagePriceEl.textContent = `RM ${totalBaggageCost}`;
        totalPriceEl.textContent = `RM ${total}`;
    }
    

    updatePrices(); // Initial call

    // Make it globally callable from other scripts like meal_popup.js or bag_popup.js
    window.updatePrices = updatePrices;
});
