document.addEventListener('DOMContentLoaded', function () {
    const popupOverlay = document.getElementById('popup-overlay');
    const popupBody = document.getElementById('popup-body');
  
    // Handle click on all edit baggage buttons
    document.querySelectorAll('.edit-baggage-btn').forEach(button => {
        button.addEventListener('click', () => {
          document.querySelectorAll('.edit-baggage-btn').forEach(btn => btn.classList.remove('active'));
          button.classList.add('active');
      
          fetch('bag_popup.php')
            .then(res => res.text())
            .then(html => {
              popupBody.innerHTML = html;
              popupOverlay.classList.remove('hidden');
              document.body.classList.add('blurred');
              setupBagPopupEvents();
            });
        });
      });      
  
      function setupBagPopupEvents() {
        const bagOptions = popupBody.querySelectorAll('.bag-option');
        const quantitySection = popupBody.querySelector('.bag-quantity-section');
        const plusBtn = popupBody.querySelector('.quantity-btn.plus');
        const minusBtn = popupBody.querySelector('.quantity-btn.minus');
        const quantityDisplay = popupBody.querySelector('.quantity-display');
        const saveBtn = popupBody.querySelector('.popup-save-btn');
        const closeBtn = popupBody.querySelector('.popup-close');
      
        let selectedBagType = "10kg"; 
        let quantity = 1;
      
        function updateQuantityDisplay() {
          quantityDisplay.textContent = quantity;
        }
      
        bagOptions.forEach(option => {
          option.addEventListener('click', function () {
            bagOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedBagType = this.getAttribute('data-bag');
            quantity = 1;
            updateQuantityDisplay();
          });
        });
      
        plusBtn?.addEventListener('click', () => {
          if (selectedBagType === "10kg") {
            if (quantity < 2) {
              quantity++;
              updateQuantityDisplay();
            } else {
              alert("You can only select up to 2 free 10kg bags.");
            }
          } else {
            if (quantity < 10) {
              quantity++;
              updateQuantityDisplay();
            } else {
              alert("Maximum 10 pieces allowed.");
            }
          }
        });
      
        minusBtn?.addEventListener('click', () => {
          if (quantity > 1) {
            quantity--;
            updateQuantityDisplay();
          }
        });
      
        closeBtn?.addEventListener('click', () => {
          popupOverlay.classList.add('hidden');
          document.body.classList.remove('blurred');
        });
      
        saveBtn?.addEventListener('click', function (e) {
          e.preventDefault();
      
          const selectedOption = popupBody.querySelector('.bag-option.selected');
          const pricePerUnit = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
      
          const openerButton = document.querySelector('.edit-baggage-btn.active');
          const totalCost = pricePerUnit * quantity;
      
          if (openerButton) {
            const addonValue = openerButton.closest('.addon-details')?.querySelector('.addon-value');
            if (addonValue) {
              addonValue.textContent = `${quantity} piece${quantity > 1 ? 's' : ''}, ${selectedBagType}`;
              addonValue.setAttribute('data-price', totalCost.toFixed(2));
            }
      
            openerButton.classList.remove('active');
          }
      
          popupOverlay.classList.add('hidden');
          document.body.classList.remove('blurred');
          if (typeof updatePrices === 'function') {
            updatePrices();
          }
        });
      }

  });
  