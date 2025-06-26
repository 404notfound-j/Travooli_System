document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.querySelector('.add-passenger-btn');
    const popupOverlay = document.getElementById('popup-overlay');
    const popupBody = document.getElementById('popup-body');
  
    addBtn.addEventListener('click', () => {
      fetch('pass_info_popup.php')
        .then(res => res.text())
        .then(html => {
          popupBody.innerHTML = html;
          popupOverlay.classList.remove('hidden');
          document.body.classList.add('blurred');
  
          // Add Save behavior inside the loaded popup
          setTimeout(() => {
            const saveBtn = popupBody.querySelector('.popup-save-btn');
            const closeBtn = popupBody.querySelector('.popup-close');
  
            closeBtn?.addEventListener('click', () => {
              popupOverlay.classList.add('hidden');
              document.body.classList.remove('blurred');
            });
  
            saveBtn?.addEventListener('click', function (e) {
              e.preventDefault();
            
              const fname = popupBody.querySelector('#first_name').value.trim();
              const lname = popupBody.querySelector('#last_name').value.trim();
              const gender = popupBody.querySelector('#gender').value;
              const country = popupBody.querySelector('#country').value;
            
              if (!fname || !lname) return;
            
              const passengerList = document.querySelector('.passenger-list');
              const passengerItem = document.createElement('div');
              passengerItem.classList.add('passenger-item');
              passengerItem.innerHTML = `
                <input type="checkbox" checked>
                <div class="passenger-details">
                  <span class="passenger-name">${fname} ${lname}</span>
                  <span class="passenger-type">Adult / ${gender} / ${country}</span>
                </div>
                <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
              `;
              passengerList.appendChild(passengerItem);
              document.querySelectorAll('.trip-segment').forEach(segment => {
                console.log(document.querySelectorAll('.trip-segment').length); // Should be 2
                const newAddon = document.createElement('div');
                newAddon.classList.add('passenger-addon-item');
                newAddon.innerHTML = `
                  <div class="addons-row">
                    <div class="passenger-name">${fname} ${lname}</div>
                    <div class="addon-details">
                      <span class="addon-type">Meal Add-on</span>
                      <span class="addon-value" data-price="30">Multi-meal</span>
                      <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                    </div>
                    <div class="addon-details">
                      <span class="addon-type">Additional Baggage</span>
                      <span class="addon-value" data-price="20">1piece, 25kg</span>
                      <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                    </div>
                  </div>
                `;
                const addonContainer = segment.querySelector('.add-on-container') || segment;
                addonContainer.appendChild(newAddon);                
              });
              
              if (typeof updatePrices === 'function') updatePrices();
              popupOverlay.classList.add('hidden');
              document.body.classList.remove('blurred');
            });

            let currentEditingPassenger = null;
            const editBtn = passengerItem.querySelector('.edit-passenger-btn');
            editBtn.addEventListener('click', () => {
              const name = passengerItem.querySelector('.passenger-name').textContent.trim();
              const typeText = passengerItem.querySelector('.passenger-type').textContent.trim(); // <- corrected this line
              const [type, gender, country] = typeText.split(" / ");
              const [firstName, ...lastNameParts] = name.split(" ");
              const lastName = lastNameParts.join(" ");
            
              fetch('pass_info_popup.php')
                .then(res => res.text())
                .then(html => {
                  popupBody.innerHTML = html;
                  popupOverlay.classList.remove('hidden');
                  document.body.classList.add('blurred');
            
                  // Save reference for editing
                  currentEditingPassenger = passengerItem;
            
                  // Populate fields
                  document.getElementById('first_name').value = firstName;
                  document.getElementById('last_name').value = lastName;
                  document.getElementById('gender').value = gender.toLowerCase();
                  document.getElementById('country').value = country.toLowerCase();
            
                  setupPopupEvents(); // now it knows it's in edit mode
                });
            });
              
              popupOverlay.classList.add('hidden');
              document.body.classList.remove('blurred');
            });
          }, 100);
        });

// Handle Edit Passenger Buttons
document.querySelectorAll('.edit-passenger-btn').forEach(button => {
  button.addEventListener('click', () => {
    const passengerItem = button.closest('.passenger-item'); // get full block
    currentEditingPassenger = passengerItem; // <== KEY LINE

    const name = passengerItem.querySelector('.passenger-name').textContent.trim();
    const typeText = passengerItem.querySelector('.passenger-type').textContent.trim();
    const [type, gender, country] = typeText.split(" / ");
    const [firstName, ...lastNameParts] = name.split(" ");
    const lastName = lastNameParts.join(" ");

    fetch('pass_info_popup.php')
      .then(res => res.text())
      .then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');

        // Populate the fields
        document.getElementById('first_name').value = firstName;
        document.getElementById('last_name').value = lastName;
        document.getElementById('gender').value = gender.toLowerCase();
        document.getElementById('country').value = country.toLowerCase();
        setupPopupEvents();
      });
  });
});


function setupPopupEvents() {
  const saveBtn = popupBody.querySelector('.popup-save-btn');
  const closeBtn = popupBody.querySelector('.popup-close');

  closeBtn?.addEventListener('click', () => {
    popupOverlay.classList.add('hidden');
    document.body.classList.remove('blurred');
  });

  saveBtn?.addEventListener('click', function (e) {
    e.preventDefault();

    const fname = popupBody.querySelector('#first_name').value.trim();
    const lname = popupBody.querySelector('#last_name').value.trim();
    const gender = popupBody.querySelector('#gender').value;
    const country = popupBody.querySelector('#country').value;

    if (!fname || !lname) return;

    // If editing an existing passenger
    if (currentEditingPassenger) {
      currentEditingPassenger.querySelector('.passenger-name').textContent = `${fname} ${lname}`;
      currentEditingPassenger.querySelector('.passenger-type').textContent = `Adult / ${gender} / ${country}`;
      currentEditingPassenger = null; // clear reference
    }

    popupOverlay.classList.add('hidden');
    document.body.classList.remove('blurred');
  });
}
document.addEventListener('click', function (e) {
  if (e.target.closest('.edit-meals-btn')) {
    const btn = e.target.closest('.edit-meals-btn');
    document.querySelectorAll('.edit-meals-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    fetch('meal_popup.php')
      .then(res => res.text())
      .then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');
        setupMealPopupEvents();
      });
  }

  if (e.target.closest('.edit-baggage-btn')) {
    const btn = e.target.closest('.edit-baggage-btn');
    document.querySelectorAll('.edit-baggage-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    fetch('bag_popup.php')
      .then(res => res.text())
      .then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');
        setupBagPopupEvents();
      });
  }
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
  