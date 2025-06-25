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
        });
      });
      const closeBtn = popupBody.querySelector('.popup-close');
        closeBtn?.addEventListener('click', () => {
        popupOverlay.classList.add('hidden');
        popupBody.innerHTML = ''; // ✅ clear the popup contents
        document.body.classList.remove('blurred');
        document.querySelectorAll('.edit-meals-btn').forEach(btn => btn.classList.remove('active')); // optional cleanup
    });
      const saveBtn = popupBody.querySelector('.popup-save-btn');
      if (saveBtn) {
        saveBtn.addEventListener('click', function (e) {
          e.preventDefault();
          const selectedOption = popupBody.querySelector('.meal-option.selected');
          const selectedMeal = selectedOption ? selectedOption.getAttribute('data-meal') : null;
          if (selectedMeal) {
            const selectedPrice = selectedOption.getAttribute('data-price') || 0;
            const openerButton = document.querySelector('.edit-meals-btn.active');
            if (openerButton) {
              const addonValue = openerButton.closest('.addon-details')?.querySelector('.addon-value');
              if (addonValue) {
                addonValue.textContent = selectedMeal;
                addonValue.setAttribute('data-price', selectedPrice);
              }
              openerButton.classList.remove('active');
            }
            popupOverlay.classList.add('hidden');
            document.body.classList.remove('blurred');
            if (typeof updatePrices === 'function') {
              updatePrices();
            }
          }          
        });
      }
    }
  });
  