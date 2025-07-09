document.addEventListener('DOMContentLoaded', function () {

  // --- GLOBAL VARIABLES FOR SEARCH BAR FUNCTIONALITY (local to flightBook.js) ---
  let currentDate = new Date(); // Stores the currently displayed month in the calendar
  let selectedDepartDate = null; // Stores the selected departure date
  let selectedReturnDate = null; // Stores the selected return date
  let isSelectingReturn = false; // Flag to indicate if the user is currently selecting a return date
  let adultCount = 1;
  let childCount = 0;


  // --- UTILITY FUNCTIONS ---

  // For formatting dates for the internal date picker inputs (e.g., "Mon, Jul 08")
  function formatDisplayDate(date) {
      if (!date) return '';
      const options = { weekday: 'short', month: 'short', day: 'numeric' };
      return date.toLocaleDateString('en-US', options);
  }

  // For formatting dates to ISO (YYYY-MM-DD) for data-attributes and backend
  function formatDateISO(date) {
      if (!date) return '';
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
  }

  // For formatting month and year for calendar headers (e.g., "July 2025")
  function formatMonthYear(date) {
      if (!date) return '';
      const options = { month: 'long', year: 'numeric' };
      return date.toLocaleDateString('en-US', options);
  }

  // For comparing two date objects ignoring time
  function isSameDate(date1, date2) {
      if (!date1 || !date2) return false;
      // Correct way to compare only the date part
      return date1.toDateString() === date2.toDateString();
  }

  // This updates the text content of the #dateDisplay span on flightBook.php
  function updateMainDateDisplaySpan(departISO, returnISO, tripType) {
      const displayElem = document.getElementById('dateDisplay');
      if (!displayElem) {
          return;
      }
      const formattedDepart = departISO ? new Date(departISO).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) : '';
      const formattedReturn = returnISO ? new Date(returnISO).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) : '';

      if (!formattedDepart && !formattedReturn) {
          displayElem.textContent = 'Depart';
          return;
      }
      displayElem.textContent = tripType === 'round' && formattedReturn ?
          `${formattedDepart} - ${formattedReturn}` : formattedDepart;
  }

  // For passenger input display (main search bar input)
  function updatePassengerDisplayInput() {
      let label = adultCount === 1 ? '1 Adult' : `${adultCount} Adults`;
      if (childCount === 1) label += ', 1 Child';
      else if (childCount > 1) label += `, ${childCount} Children`;
      const displayElem = document.getElementById('passengerInput');
      if (displayElem) displayElem.value = label;
  }

  // Extracts airport code from input value like "City (CODE)".
  function extractCode(value) {
      const match = value.match(/\(([^)]+)\)$/);
      return match ? match[1].trim() : value.trim();
  }


  // --- AIRPORT DROPDOWN FUNCTIONALITY ---

  function initializeAirportDropdowns() {
      const fromInput = document.getElementById('fromAirport');
      const toInput = document.getElementById('toAirport');
      const fromDropdown = document.getElementById('fromDropdown');
      const toDropdown = document.getElementById('toDropdown');

      setupDropdown(fromInput, fromDropdown, 'from');
      setupDropdown(toInput, toDropdown, 'to');

      document.addEventListener('click', function(event) {
          if (!event.target.closest('.search-input') && !event.target.closest('.date-picker-wrapper') && !event.target.closest('#passengerSection')) {
              closeAllDropdowns();
          }
      });
  }

  function setupDropdown(input, dropdown, type) {
      const searchInput = input.closest('.search-input');

      input.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllDropdowns();
          showDropdown(dropdown, searchInput);
      });

      dropdown.addEventListener('click', function(e) {
          const option = e.target.closest('.airport-option');
          if (option) {
              selectAirport(option, input, dropdown, searchInput, type);
          }
      });
  }

  function showDropdown(dropdown, searchInput) {
      dropdown.classList.add('show');
      searchInput.classList.add('active');
  }

  function hideDropdown(dropdown, searchInput) {
      dropdown.classList.remove('show');
      searchInput.classList.remove('active');
  }

  function closeAllDropdowns() {
      const dropdowns = document.querySelectorAll('.airport-dropdown, .date-picker-dropdown, .passenger-dropdown');
      const searchInputs = document.querySelectorAll('.search-input, .date-picker-wrapper');

      dropdowns.forEach(dropdown => dropdown.classList.remove('show'));
      searchInputs.forEach(input => input.classList.remove('active'));
  }

  function selectAirport(option, input, dropdown, searchInput, type) {
      const city = option.dataset.city;
      const code = option.dataset.code;
      const name = option.dataset.name;

      input.value = `${city} (${code})`;
      input.setAttribute('data-code', code);
      input.setAttribute('data-city', city);
      input.setAttribute('data-name', name);

      const allOptions = dropdown.querySelectorAll('.airport-option');
      allOptions.forEach(opt => opt.classList.remove('selected'));
      option.classList.add('selected');

      hideDropdown(dropdown, searchInput);

      updateOtherDropdown(type, code);
  }

  function updateOtherDropdown(selectedType, selectedCode) {
      const otherDropdown = selectedType === 'from' ?
          document.getElementById('toDropdown') :
          document.getElementById('fromDropdown');

      const allOptions = otherDropdown.querySelectorAll('.airport-option');

      allOptions.forEach(option => {
          if (option.dataset.code === selectedCode) {
              option.style.display = 'none';
          } else {
              option.style.display = 'block';
          }
      });
  }


  // --- DATE PICKER FUNCTIONALITY ---

  function initializeDatePicker() {
      const dateInput = document.getElementById('dateInput');
      const datePickerDropdown = document.getElementById('datePickerDropdown');
      const datePickerWrapper = document.querySelector('.date-picker-wrapper');

      const roundTripRadio = document.getElementById('roundTrip');
      const oneWayRadio = document.getElementById('oneWay');

      const departDateInput = document.getElementById('departDate');
      const returnDateInput = document.getElementById('returnDate');
      const returnField = document.getElementById('returnField');

      const prevMonthBtn = document.querySelector('.prev-month');
      const nextMonthBtn = document.querySelector('.next-month');
      const doneBtn = document.querySelector('.btn-done');

      // Ensure initial focus is on departDate unless already set by preload
      if (!selectedDepartDate && !selectedReturnDate) { // Only set if no date is preloaded
          departDateInput.parentElement.classList.add('focused');
          returnDateInput.parentElement.classList.remove('focused');
      }


      generateCalendar();
      updateDateFieldDisplay();
      updateDateDisplayExternal();

      dateInput.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllDropdowns();
          showDatePicker();
      });

      roundTripRadio.addEventListener('change', function() {
          if (this.checked) {
              returnField.style.display = 'flex';
              isSelectingReturn = true;
              if (selectedDepartDate) {
                   departDateInput.parentElement.classList.remove('focused');
                   returnDateInput.parentElement.classList.add('focused');
              } else { // If no depart date is selected yet, focus departs
                  departDateInput.parentElement.classList.add('focused');
                  returnDateInput.parentElement.classList.remove('focused');
              }
              updateDateDisplayExternal();
              generateCalendar();
          }
      });

      oneWayRadio.addEventListener('change', function() {
          if (this.checked) {
              returnField.style.display = 'none';
              selectedReturnDate = null;
              isSelectingReturn = false;
              departDateInput.parentElement.classList.add('focused'); // Always focus depart for one-way
              returnDateInput.parentElement.classList.remove('focused');
              updateDateFieldDisplay();
              updateDateDisplayExternal();
              generateCalendar();
          }
      });

      departDateInput.addEventListener('click', function(e) {
          e.stopPropagation();
          isSelectingReturn = false;
          departDateInput.parentElement.classList.add('focused');
          returnDateInput.parentElement.classList.remove('focused');
          generateCalendar();
      });

      returnDateInput.addEventListener('click', function(e) {
          e.stopPropagation();
          if (roundTripRadio.checked) {
              isSelectingReturn = true;
              returnDateInput.parentElement.classList.add('focused');
              departDateInput.parentElement.classList.remove('focused');
              generateCalendar();
          }
      });

      prevMonthBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          currentDate.setMonth(currentDate.getMonth() - 1);
          generateCalendar();
      });

      nextMonthBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          currentDate.setMonth(currentDate.getMonth() + 1);
          generateCalendar();
      });

      doneBtn.addEventListener('click', function(e) {
          e.stopPropagation();
          if (selectedDepartDate) {
              updateDateDisplayExternal();
              updateMainDateDisplaySpan(
                  formatDateISO(selectedDepartDate),
                  selectedReturnDate ? formatDateISO(selectedReturnDate) : '',
                  document.getElementById('roundTrip').checked ? 'round' : 'one'
              );
              hideDatePicker();
          }
      });

      datePickerDropdown.addEventListener('click', function(e) {
          e.stopPropagation();
      });
  }

  function showDatePicker() {
      const datePickerDropdown = document.getElementById('datePickerDropdown');
      const datePickerWrapper = document.querySelector('.date-picker-wrapper');

      datePickerDropdown.classList.add('show');
      datePickerWrapper.classList.add('active');
      generateCalendar();
  }

  function hideDatePicker() {
      const datePickerDropdown = document.getElementById('datePickerDropdown');
      const datePickerWrapper = document.querySelector('.date-picker-wrapper');

      datePickerDropdown.classList.remove('show');
      datePickerWrapper.classList.remove('active');
  }

  function updateDateDisplayExternal() {
      const dateInput = document.getElementById('dateInput');
      const roundTripRadio = document.getElementById('roundTrip');

      if (selectedDepartDate) {
          const departFormatted = formatDisplayDate(selectedDepartDate);

          if (!roundTripRadio.checked) {
              dateInput.value = `${departFormatted} (One-way)`;
          } else if (selectedReturnDate) {
              const returnFormatted = formatDisplayDate(selectedReturnDate);
              dateInput.value = `${departFormatted} - ${returnFormatted}`;
          } else {
              dateInput.value = `${departFormatted} - Return?`;
          }
      } else {
          dateInput.value = '';
          dateInput.placeholder = 'Depart';
      }
  }

  function generateCalendar() {
      const month1 = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
      const month2 = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);

      document.getElementById('currentMonth1').textContent = formatMonthYear(month1);
      document.getElementById('currentMonth2').textContent = formatMonthYear(month2);

      generateMonthCalendar(month1, 'calendarDates1');
      generateMonthCalendar(month2, 'calendarDates2');
  }

  function generateMonthCalendar(date, containerId) {
      const container = document.getElementById(containerId);
      const year = date.getFullYear();
      const month = date.getMonth();

      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const daysInMonth = lastDay.getDate();
      const startingDayOfWeek = firstDay.getDay();

      container.innerHTML = '';

      let dayCount = 1;
      const today = new Date();
      today.setHours(0,0,0,0); // Normalize today's date to midnight

      for (let week = 0; week < 6; week++) {
          const weekRow = document.createElement('div');
          weekRow.className = 'calendar-row';

          for (let day = 0; day < 7; day++) {
              const dayElement = document.createElement('div');
              dayElement.className = 'calendar-date';

              const dayIndex = week * 7 + day;

              if (dayIndex < startingDayOfWeek) {
                  const prevMonthDate = new Date(year, month, -(startingDayOfWeek - dayIndex - 1));
                  dayElement.textContent = prevMonthDate.getDate();
                  dayElement.classList.add('adjacent-month');
              } else if (dayCount <= daysInMonth) {
                  const currentDay = new Date(year, month, dayCount);
                  currentDay.setHours(0,0,0,0); // Normalize current day in loop to midnight

                  dayElement.textContent = dayCount;
                  dayElement.dataset.date = formatDateISO(currentDay);

                  if (currentDay >= today) {
                      dayElement.classList.add('available');
                      dayElement.addEventListener('click', () => selectDate(currentDay));
                  } else {
                      dayElement.classList.add('past');
                  }

                  if (selectedDepartDate && isSameDate(currentDay, selectedDepartDate)) {
                      dayElement.classList.add('selected');
                  }

                  if (selectedReturnDate && isSameDate(currentDay, selectedReturnDate)) {
                      dayElement.classList.add('selected');
                  }

                  if (selectedDepartDate && selectedReturnDate &&
                      currentDay > selectedDepartDate && currentDay < selectedReturnDate) {
                      dayElement.classList.add('in-range');
                  }

                  dayCount++;
              } else {
                  const nextMonthDate = new Date(year, month + 1, dayCount - daysInMonth);
                  dayElement.textContent = nextMonthDate.getDate();
                  dayElement.classList.add('adjacent-month');
                  dayCount++;
              }

              weekRow.appendChild(dayElement);
          }
          container.appendChild(weekRow);
      }
  }

  function selectDate(date) {
      const roundTripRadio = document.getElementById('roundTrip');
      const departDateInput = document.getElementById('departDate');
      const returnDateInput = document.getElementById('returnDate');

      const clickedDateNormalized = new Date(date.getFullYear(), date.getMonth(), date.getDate());

      // Toggle logic for deselection
      if (selectedDepartDate && isSameDate(clickedDateNormalized, selectedDepartDate)) {
          selectedDepartDate = null;
          selectedReturnDate = null;
          isSelectingReturn = false;
          departDateInput.parentElement.classList.add('focused');
          returnDateInput.parentElement.classList.remove('focused');
      } else if (selectedReturnDate && isSameDate(clickedDateNormalized, selectedReturnDate)) {
          selectedReturnDate = null;
          isSelectingReturn = true;
          returnDateInput.parentElement.classList.add('focused');
          departDateInput.parentElement.classList.remove('focused');
      }
      // Standard selection logic
      else if (!isSelectingReturn || !roundTripRadio.checked) {
          selectedDepartDate = clickedDateNormalized;
          isSelectingReturn = roundTripRadio.checked;

          if (roundTripRadio.checked) {
              departDateInput.parentElement.classList.remove('focused');
              returnDateInput.parentElement.classList.add('focused');

              if (selectedReturnDate && selectedReturnDate <= selectedDepartDate) {
                  selectedReturnDate = null;
              }
          } else {
              selectedReturnDate = null;
          }
      } else {
          // Selecting return date
          if (selectedDepartDate && clickedDateNormalized >= selectedDepartDate) {
              selectedReturnDate = clickedDateNormalized;
              isSelectingReturn = false;

              returnDateInput.parentElement.classList.remove('focused');
              departDateInput.parentElement.classList.add('focused');
          }
      }

      updateDateFieldDisplay();
      generateCalendar();
      updateDateDisplayExternal();
      updateMainDateDisplaySpan(
          selectedDepartDate ? formatDateISO(selectedDepartDate) : '',
          selectedReturnDate ? formatDateISO(selectedReturnDate) : '',
          document.getElementById('roundTrip').checked ? 'round' : 'one'
      );
  }

  function updateDateFieldDisplay() {
      const departDateInput = document.getElementById('departDate');
      const returnDateInput = document.getElementById('returnDate');

      if (selectedDepartDate) {
          departDateInput.value = formatDisplayDate(selectedDepartDate);
          departDateInput.dataset.selected = formatDateISO(selectedDepartDate);
      } else {
          departDateInput.value = '';
          departDateInput.dataset.selected = '';
      }

      if (selectedReturnDate) {
          returnDateInput.value = formatDisplayDate(selectedReturnDate);
          returnDateInput.dataset.selected = formatDateISO(selectedReturnDate);
      } else {
          returnDateInput.value = '';
          returnDateInput.dataset.selected = '';
      }
  }

  function initializePassengerDropdown() {
      const passengerInput = document.getElementById('passengerInput');
      const passengerDropdown = document.getElementById('passengerDropdown');
      const passengerSection = document.getElementById('passengerSection');

      passengerInput.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllDropdowns();
          showPassengerDropdown();
      });

      initializeCounterButtons();
      updatePassengerDisplayInput();
      document.addEventListener('click', function(event) {
          if (!event.target.closest('#passengerSection') && !event.target.closest('.date-picker-wrapper') && !event.target.closest('.search-input')) {
              hidePassengerDropdown();
          }
      });
  }

  function showPassengerDropdown() {
      const passengerDropdown = document.getElementById('passengerDropdown');
      const passengerSection = document.getElementById('passengerSection');

      passengerDropdown.classList.add('show');
      passengerSection.classList.add('active');
  }

  function hidePassengerDropdown() {
      const passengerDropdown = document.getElementById('passengerDropdown');
      const passengerSection = document.getElementById('passengerSection');

      passengerDropdown.classList.remove('show');
      passengerSection.classList.remove('active');
  }

  function initializeCounterButtons() {
      const counterButtons = document.querySelectorAll('.counter-btn');

      counterButtons.forEach(button => {
          button.addEventListener('click', function(e) {
              e.stopPropagation();

              const type = this.dataset.type;
              const isPlus = this.classList.contains('plus');
              const isMinus = this.classList.contains('minus');

              if (type === 'adult') {
                  if (isPlus && adultCount < 9) {
                      adultCount++;
                  } else if (isMinus && adultCount > 1) {
                      adultCount--;
                  }
                  updateCounterDisplay(type, adultCount);
              } else if (type === 'child') {
                  if (isPlus && childCount < 9) {
                      childCount++;
                  } else if (isMinus && childCount > 0) {
                      childCount--;
                  }
                  updateCounterDisplay(type, childCount);
              }

              updatePassengerDisplayInput();
              updateCounterButtonStates();
          });
      });
      updateCounterButtonStates();
  }

  function updateCounterDisplay(type, count) {
      const countElement = document.getElementById(type + 'Count');
      countElement.textContent = count;
  }

  function updateCounterButtonStates() {
      const adultMinusBtn = document.querySelector('.counter-btn.minus[data-type="adult"]');
      const adultPlusBtn = document.querySelector('.counter-btn.plus[data-type="adult"]');
      adultMinusBtn.classList.toggle('disabled', adultCount <= 1);
      adultPlusBtn.classList.toggle('disabled', adultCount >= 9);

      const childMinusBtn = document.querySelector('.counter-btn.minus[data-type="child"]');
      const childPlusBtn = document.querySelector('.counter-btn.plus[data-type="child"]');
      childMinusBtn.classList.toggle('disabled', childCount <= 0);
      childPlusBtn.classList.toggle('disabled', childCount >= 9);
  }

  initializeAirportDropdowns();
  initializeDatePicker();
  initializePassengerDropdown();

  // --- Initial Search Button Click Handler ---
  document.getElementById('searchBtn')?.addEventListener('click', function (e) {
      e.preventDefault();

      const fromInput = document.getElementById('fromAirport');
      const toInput = document.getElementById('toAirport');
      const departInput = document.getElementById('departDate');
      const returnInput = document.getElementById('returnDate');
      const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'one';

      const fromCode = fromInput.getAttribute('data-code');
      const toCode = toInput.getAttribute('data-code');
      const departDateISO = departInput.dataset.selected;
      const returnDateISO = returnInput?.dataset.selected || '';

      if (!fromCode || !toCode || !departDateISO) {
          alert("Please complete all required fields before searching.");
          return;
      }

      if (fromCode === toCode) {
          alert("Departure and destination airports cannot be the same.");
          return;
      }

      const searchInfo = {
          from: fromCode,
          to: toCode,
          fromText: fromInput.value,
          toText: toInput.value,
          departDate: departDateISO,
          returnDate: returnDateISO,
          trip: tripType,
          adults: adultCount,
          children: childCount,
          seatClass: 'EC' // Default seat class for initial search
      };

      // Set default selected seat class button for initial display
      document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('selected'));
      const defaultSeatClassButton = Array.from(document.querySelectorAll('.filter-button')).find(btn => btn.textContent.trim() === 'Economy'); // Set 'Economy' as default
      if (defaultSeatClassButton) defaultSeatClassButton.classList.add('selected');

      performFlightSearch(searchInfo);
  });

  // --- Preload Search from Session ---
  function preloadSearchFromSession() {
      const searchInfo = JSON.parse(sessionStorage.getItem("flightSearch") || '{}');

      if (!searchInfo.from || !searchInfo.to || !searchInfo.departDate) {
          window.location.href = 'U_dashboard.php'; // Redirect if essential data is missing
          return;
      }

      const fromAirportInput = document.getElementById('fromAirport');
      const toAirportInput = document.getElementById('toAirport');

      if (fromAirportInput) {
          fromAirportInput.value = searchInfo.fromText || '';
          fromAirportInput.setAttribute('data-code', searchInfo.from);
          // Assuming fromText is like "City (CODE)", extracting city name for data-city/name
          fromAirportInput.setAttribute('data-city', searchInfo.fromText ? searchInfo.fromText.split(' (')[0] : '');
          fromAirportInput.setAttribute('data-name', searchInfo.fromText ? searchInfo.fromText.split(' (')[0] : '');
      }
      if (toAirportInput) {
          toAirportInput.value = searchInfo.toText || '';
          toAirportInput.setAttribute('data-code', searchInfo.to);
          toAirportInput.setAttribute('data-city', searchInfo.toText ? searchInfo.toText.split(' (')[0] : '');
          toAirportInput.setAttribute('data-name', searchInfo.toText ? searchInfo.toText.split(' (')[0] : '');
      }

      if (searchInfo.departDate) {
          selectedDepartDate = new Date(searchInfo.departDate);
          selectedDepartDate.setHours(0,0,0,0);
          currentDate = new Date(selectedDepartDate.getFullYear(), selectedDepartDate.getMonth(), 1);
      } else {
          selectedDepartDate = null;
          currentDate = new Date();
          currentDate.setDate(1);
      }

      if (searchInfo.returnDate && searchInfo.trip === 'round') {
          selectedReturnDate = new Date(searchInfo.returnDate);
          selectedReturnDate.setHours(0,0,0,0);
      } else {
          selectedReturnDate = null;
      }

      const oneWayRadio = document.getElementById('oneWay');
      const roundTripRadio = document.getElementById('roundTrip');
      const returnField = document.getElementById('returnField');

      if (searchInfo.trip === 'round') {
          if (roundTripRadio) roundTripRadio.checked = true;
          if (returnField) returnField.style.display = 'flex';
          isSelectingReturn = true;
          if (selectedDepartDate) {
              document.getElementById('departDate').parentElement.classList.remove('focused');
              document.getElementById('returnDate').parentElement.classList.add('focused');
          }
      } else {
          if (oneWayRadio) oneWayRadio.checked = true;
          if (returnField) returnField.style.display = 'none';
          selectedReturnDate = null;
          isSelectingReturn = false;
          document.getElementById('departDate').parentElement.classList.add('focused');
          document.getElementById('returnDate').parentElement.classList.remove('focused');
      }

      updateDateFieldDisplay();
      updateDateDisplayExternal();
      generateCalendar();
      updateMainDateDisplaySpan(searchInfo.departDate, searchInfo.returnDate || '', searchInfo.trip || 'one');

      adultCount = parseInt(searchInfo.adults || 1, 10);
      childCount = parseInt(searchInfo.children || 0, 10);

      updatePassengerDisplayInput();
      updateCounterDisplay('adult', adultCount);
      updateCounterDisplay('child', childCount);
      updateCounterButtonStates();

      // Set selected seat class button based on preloaded data
      document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('selected'));
      let preloadedSeatClassText = 'Economy'; // Default to Economy if not in session
      if (searchInfo.seatClass === 'PE') preloadedSeatClassText = 'Premium Economy';
      else if (searchInfo.seatClass === 'BC') preloadedSeatClassText = 'Business Class';
      else if (searchInfo.seatClass === 'FC') preloadedSeatClassText = 'First Class';

      const preloadedSeatClassButton = Array.from(document.querySelectorAll('.filter-button')).find(btn => btn.textContent.trim() === preloadedSeatClassText);
      if (preloadedSeatClassButton) preloadedSeatClassButton.classList.add('selected');

      performFlightSearch(searchInfo);
  }

  // --- Centralized Flight Search Function ---
  function performFlightSearch(searchInfo) {
      sessionStorage.setItem("flightSearch", JSON.stringify(searchInfo)); // Store updated searchInfo

      document.getElementById('flightResults').innerHTML = ''; // Clear previous results

      const searchParams = {
          from: searchInfo.from,
          to: searchInfo.to,
          date: searchInfo.departDate,
          seatClass: searchInfo.seatClass || 'EC', // Ensure a default
          airlines: searchInfo.airlines || '',
          timeFrom: searchInfo.timeFrom || '06:00', // Use '06:00' as per filter, not '00:00'
          timeTo: searchInfo.timeTo || '23:59',
          sortBy: searchInfo.sortBy || ''
      };

      if (searchInfo.trip === 'round') {
          const returnSearch = {
              from: searchInfo.to,
              to: searchInfo.from,
              date: searchInfo.returnDate,
              seatClass: searchInfo.seatClass || 'EC', // Ensure a default
              airlines: searchInfo.airlines || '',
              timeFrom: searchInfo.timeFrom || '06:00', // Use '06:00' as per filter, not '00:00'
              timeTo: searchInfo.timeTo || '23:59',
              sortBy: searchInfo.sortBy || ''
          };

          Promise.all([
              new Promise(resolve => fetchFlights(searchParams, resolve)),
              new Promise(resolve => fetchFlights(returnSearch, resolve))
          ]).then(([departData, returnData]) => {
              const pairedData = departData.map((depart, i) => ({
                  depart,
                  ret: returnData[i % returnData.length] || returnData[0]
              }));
              renderFlights(pairedData, 'flightResults', 'round');
          }).catch(error => {
              console.error("Error fetching round trip flights:", error);
              document.getElementById('flightResults').innerHTML = '<p>Error loading round trip flights. Please try again.</p>';
          });

      } else {
          fetchFlights(searchParams, data => renderFlights(data, 'flightResults', 'one'))
              .catch(error => {
                  console.error("Error fetching one-way flights:", error);
                  document.getElementById('flightResults').innerHTML = '<p>No one-way flights found for your search criteria or error loading. Please try again.</p>';
              });
      }
  }

  preloadSearchFromSession(); // Initial load of search data and flights

  function fetchFlights(params, callback) {
      const formData = new FormData();
      formData.append("origin", params.from);
      formData.append("destination", params.to);
      formData.append("date", params.date || params.departDate || '');
      formData.append("seatClass", params.seatClass || '');
      formData.append("airlines", params.airlines || '');
      formData.append("timeFrom", params.timeFrom || '06:00');
      formData.append("timeTo", params.timeTo || '23:59');
      formData.append("sortBy", params.sortBy || '');

      return fetch("getflight.php", {
          method: "POST",
          body: formData
      })
      .then(res => {
          if (!res.ok) {
              return res.text().then(text => { throw new Error(res.status + ": " + text); });
          }
          return res.json();
      })
      .then(data => {
          callback(data);
          return data;
      })
      .catch(err => {
          alert("Something went wrong while loading flights. Check console for details.");
          throw err;
      });
  }

  function formatTime(time) {
      const parts = time.split(":");
      const h = parseInt(parts[0], 10);
      const m = parseInt(parts[1], 10);
      const ampm = h >= 12 ? 'PM' : 'AM';
      const hour = h % 12 || 12;
      return `${hour}:${m.toString().padStart(2, '0')} ${ampm}`;
  }

  function calculateDuration(start, end) {
      const [sh, sm] = start.split(":"), [eh, em] = end.split(":");
      let mins = (eh * 60 + +em) - (sh * 60 + +sm);
      if (mins < 0) mins += 1440;
      const h = Math.floor(mins / 60), m = mins % 60;
      return `${h}h ${m}m`;
  }

  function renderFlights(data, containerId, tripType) {
      const container = document.getElementById(containerId);
      if (!container) return;
      container.innerHTML = '';

      let html = '';

      if (tripType === 'round') {
          if (!data || data.length === 0 || !data[0].depart) {
              html = '<p>No round trip flights found for your search criteria.</p>';
          } else {
              for (const pair of data) {
                  const { depart, ret } = pair;
                  if (!depart || !ret) continue;

                  let reviewTextDepart = '';
                  let ratingScoreDepart = parseFloat(depart.avg_rating);
                  let reviewCountDepart = parseInt(depart.review_count, 10);

                  if (reviewCountDepart > 0 && !isNaN(ratingScoreDepart)) {
                      let ratingWordDepart = 'Avg';
                      if (ratingScoreDepart >= 4) ratingWordDepart = 'Excellent';
                      else if (ratingScoreDepart >= 3) ratingWordDepart = 'Good';
                      reviewTextDepart = `<strong>${ratingWordDepart}</strong> ${reviewCountDepart} reviews`;
                  } else {
                      reviewTextDepart = `${reviewCountDepart} reviews`;
                  }

                  html += `
                      <div class="flight-card roundtrip-card">
                          <div class="airline-section">
                              <img src="images/${depart.airline_id}.png" class="airline-logo" alt="${depart.airline_id} Logo">
                          </div>
                          <div class="content-section">
                              <div class="top-row">
                                  <div class="rating-box">
                                      <span class="rating-score">${isNaN(ratingScoreDepart) ? 'N/A' : ratingScoreDepart.toFixed(1)}</span>
                                      <span class="reviews">${reviewTextDepart}</span>
                                  </div>
                                  <div class="price-box">
                                      <span class="from">starting from</span>
                                      <span class="price">RM ${(Number(depart.price) + Number(ret.price)).toFixed(2)}</span>
                                  </div>
                              </div>
                              <div class="schedule">
                                  <div class="flight-detail">
                                      <span class="flight-time">
                                          ${formatTime(depart.departure_time)} - ${formatTime(depart.arrival_time)}
                                      </span>
                                      <span class="flight-meta">${depart.orig_airport_id} → ${depart.dest_airport_id}</span>
                                      <span class="flight-meta-duration">${calculateDuration(depart.departure_time, depart.arrival_time)}</span>
                                  </div>
                                  <div class="flight-detail">
                                      <span class="flight-time">
                                          ${formatTime(ret.departure_time)} - ${formatTime(ret.arrival_time)}
                                      </span>
                                      <span class="flight-meta">${ret.orig_airport_id} → ${ret.dest_airport_id}</span>
                                      <span class="flight-meta-duration">${calculateDuration(ret.departure_time, ret.arrival_time)}</span>
                                  </div>
                              </div>
                              <div class="buttons-row">
                                  <button class="heart-btn"><i class="fa-solid fa-heart"></i></button>
                                  <button class="view-details-btn"
                                      data-depart-id="${depart.flight_id}"
                                      data-return-id="${ret.flight_id}"
                                      data-type="roundtrip">
                                      View Details
                                  </button>
                              </div>
                          </div>
                      </div>`;
              }
          }
      } else {
          if (!data || data.length === 0) {
              html = '<p>No one-way flights found for your search criteria.</p>';
          } else {
              for (const flight of data) {
                  let reviewTextFlight = '';
                  let ratingScoreFlight = parseFloat(flight.avg_rating);
                  let reviewCountFlight = parseInt(flight.review_count, 10);

                  if (reviewCountFlight > 0 && !isNaN(ratingScoreFlight)) {
                      let ratingWordFlight = 'Avg';
                      if (ratingScoreFlight >= 4) ratingWordFlight = 'Excellent';
                      else if (ratingScoreFlight >= 3) ratingWordFlight = 'Good';
                      reviewTextFlight = `<strong>${ratingWordFlight}</strong> ${reviewCountFlight} reviews`;
                  } else {
                      reviewTextFlight = `${reviewCountFlight} reviews`;
                  }

                  html += `
                      <div class="flight-card">
                          <div class="airline-section">
                              <img src="images/${flight.airline_id}.png" class="airline-logo" alt="${flight.airline_id} Logo">
                          </div>
                          <div class="content-section">
                              <div class="top-row">
                                  <div class="rating-box">
                                      <span class="rating-score">${isNaN(ratingScoreFlight) ? 'N/A' : ratingScoreFlight.toFixed(1)}</span>
                                      <span class="reviews">${reviewTextFlight}</span>
                                  </div>
                                  <div class="price-box">
                                      <span class="from">starting from</span>
                                      <span class="price">RM ${parseFloat(flight.price).toFixed(2)}</span>
                                  </div>
                              </div>
                              <div class="schedule">
                                  <div class="flight-detail">
                                      <span class="flight-time">
                                          ${formatTime(flight.departure_time)} - ${formatTime(flight.arrival_time)}
                                      </span>
                                      <span class="flight-meta">${flight.orig_airport_id} → ${flight.dest_airport_id}</span>
                                      <span class="flight-meta-duration">${calculateDuration(flight.departure_time, flight.arrival_time)}</span>
                                  </div>
                              </div>
                              <div class="buttons-row">
                                  <button class="heart-btn"><i class="fa-solid fa-heart"></i></button>
                                  <button class="view-details-btn"
                                      data-flight-id="${flight.flight_id}"
                                      data-type="one">
                                      View Details
                                  </button>
                              </div>
                          </div>
                      </div>`;
              }
          }
      }

      container.innerHTML = html;
      attachViewHandlers();
  }

  // --- Apply Filters Button Handler ---
  document.querySelector('.apply-btn').addEventListener('click', function () {
    const searchInfo = JSON.parse(sessionStorage.getItem("flightSearch") || '{}');

    if (!searchInfo.from || !searchInfo.to || !searchInfo.departDate) {
        alert("Please complete all required fields in the search bar (From, To, Depart Date)");
        return;
    }

    if (searchInfo.from === searchInfo.to) {
        alert("Departure and destination airports cannot be the same.");
        return;
    }

    const seatClassBtn = document.querySelector('.filter-button.selected');
    const seatClassText = seatClassBtn ? seatClassBtn.textContent.trim() : '';
    const airlineChecks = Array.from(document.querySelectorAll('.checkbox-group input[type=\"checkbox\"]:checked'));
    const airlines = airlineChecks.map(cb => cb.value).join(',');

    const timeFrom = '06:00';
    const timeTo = '23:59';

    // Corrected seatClass assignment based on selected button
    let determinedSeatClass = 'EC'; // Default to Economy Class if nothing selected
    if (seatClassText === 'Economy') {
        determinedSeatClass = 'EC';
    } else if (seatClassText === 'Premium Economy') {
        determinedSeatClass = 'PE';
    } else if (seatClassText === 'Business Class') {
        determinedSeatClass = 'BC';
    } else if (seatClassText === 'First Class') {
        determinedSeatClass = 'FC';
    }
    searchInfo.seatClass = determinedSeatClass; // Assign the determined seat class

    searchInfo.airlines = airlines;
    searchInfo.timeFrom = timeFrom;
    searchInfo.timeTo = timeTo;

    performFlightSearch(searchInfo); // Centralized function call
  });

  // --- FIX: Seat Class Filter Button Click Handler ---
  document.querySelectorAll('.filter-group h4').forEach(header => {
      if (header.textContent.trim() === 'Seat class') {
          const buttons = header.nextElementSibling.querySelectorAll('.filter-button');
          buttons.forEach(button => {
              button.addEventListener('click', function(e) { // <-- ADD 'e' parameter here
                  e.stopPropagation(); // <-- ADD this line to stop event bubbling
                  e.preventDefault();  // <-- ADD this line to prevent default button action

                  buttons.forEach(btn => btn.classList.remove('selected')); // Remove 'selected' from all
                  this.classList.add('selected'); // Add 'selected' to the clicked button
                  console.log("Seat class button clicked:", this.textContent); // Debugging log
              });
          });
      }
  });

  // --- Heart Button Click Handler ---
  document.addEventListener('click', function (e) {
      if (e.target.classList.contains('fa-heart')) {
          e.target.classList.toggle('liked');
      }
      if (e.target.classList.contains('heart-btn')) {
          const icon = e.target.querySelector('.fa-heart');
          if (icon) icon.classList.toggle('liked');
      }
  });

  // --- Attach View Details Handlers ---
  function attachViewHandlers() {
      document.querySelectorAll('.view-details-btn').forEach(btn => {
          btn.addEventListener('click', function () {
              const type = this.dataset.type;
              // Retrieve the current search info from session storage
              const searchData = JSON.parse(sessionStorage.getItem("flightSearch") || '{}');

              // Update the searchData object with flight-specific details
              if (type === 'roundtrip') {
                  const departId = this.dataset.departId;
                  const returnId = this.dataset.returnId;
                  searchData.depart = departId; // Add depart flight ID
                  searchData.return = returnId; // Add return flight ID
                  searchData.trip = 'round'; // Set trip type
              } else if (type === 'one') {
                  const flightId = this.dataset.flightId;
                  searchData.selectedFlight = flightId; // Add selected one-way flight ID
                  searchData.trip = 'one'; // Set trip type
              }

              // Store the updated searchInfo back into session storage
              sessionStorage.setItem("flightSearch", JSON.stringify(searchData));

              // Redirect to flightDetails.php WITHOUT URL parameters
              window.location.href = `flightDetails.php`;
          });
      });
  }

  // --- Cancel Filters Button Handler ---
  document.getElementById('cancelBtn')?.addEventListener('click', function() {
      // Revert to initial search state or default for filters
      const searchInfo = JSON.parse(sessionStorage.getItem("flightSearch") || '{}');

      // Reset filter visual states
      document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('selected'));
      // Consistent default: Select 'Economy' visually and set 'EC' in searchInfo
      const defaultSeatClassButton = Array.from(document.querySelectorAll('.filter-button')).find(btn => btn.textContent.trim() === 'Economy');
      if (defaultSeatClassButton) defaultSeatClassButton.classList.add('selected');
      searchInfo.seatClass = 'EC'; // Explicitly set to 'EC' for consistency

      document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(cb => cb.checked = false);
      searchInfo.airlines = ''; // Clear airlines filter
      searchInfo.timeFrom = '06:00'; // Reset time filters
      searchInfo.timeTo = '23:59'; // Reset time filters

      // You might want to reset the time range slider here too if it's implemented.

      // Re-run search with reset filters
      performFlightSearch(searchInfo);
  });

});