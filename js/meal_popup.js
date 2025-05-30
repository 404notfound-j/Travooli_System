// Meal popup functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get all meal option elements
    const mealOptions = document.querySelectorAll('.meal-option');
    
    // Add click event listener to each meal option
    mealOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove 'selected' class from all options
            mealOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add 'selected' class to clicked option
            this.classList.add('selected');
            
            // Optional: Log the selected meal for debugging
            const selectedMeal = this.getAttribute('data-meal');
            console.log('Selected meal:', selectedMeal);
            
            // Optional: You can add additional functionality here
            // For example, updating a hidden form field with the selected value
            // or calling an API to save the selection
        });
    });
    
    // Optional: Handle save button click
    const saveButton = document.querySelector('.popup-save-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission for now
            
            // Get the currently selected meal
            const selectedOption = document.querySelector('.meal-option.selected');
            const selectedMeal = selectedOption ? selectedOption.getAttribute('data-meal') : null;
            
            if (selectedMeal) {
                console.log('Saving meal selection:', selectedMeal);
                
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
