document.addEventListener('DOMContentLoaded', function () {
    const popupOverlay = document.getElementById('popup-overlay');
    const popupBody = document.getElementById('popup-body');
  
    // Attach click to all meal edit buttons
    document.querySelectorAll('.edit-meals-btn').forEach(button => {
      button.addEventListener('click', () => {
        // Mark clicked button as active
        document.querySelectorAll('.edit-meals-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
  
        // Load the popup content
        fetch('meal_popup.php')
          .then(res => res.text())
          .then(html => {
            popupBody.innerHTML = html;
            popupOverlay.classList.remove('hidden');
            document.body.classList.add('blurred');
            setupMealPopupEvents(); // Re-bind logic for dynamic content
          });
      });
    });
  
    // Defined only once
    function setupMealPopupEvents() {
      const mealOptions = popupBody.querySelectorAll('.meal-option');
  
      mealOptions.forEach(option => {
        option.addEventListener('click', function () {
          mealOptions.forEach(opt => opt.classList.remove('selected'));
          this.classList.add('selected');
  
          const selectedMeal = this.getAttribute('data-meal');
          console.log('Selected meal:', selectedMeal);
        });
      });
  
      const saveButton = popupBody.querySelector('.popup-save-btn');
      if (saveButton) {
        saveButton.addEventListener('click', function (e) {
          e.preventDefault();
  
          const selectedOption = popupBody.querySelector('.meal-option.selected');
          const selectedMeal = selectedOption ? selectedOption.getAttribute('data-meal') : null;
  
          if (selectedMeal) {
            console.log('Saving meal selection:', selectedMeal);
  
            // Update the correct .addon-value
            const openerButton = document.querySelector('.edit-meals-btn.active');
            if (openerButton) {
              const addonValue = openerButton.closest('.addon-details')?.querySelector('.addon-value');
              if (addonValue) {
                addonValue.textContent = selectedMeal;
              }
              openerButton.classList.remove('active');
            }
  
            popupOverlay.classList.add('hidden');
            document.body.classList.remove('blurred');
          }
        });
      }
    }
  });
  