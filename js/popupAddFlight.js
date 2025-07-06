document.addEventListener("DOMContentLoaded", function () {
  const addBtn = document.querySelector('.add-passenger-btn');
  const popupOverlay = document.getElementById('popup-overlay');
  const popupBody = document.getElementById('popup-body');
  const passengerList = document.querySelector('.passenger-list');

  const flightData = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");

  // Overwrite with sessionStorage from selected values
  if (sessionStorage.getItem("selectedAdults")) {
    sessionStorage.setItem("adultCount", sessionStorage.getItem("selectedAdults"));
  }
  if (sessionStorage.getItem("selectedChildren")) {
    sessionStorage.setItem("childCount", sessionStorage.getItem("selectedChildren"));
  }
  
  // Fallbacks
  const adultCount = parseInt(sessionStorage.getItem("adultCount")) || 1;
  const childCount = parseInt(sessionStorage.getItem("childCount")) || 0;
  console.log("🚀 Flight Data from sessionStorage:", flightData);
  console.log("✅ Selected Adults:", sessionStorage.getItem("selectedAdults"));
  console.log("✅ Selected Children:", sessionStorage.getItem("selectedChildren"));
  console.log("👥 Final Adult Count:", adultCount);
  console.log("👶 Final Child Count:", childCount);
    

  const countryMap = {
    au: "Australia", cn: "China", jp: "Japan", kr: "South Korea",
    gb: "United Kingdom", in: "India", my: "Malaysia",
    sg: "Singapore", us: "United States"
  };

  let currentEditingPassenger = null;

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

      fetch('pass_info_popup.php')
        .then(res => res.text())
        .then(html => {
          popupBody.innerHTML = html;
          popupOverlay.classList.remove('hidden');
          document.body.classList.add('blurred');
          document.getElementById('first_name').value = firstName;
          document.getElementById('last_name').value = lastName;
          document.getElementById('gender').value = gender.toLowerCase();
          document.getElementById('country').value = countryCode;
          document.getElementById('dob').value = dob || '';
          setupPopupEvents();
        });
    });
  }

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

  function updateTicketLabel() {
    let adults = 0, children = 0;
    document.querySelectorAll('.passenger-item input[type="checkbox"]:checked').forEach(box => {
      const typeEl = box.closest('.passenger-item')?.querySelector('.passenger-type');
      const type = typeEl?.textContent.trim().split(' / ')[0];
      if (type === 'Adult') adults++;
      if (type === 'Child') children++;
    });

    let label = 'Tickets (';
    if (adults > 0) label += `${adults} Adult${adults > 1 ? 's' : ''}`;
    if (children > 0) {
      label += adults > 0 ? ', ' : '';
      label += `${children} Child${children > 1 ? 'ren' : ''}`;
    }
    label += ')';
    document.getElementById('ticket-count-label').textContent = label;
  }

  function countTotalPassengers() {
    return document.querySelectorAll('.passenger-item input[type="checkbox"]').length;
  }

  function updatePrices() {
    const currentPassengerCount = countTotalPassengers();
    const flightData = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");
    const originalAdults = parseInt(flightData.adults) || 1;
    const originalChildren = parseInt(flightData.children) || 0;
    const originalPassengerCount = originalAdults + originalChildren;
    const totalFlightPrice = parseFloat(flightData.flightPrice || "0");

    const pricePerPassenger = totalFlightPrice / originalPassengerCount;
    const updatedFlightPrice = pricePerPassenger * currentPassengerCount;
    const mealPrice = 30 * currentPassengerCount;
    const baggagePrice = 20 * currentPassengerCount;
    const total = updatedFlightPrice + mealPrice + baggagePrice;

    document.getElementById('flight-price').textContent = `RM ${updatedFlightPrice.toFixed(2)}`;
    document.querySelector('.ticket-price').textContent = `RM ${updatedFlightPrice.toFixed(2)}`;
    document.querySelector('.meal-price').textContent = `RM ${mealPrice.toFixed(2)}`;
    document.querySelector('.baggage-price').textContent = `RM ${baggagePrice.toFixed(2)}`;
    document.querySelector('.total-value').textContent = `RM ${total.toFixed(2)}`;
  }

  function generatePassengerList() {
    passengerList.innerHTML = '';
    document.querySelectorAll('.passenger-addon-item').forEach(el => el.remove());

    const userData = window.loggedUser || null;

    for (let i = 1; i <= adultCount; i++) {
      createPassengerEntry("Adult", i, i === 1 ? userData : null);
    }

    for (let i = 1; i <= childCount; i++) {
      createPassengerEntry("Child", i);
    }

    document.querySelectorAll('.passenger-item input[type="checkbox"]').forEach(box =>
      box.addEventListener('change', updateTicketLabel)
    );
    updateTicketLabel();
    updatePrices();
  }

  generatePassengerList();

  addBtn.addEventListener('click', () => {
    fetch('pass_info_popup.php')
      .then(res => res.text())
      .then(html => {
        popupBody.innerHTML = html;
        popupOverlay.classList.remove('hidden');
        document.body.classList.add('blurred');

        const saveBtn = popupBody.querySelector('.popup-save-btn');
        const closeBtn = popupBody.querySelector('.popup-close');

        closeBtn?.addEventListener('click', () => {
          popupOverlay.classList.add('hidden');
          document.body.classList.remove('blurred');
        });

        saveBtn?.addEventListener('click', e => {
          e.preventDefault();
          const fname = popupBody.querySelector('#first_name').value.trim();
          const lname = popupBody.querySelector('#last_name').value.trim();
          const gender = popupBody.querySelector('#gender').value;
          const countryCode = popupBody.querySelector('#country').value;
          const countryName = countryMap[countryCode] || countryCode;
          const dobInput = popupBody.querySelector('#dob');
          const dob = new Date(dobInput?.value);
          const today = new Date();
          let age = today.getFullYear() - dob.getFullYear();
          if (today.getMonth() < dob.getMonth() ||
              (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
            age--;
          }
          const type = age < 12 ? 'Child' : 'Adult';
          if (!fname || !lname || !countryCode || !dobInput.value) {
            alert("Please fill all fields correctly.");
            return;
          }
          const nameRegex = /^[A-Za-z\s'-]+$/;
          if (!nameRegex.test(fname) || !nameRegex.test(lname)) {
            alert("Names can only contain letters, spaces, apostrophes or hyphens.");
            return;
          }

          createPassengerEntry(type, 0, {
            fst_name: fname,
            lst_name: lname,
            gender: gender,
            country: countryCode,
            dob: dobInput.value
          });

          updateTicketLabel();
          updatePrices();
          popupOverlay.classList.add('hidden');
          document.body.classList.remove('blurred');
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

    saveBtn?.addEventListener('click', e => {
      e.preventDefault();
      const fname = popupBody.querySelector('#first_name').value.trim();
      const lname = popupBody.querySelector('#last_name').value.trim();
      const gender = popupBody.querySelector('#gender').value;
      const countryCode = popupBody.querySelector('#country').value;
      const countryName = countryMap[countryCode] || countryCode;
      const dobInput = popupBody.querySelector('#dob');
      const dob = new Date(dobInput.value);
      const today = new Date();
      const nameRegex = /^[A-Za-z\s'-]+$/;
      if (!nameRegex.test(fname) || !nameRegex.test(lname)) {
        alert("Names can only contain letters, spaces, apostrophes or hyphens.");
        return;
      }
      let age = today.getFullYear() - dob.getFullYear();
      if (today.getMonth() < dob.getMonth() ||
          (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
        age--;
      }
      const passengerType = age < 12 ? 'Child' : 'Adult';

      if (currentEditingPassenger) {
        const oldName = currentEditingPassenger.querySelector('.passenger-name').textContent.trim();
        const updatedName = `${fname} ${lname}`;
        currentEditingPassenger.setAttribute('data-dob', dobInput.value);
        currentEditingPassenger.querySelector('.passenger-name').textContent = updatedName;
        currentEditingPassenger.querySelector('.passenger-type').textContent = `${passengerType} / ${gender} / ${countryName}`;

        document.querySelectorAll('.addons-row .passenger-name').forEach(el => {
          if (el.textContent.trim() === oldName) el.textContent = updatedName;
        });

        currentEditingPassenger = null;
      }

      updateTicketLabel();
      updatePrices();
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

  document.querySelector('.select-seats-btn')?.addEventListener('click', function (e) {
    e.preventDefault();
    const total = document.querySelector('.total-value')?.textContent.replace('RM', '').trim() || '0';
    const ticket = document.getElementById('flight-price')?.textContent.replace('RM', '').trim() || '0';
    const baggage = document.querySelector('.baggage-price')?.textContent.replace('RM', '').trim() || '0';
    const meal = document.querySelector('.meal-price')?.textContent.replace('RM', '').trim() || '0';

    sessionStorage.setItem('total_price', total);
    sessionStorage.setItem('ticket_price', ticket);
    sessionStorage.setItem('baggage_price', baggage);
    sessionStorage.setItem('meal_price', meal);

    window.location.href = "seat_selection.php";
  });
});
