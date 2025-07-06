document.addEventListener("DOMContentLoaded", function () {
  const popupOverlay = document.getElementById('popup-overlay');
  const popupBody = document.getElementById('popup-body');
  const passengerList = document.querySelector('.passenger-list');
  const addBtn = document.querySelector('.add-passenger-btn');

  // Make updatePrices globally available
  window.updatePrices = function () {
    const currentPassengerCount = document.querySelectorAll('.passenger-item input[type="checkbox"]:checked').length;
    const flightData = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");
    const originalAdults = parseInt(flightData.adults) || 1;
    const originalChildren = parseInt(flightData.children) || 0;
    const originalPassengerCount = originalAdults + originalChildren;
    const totalFlightPrice = parseFloat(flightData.flightPrice || "0");

    const pricePerPassenger = originalPassengerCount > 0 ? totalFlightPrice / originalPassengerCount : 0;
    const updatedFlightPrice = pricePerPassenger * currentPassengerCount;

    let totalMealCost = 0;
    let totalBaggageCost = 0;

    document.querySelectorAll('.passenger-addon-item').forEach(item => {
      const addonValues = item.querySelectorAll('.addon-details .addon-value');
      const mealCost = parseFloat(addonValues[0]?.dataset.price) || 0;
      const baggageCost = parseFloat(addonValues[1]?.dataset.price) || 0;
      totalMealCost += mealCost;
      totalBaggageCost += baggageCost;
    });

    const total = updatedFlightPrice + totalMealCost + totalBaggageCost;
    document.getElementById('flight-price').textContent = `RM ${updatedFlightPrice.toFixed(2)}`;
    document.querySelector('.meal-price').textContent = `RM ${totalMealCost.toFixed(2)}`;
    document.querySelector('.baggage-price').textContent = `RM ${totalBaggageCost.toFixed(2)}`;
    document.querySelector('.total-value').textContent = `RM ${total.toFixed(2)}`;
  };

  // Helper functions
  window.incrementQuantity = function(inputId) {
    const input = document.getElementById(inputId);
    if (input) input.value = (parseInt(input.value) || 0) + 1;
  };

  window.decrementQuantity = function(inputId) {
    const input = document.getElementById(inputId);
    if (input && parseInt(input.value) > 0) input.value = parseInt(input.value) - 1;
  };

  const countryMap = { au: "Australia", cn: "China", jp: "Japan", kr: "South Korea", gb: "United Kingdom", in: "India", my: "Malaysia", sg: "Singapore", us: "United States" };
  const adultCount = parseInt(sessionStorage.getItem("selectedAdults") || "1");
  const childCount = parseInt(sessionStorage.getItem("selectedChildren") || "0");
  let currentEditingPassenger = null;

  function createPassengerEntry(type, index, userData = null) {
    const fname = userData?.fst_name || `Guest`;
    const lname = userData?.lst_name || ``;
    const gender = userData?.gender || "Male";
    const countryCode = userData?.country?.toLowerCase() || "my";
    const countryName = countryMap[countryCode] || "Malaysia";

    const passengerItem = document.createElement('div');
    passengerItem.classList.add('passenger-item');
    passengerItem.setAttribute('data-dob', userData?.dob || '');
    passengerItem.innerHTML = `
      <input type="checkbox" checked>
      <div class="passenger-details">
        <span class="passenger-name">${fname} ${lname}</span>
        <span class="passenger-type">${type} / ${gender} / ${countryName}</span>
      </div>
      <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
    `;
    passengerList.appendChild(passengerItem);
    bindEditPassengerBtn(passengerItem.querySelector('.edit-passenger-btn'));

    document.querySelectorAll('.trip-segment').forEach(segment => {
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
  }

  function bindEditPassengerBtn(button) {
    button.addEventListener('click', () => {
      const passengerItem = button.closest('.passenger-item');
      currentEditingPassenger = passengerItem;
      const name = passengerItem.querySelector('.passenger-name').textContent.trim();
      const typeText = passengerItem.querySelector('.passenger-type').textContent.trim();
      const [type, gender, country] = typeText.split(" / ");
      const [firstName, ...lastNameParts] = name.split(" ");
      const lastName = lastNameParts.join(" ");
      const codeEntry = Object.entries(countryMap).find(([code, name]) => name === country);
      const countryCode = codeEntry ? codeEntry[0] : '';
      const dob = passengerItem.getAttribute('data-dob');

      fetch('pass_info_popup.php').then(res => res.text()).then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');
        document.getElementById('first_name').value = firstName;
        document.getElementById('last_name').value = lastName;
        document.getElementById('gender').value = gender.toLowerCase();
        document.getElementById('country').value = countryCode;
        document.getElementById('dob').value = dob || '';
        setupPassengerPopupEvents();
      });
    });
  }

  function setupPassengerPopupEvents() {
    popupBody.querySelector('.popup-close')?.addEventListener('click', closePopup);
    popupBody.querySelector('.popup-save-btn')?.addEventListener('click', e => {
      e.preventDefault();
      const fname = popupBody.querySelector('#first_name').value.trim();
      const lname = popupBody.querySelector('#last_name').value.trim();
      const gender = popupBody.querySelector('#gender').value;
      const countryCode = popupBody.querySelector('#country').value;
      const countryName = countryMap[countryCode] || countryCode;
      const dob = new Date(popupBody.querySelector('#dob').value);
      const today = new Date();
      const nameRegex = /^[A-Za-z\s'-]+$/;

      if (!nameRegex.test(fname) || !nameRegex.test(lname)) return alert("Invalid name format.");
      let age = today.getFullYear() - dob.getFullYear();
      if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) age--;
      const passengerType = age < 12 ? 'Child' : 'Adult';
      if (!dob || isNaN(dob)) {
        return alert("Please enter a valid date of birth.");
      }
      if (dob > today) {
        return alert("Date of birth cannot be in the future.");
      }
      if (!countryMap[countryCode]) 
        return alert("Please select a valid country.");

      
      if (currentEditingPassenger) {
        const oldName = currentEditingPassenger.querySelector('.passenger-name').textContent.trim();
        const updatedName = `${fname} ${lname}`;
        currentEditingPassenger.setAttribute('data-dob', dob.toISOString().split('T')[0]);
        currentEditingPassenger.querySelector('.passenger-name').textContent = updatedName;
        currentEditingPassenger.querySelector('.passenger-type').textContent = `${passengerType} / ${gender} / ${countryName}`;
        document.querySelectorAll('.addons-row .passenger-name').forEach(el => {
          if (el.textContent.trim() === oldName) el.textContent = updatedName;
        });
        currentEditingPassenger = null;
      }
      updateTicketLabel();
      updatePrices();
      closePopup();
    });
  }

  function closePopup() {
    popupOverlay.classList.add('hidden');
    popupOverlay.style.display = 'none';        
    popupBody.innerHTML = '';                   
    popupBody.style.display = 'none';            
    document.body.classList.remove('blurred');
    currentEditingPassenger = null;
    document.querySelectorAll('.edit-meals-btn, .edit-baggage-btn, .edit-passenger-btn').forEach(btn => btn.classList.remove('active'));
  }
  

  function generatePassengerList() {
    passengerList.innerHTML = '';
    document.querySelectorAll('.passenger-addon-item').forEach(el => el.remove());
    const userData = window.loggedUser || null;
    for (let i = 1; i <= adultCount; i++) createPassengerEntry("Adult", i, i === 1 ? userData : null);
    for (let i = 1; i <= childCount; i++) createPassengerEntry("Child", i);
    document.querySelectorAll('.passenger-item input[type="checkbox"]').forEach(box => box.addEventListener('change', updateTicketLabel));
    updateTicketLabel();
    updatePrices();
  }

  function updateTicketLabel() {
    let adults = 0, children = 0;
    document.querySelectorAll('.passenger-item input[type="checkbox"]:checked').forEach(box => {
      const type = box.closest('.passenger-item')?.querySelector('.passenger-type')?.textContent.split(' / ')[0];
      if (type === 'Adult') adults++;
      if (type === 'Child') children++;
    });
    let label = 'Tickets (';
    if (adults > 0) label += `${adults} Adult${adults > 1 ? 's' : ''}`;
    if (children > 0) label += (adults > 0 ? ', ' : '') + `${children} Child${children > 1 ? 'ren' : ''}`;
    label += ')';
    document.getElementById('ticket-count-label').textContent = label;
  }

  generatePassengerList();

  document.querySelector('.select-seats-btn')?.addEventListener('click', function (e) {
    e.preventDefault();
  
    // Parse prices as floats, fallback to 0
    const ticket = parseFloat(document.getElementById('flight-price')?.textContent.replace('RM', '').trim()) || 0;
    const baggage = parseFloat(document.querySelector('.baggage-price')?.textContent.replace('RM', '').trim()) || 0;
    const meal = parseFloat(document.querySelector('.meal-price')?.textContent.replace('RM', '').trim()) || 0;
  
    const subtotal = ticket + baggage + meal;
  
    const TAX_RATE = 0.06; // 6% tax
    const taxAmount = parseFloat((subtotal * TAX_RATE).toFixed(2));
    const total = parseFloat((subtotal + taxAmount).toFixed(2));
  
    // Store values in sessionStorage as strings with 2 decimal places
    sessionStorage.setItem('ticket_price', ticket.toFixed(2));
    sessionStorage.setItem('baggage_price', baggage.toFixed(2));
    sessionStorage.setItem('meal_price', meal.toFixed(2));
    sessionStorage.setItem('tax_amount', taxAmount.toFixed(2));
    sessionStorage.setItem('total_price', total.toFixed(2));
  
    // Redirect to seat selection page
    window.location.href = "seat_selection.php";
  });
});