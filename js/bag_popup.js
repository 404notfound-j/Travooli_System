// Bag popup functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get all bag option elements
    const bagOptions = document.querySelectorAll('.bag-option');
    
    // Add click event listener to each bag option
    bagOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove 'selected' class from all options
            bagOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add 'selected' class to clicked option
            this.classList.add('selected');
            
            // Optional: Log the selected bag for debugging
            const selectedBag = this.getAttribute('data-bag');
            console.log('Selected bag:', selectedBag);
        });
    });
    
    // Handle save button click
    const saveButton = document.querySelector('.popup-save-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for now
            
            // Get the currently selected bag
            const selectedOption = document.querySelector('.bag-option.selected');
            const selectedBag = selectedOption ? selectedOption.getAttribute('data-bag') : null;
            const quantity = document.querySelector('.quantity-display').textContent;
            
            if (selectedBag) {
                console.log('Saving bag selection:', selectedBag, 'Quantity:', quantity);
                
                // Here you can add your save logic
                // For example: send to server, update localStorage, etc.
                
                // Close the popup after saving
                const popup = document.querySelector('.popup-bg');
                if (popup) {
                    popup.style.display = 'none';
                }
            }
        });
    }
});

// Quantity increment/decrement functions
function incrementQuantity() {
    const display = document.querySelector('.quantity-display');
    let currentValue = parseInt(display.textContent);
    if (currentValue < 10) { // Set maximum limit
        display.textContent = currentValue + 1;
        console.log('Quantity increased to:', currentValue + 1);
    }
}

function decrementQuantity() {
    const display = document.querySelector('.quantity-display');
    let currentValue = parseInt(display.textContent);
    if (currentValue > 1) { // Set minimum limit
        display.textContent = currentValue - 1;
        console.log('Quantity decreased to:', currentValue - 1);
    }
} 