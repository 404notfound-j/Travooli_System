document.addEventListener("DOMContentLoaded", function () {
  const popupOverlay = document.getElementById('popup-overlay');
  const popupBody = document.getElementById('popup-body');
  const passengerListContainer = document.getElementById('passenger-list');
  const departAddonContainer = document.getElementById('depart-addon-container');
  const returnAddonContainer = document.getElementById('return-addon-container');

  const countryMap = {
    au: "Australia", cn: "China", jp: "Japan", kr: "South Korea",
    gb: "United Kingdom", in: "India", my: "Malaysia", sg: "Singapore", us: "United States"
  };
  let currentEditingPassenger = null;

  // --- Retrieve search data from sessionStorage ---
  const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');
  const adults = parseInt(flightSearch.adults || 1, 10);
  const children = parseInt(flightSearch.children || 0, 10);
  const tripType = flightSearch.trip || 'one';
  const departFlightId = flightSearch.depart || flightSearch.selectedFlight;
  const returnFlightId = flightSearch.return;
  const baseFlightPrice = parseFloat(flightSearch.totalPrice || 0);

  const departFlightPricePerPax = parseFloat(flightSearch.departFlightPricePerPax || 0);
  const returnFlightPricePerPax = parseFloat(flightSearch.returnFlightPricePerPax || 0);

  const initialTotalPassengers = adults + children;


  // --- Global function for price updates ---
  window.updatePrices = function () {
    let totalBaggageCost = 0;
    let totalMealCost = 0;
    let selectedFlightTicketCost = 0;

    const allPassengerItems = document.querySelectorAll('#passenger-list .passenger-item');
    let activeAdultCount = 0;
    let activeChildCount = 0;
    let currentActivePassengerCount = 0;

    allPassengerItems.forEach(passengerItem => {
        const checkbox = passengerItem.querySelector('.passenger-select-checkbox');
        if (checkbox && checkbox.checked) {
            currentActivePassengerCount++;
            const passengerIndex = passengerItem.dataset.passengerIndex;

            const passengerTypeEl = passengerItem.querySelector('.passenger-type');
            if (passengerTypeEl && passengerTypeEl.textContent.startsWith('Adult')) {
                activeAdultCount++;
            } else if (passengerTypeEl && passengerTypeEl.textContent.startsWith('Child')) {
                activeChildCount++;
            }

            document.querySelectorAll(`.passenger-addon-item[data-passenger-index="${passengerIndex}"]`).forEach(addonItem => {
                const mealAddonValue = addonItem.querySelector('.addon-details .addon-value[data-addon-type="Meal Add-on"]');
                const baggageAddonValue = addonItem.querySelector('.addon-details .addon-value[data-addon-type="Additional Baggage"]');

                if (mealAddonValue) {
                    totalMealCost += parseFloat(mealAddonValue.getAttribute('data-price')) || 0;
                }
                if (baggageAddonValue) {
                    totalBaggageCost += parseFloat(baggageAddonValue.getAttribute('data-price')) || 0;
                }
            });
        }
    });

    selectedFlightTicketCost = (departFlightPricePerPax * currentActivePassengerCount);
    if (tripType === 'round') {
        selectedFlightTicketCost += (returnFlightPricePerPax * currentActivePassengerCount);
    }
    
    const taxes = selectedFlightTicketCost * 0.06;
    const discount = 0;
    const grandTotal = selectedFlightTicketCost + totalBaggageCost + totalMealCost + taxes - discount;

    // Update display
    document.getElementById('ticket-count-label').textContent = `Tickets (${activeAdultCount} Adult${activeAdultCount > 1 ? 's' : ''}${activeChildCount > 0 ? ', ' : ''}${activeChildCount > 0 ? `${activeChildCount} Child${activeChildCount > 1 ? 'ren' : ''}` : ''})`;
    document.getElementById('flight-price').textContent = `RM ${selectedFlightTicketCost.toFixed(2)}`;
    document.querySelector('.baggage-price').textContent = `RM ${totalBaggageCost.toFixed(2)}`;
    document.querySelector('.meal-price').textContent = `RM ${totalMealCost.toFixed(2)}`;
    document.querySelector('.tax-price').textContent = `RM ${taxes.toFixed(2)}`;
    document.querySelector('.total-value').textContent = `RM ${grandTotal.toFixed(2)}`;

    const mealPriceEl = document.querySelector('.meal-price');
    const baggagePriceEl = document.querySelector('.baggage-price');

    if (mealPriceEl) {
        if (totalMealCost === 0) {
            mealPriceEl.classList.add('free');
        } else {
            mealPriceEl.classList.remove('free');
        }
    }

    if (baggagePriceEl) {
        if (totalBaggageCost === 0) {
            baggagePriceEl.classList.add('free');
        } else {
            baggagePriceEl.classList.remove('free');
        }
    }

    // Update sessionStorage with current prices and active passenger counts
    flightSearch.ticketPrice = selectedFlightTicketCost;
    flightSearch.baggagePrice = totalBaggageCost;
    flightSearch.mealPrice = totalMealCost;
    flightSearch.taxPrice = taxes;
    flightSearch.discount = discount;
    flightSearch.finalTotalPrice = grandTotal;
    flightSearch.activeAdults = activeAdultCount;
    flightSearch.activeChildren = activeChildCount;
    sessionStorage.setItem('flightSearch', JSON.stringify(flightSearch));
  };

  async function fetchAirportFullNames(airportShortCode) {
    try {
      const response = await fetch(`getAirportInfo.php?code=${airportShortCode}`);
      if (!response.ok) throw new Error('Network response was not ok.');
      const data = await response.json();
      return data.city_full || airportShortCode;
    } catch (error) {
      console.error('Error fetching airport info:', error);
      return airportShortCode;
    }
  }

  async function initializeRouteDisplay() {
    if (departFlightId) {
      const originAirportShort = flightSearch.from;
      const destAirportShort = flightSearch.to;

      const originCity = await fetchAirportFullNames(originAirportShort);
      const destCity = await fetchAirportFullNames(destAirportShort);

      document.getElementById('depart-route-display').textContent = `${originCity} - ${destCity}`;
    }

    if (tripType === 'round' && returnFlightId) {
      document.querySelector('.trip-segment[data-segment="return"]').style.display = 'flex';

      const returnOriginAirportShort = flightSearch.to;
      const returnDestAirportShort = flightSearch.from;

      const returnOriginCity = await fetchAirportFullNames(returnOriginAirportShort);
      const returnDestCity = await fetchAirportFullNames(returnDestAirportShort);

      document.getElementById('return-route-display').textContent = `${returnOriginCity} - ${returnDestCity}`;
    }
  }

  function bindEditPassengerBtn(button) {
    button.addEventListener('click', () => {
      const passengerItem = button.closest('.passenger-item');
      currentEditingPassenger = passengerItem;

      const name = passengerItem.querySelector('.passenger-name').textContent.trim();
      const typeText = passengerItem.querySelector('.passenger-type').textContent.trim();
      const parts = typeText.split(" / ");
      const type = parts[0];
      const gender = parts[1];
      const country = parts[2];

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

  function createPassengerEntry(fname, lname, type, gender, countryName, dobValue, targetContainer) {
    const passengerItem = document.createElement('div');
    passengerItem.classList.add('passenger-item');
    passengerItem.setAttribute('data-dob', dobValue);
    
    const passengerIndex = targetContainer.children.length;
    passengerItem.dataset.passengerIndex = passengerIndex;

    passengerItem.innerHTML = `
      <input type="checkbox" class="passenger-select-checkbox" checked>
      <div class="passenger-details">
        <span class="passenger-name">${fname} ${lname}</span>
        <span class="passenger-type">${type} / ${gender} / ${countryName}</span>
      </div>
      <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
    `;
    
    targetContainer.appendChild(passengerItem);

    const editBtn = passengerItem.querySelector('.edit-passenger-btn');
    bindEditPassengerBtn(editBtn);

    createAddonItemsForPassenger(fname, lname, passengerIndex);
  }

  function createAddonItemsForPassenger(fname, lname, passengerIndex) {
      document.querySelectorAll('.trip-segment').forEach(segment => {
          const newAddon = document.createElement('div');
          newAddon.classList.add('passenger-addon-item');
          newAddon.dataset.passengerIndex = passengerIndex;
          newAddon.innerHTML = `
            <div class="addons-row">
              <div class="passenger-name">${fname} ${lname}</div>
              <div class="addon-details">
                <span class="addon-type">Meal Add-on</span>
                <span class="addon-value" data-price="0" data-addon-type="Meal Add-on">No Meal</span>
                <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
              </div>
              <div class="addon-details">
                <span class="addon-type">Additional Baggage</span>
                <span class="addon-value" data-price="0" data-addon-type="Additional Baggage">1 piece, 10kg</span> <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
              </div>
            </div>
          `;
          if (segment.dataset.segment === 'depart') {
            departAddonContainer.appendChild(newAddon);
          } else if (segment.dataset.segment === 'return') {
            returnAddonContainer.appendChild(newAddon);
          }
      });
  }

  function generatePassengerList() {
    // Clear ALL existing passengers and their addons before regenerating
    passengerListContainer.innerHTML = '';
    departAddonContainer.innerHTML = '';
    returnAddonContainer.innerHTML = '';

    let currentAdultsToGenerate = adults;
    let currentChildrenToGenerate = children;

    // Handle the logged-in user first, if applicable
    if (window.loggedUser && window.loggedUser.fst_name) {
        const userFname = window.loggedUser.fst_name;
        const userLname = window.loggedUser.lst_name;
        const userGender = window.loggedUser.gender;
        const userCountry = countryMap[window.loggedUser.country] || window.loggedUser.country;
        createPassengerEntry(userFname, userLname, "Adult", userGender, userCountry, '', passengerListContainer);

        if (currentAdultsToGenerate > 0) {
            currentAdultsToGenerate--;
        }
    }

    // Generate remaining adults based on the updated count
    for (let i = 0; i < currentAdultsToGenerate; i++) {
      createPassengerEntry("Guest", `Adult ${passengerListContainer.children.length + 1}`, "Adult", "Male", "Malaysia", '', passengerListContainer);
    }
    
    // Generate children based on the total children requested
    for (let i = 0; i < currentChildrenToGenerate; i++) {
      createPassengerEntry("Guest", `Child ${passengerListContainer.children.length + 1}`, "Child", "Male", "Malaysia", '', passengerListContainer);
    }
    
    window.updatePrices();
  }

  // --- VALIDATION FUNCTION ---
  function validateAllPassengerInfo() {
    const passengerItems = document.querySelectorAll('#passenger-list .passenger-item');
    let allInfoFilled = true;
    let guestCount = 0;

    passengerItems.forEach((item) => {
      const checkbox = item.querySelector('.passenger-select-checkbox');
      if (checkbox && checkbox.checked) {
        const passengerNameEl = item.querySelector('.passenger-name');
        const passengerName = passengerNameEl ? passengerNameEl.textContent.trim() : '';

        if (passengerName.includes("Guest") || passengerName === "") {
          allInfoFilled = false;
          guestCount++;
        }
      }
    });

    if (!allInfoFilled) {
      alert(`Please fill in the information for all selected passengers. ${guestCount} passenger(s) still require details.`);
      return false;
    }
    return true;
  }
  // --- END VALIDATION FUNCTION ---


  // --- Event delegation for passenger checkbox changes ---
  document.addEventListener('change', function(event) {
    if (event.target.matches('#passenger-list .passenger-item .passenger-select-checkbox')) {
      const checkbox = event.target;
      const passengerItem = checkbox.closest('.passenger-item');
      const passengerIndex = passengerItem.dataset.passengerIndex;

      const allCheckboxes = document.querySelectorAll('#passenger-list .passenger-item .passenger-select-checkbox');
      const checkedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;

      if (!checkbox.checked && checkedCount === 0) {
        event.preventDefault();
        checkbox.checked = true;
        alert('At least one passenger must be selected.');
        return;
      }

      document.querySelectorAll(`.passenger-addon-item[data-passenger-index="${passengerIndex}"]`).forEach(addonItem => {
        if (checkbox.checked) {
          addonItem.style.display = '';
        } else {
          addonItem.style.display = 'none';
        }
      });

      window.updatePrices();
    }
  });


  // --- Initial setup calls ---
  initializeRouteDisplay();
  generatePassengerList();

  // --- Select Seats Button Handler ---
  const selectBtn = document.querySelector('.select-seats-btn');
  if (selectBtn) { 
    selectBtn.addEventListener('click', function (e) {
      e.preventDefault();

      if (validateAllPassengerInfo()) {
        window.updatePrices();
        window.location.href = 'seat_selection.php';
      }
    });
  }

  // --- Add Passenger Button Handler ---
  const addBtn = document.querySelector('.add-passenger-btn');
  if(addBtn) { 
    addBtn.addEventListener('click', () => {
      fetch('pass_info_popup.php')
        .then(res => res.text())
        .then(html => {
          popupBody.innerHTML = html;
          popupOverlay.classList.remove('hidden');
          document.body.classList.add('blurred');
          currentEditingPassenger = null;
          setupPopupEvents();
        });
    });
  }

document.querySelectorAll('#passenger-list-wrapper .edit-passenger-btn').forEach(button => {
    bindEditPassengerBtn(button);
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
    const countryCode = popupBody.querySelector('#country').value;
    const countryName = countryMap[countryCode] || countryCode;
    const dobInput = popupBody.querySelector('#dob');
    if (!fname || !lname) {
        alert("First name and Last name are required.");
        return;
    }

    if (!dobInput || !dobInput.value) {
      alert("Please provide a valid date of birth.");
      return;
    }

    const dob = new Date(dobInput.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
      age--;
    }

    const passengerType = age < 12 ? "Child" : "Adult";

    if (currentEditingPassenger) {
      const updatedFullName = `${fname} ${lname}`;
      const passengerIndexToUpdate = currentEditingPassenger.dataset.passengerIndex;

      currentEditingPassenger.setAttribute('data-dob', dobInput.value);
      currentEditingPassenger.querySelector('.passenger-name').textContent = updatedFullName;
      currentEditingPassenger.querySelector('.passenger-type').textContent = `${passengerType} / ${gender} / ${countryName}`;

      document.querySelectorAll(`.passenger-addon-item[data-passenger-index="${passengerIndexToUpdate}"] .passenger-name`).forEach(el => {
         el.textContent = updatedFullName;
      });
      currentEditingPassenger = null;
    } else {
      createPassengerEntry(fname, lname, passengerType, gender, countryName, dobInput.value, passengerListContainer);
    }

    window.updatePrices();

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
        if (typeof setupMealPopupEvents === 'function') {
            setupMealPopupEvents();
        } else {
            console.warn('setupMealPopupEvents not found. Make sure meal_popup.js is loaded.');
        }
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
        if (typeof setupBagPopupEvents === 'function') {
            setupBagPopupEvents();
        } else {
            console.warn('setupBagPopupEvents not found. Make sure bag_popup.js is loaded.');
        }
      });
  }
});})