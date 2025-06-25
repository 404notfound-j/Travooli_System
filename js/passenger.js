document.addEventListener('DOMContentLoaded', function () {
    const baggagePriceEl = document.querySelector('.baggage-price');
    const mealPriceEl = document.querySelector('.meal-price');
    const totalPriceEl = document.querySelector('.total-value');

    const baseTicketPrice = 340;
    const taxPrice = 121;
    const discount = 0;

    function updatePrices() {
        let totalMealCost = 0;
        let totalBaggageCost = 0;

        // Loop through all addon blocks (per passenger per segment)
        const addonItems = document.querySelectorAll('.passenger-addon-item');

        addonItems.forEach(item => {
            const addonValues = item.querySelectorAll('.addon-details .addon-value');

            const mealCost = parseFloat(addonValues[0]?.dataset.price) || 0;
            const baggageCost = parseFloat(addonValues[1]?.dataset.price) || 0;

            totalMealCost += mealCost;
            totalBaggageCost += baggageCost;
        });

        mealPriceEl.textContent = `RM ${totalMealCost}`;
        baggagePriceEl.textContent = `RM ${totalBaggageCost}`;

        const total = baseTicketPrice + totalMealCost + totalBaggageCost + taxPrice - discount;
        totalPriceEl.textContent = `RM ${total}`;
    }

    updatePrices(); // Initial call

    // Make it globally callable from other scripts like meal_popup.js or bag_popup.js
    window.updatePrices = updatePrices;
});
