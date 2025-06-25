document.addEventListener('DOMContentLoaded', function () {
    const popupOverlay = document.getElementById('popup-overlay');
    const popupBody = document.getElementById('popup-body');
  
    // Handle click on all edit baggage buttons
    document.querySelectorAll('.edit-baggage-btn').forEach(button => {
        button.addEventListener('click', () => {
          // Remove .active from any other button
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
  
      bagOptions.forEach(option => {
        option.addEventListener('click', function () {
          // Deselect all, then select clicked
          bagOptions.forEach(opt => opt.classList.remove('selected'));
          this.classList.add('selected');
          console.log('Selected bag:', this.getAttribute('data-bag'));
        });
      });
  
      // Quantity buttons
      const plusBtn = popupBody.querySelector('.quantity-btn.plus');
      const minusBtn = popupBody.querySelector('.quantity-btn.minus');
      const display = popupBody.querySelector('.quantity-display');
  
      plusBtn?.addEventListener('click', () => {
        let val = parseInt(display.textContent);
        if (val < 10) display.textContent = val + 1;
      });
  
      minusBtn?.addEventListener('click', () => {
        let val = parseInt(display.textContent);
        if (val > 1) display.textContent = val - 1;
      });
  
      // Save & Close button
      const saveBtn = popupBody.querySelector('.popup-save-btn');
      const closeBtn = popupBody.querySelector('.popup-close');
  
      closeBtn?.addEventListener('click', () => {
        popupOverlay.classList.add('hidden');
        document.body.classList.remove('blurred');
      });
  
      saveBtn?.addEventListener('click', function (e) {
        e.preventDefault();
      
        const selectedOption = popupBody.querySelector('.bag-option.selected');
        const selectedBag = selectedOption?.getAttribute('data-bag');
        const pricePerUnit = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
        const quantity = parseInt(popupBody.querySelector('.quantity-display')?.textContent) || 1;
      
        const totalBaggagePrice = pricePerUnit * quantity;
      
        if (selectedBag) {
          const openerButton = document.querySelector('.edit-baggage-btn.active');
      
          if (openerButton) {
            const addonValue = openerButton.closest('.addon-details')?.querySelector('.addon-value');
            if (addonValue) {
              addonValue.textContent = `${quantity} piece${quantity > 1 ? 's' : ''}, ${selectedBag}`;
              addonValue.setAttribute('data-price', totalBaggagePrice); 
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
  });
  