//DATE DISPLAY IN SEARCH BAR 
document.addEventListener('DOMContentLoaded', function () {
  function formatDateToText(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date)) return '';
    const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    return `${weekdays[date.getDay()]}, ${months[date.getMonth()]} ${date.getDate()}`;
  }
  
  function updateDateDisplay(depart, ret, tripType) {
    const displayElem = document.getElementById('dateDisplay');
    const formattedDepart = formatDateToText(depart);
    const formattedReturn = formatDateToText(ret);
  
    if (!formattedDepart && !formattedReturn) return;
  
    if (tripType === 'round' && formattedReturn) {
      displayElem.textContent = `${formattedDepart} - ${formattedReturn}`;
    } else {
      displayElem.textContent = formattedDepart;
    }
  }

  
  // ✅ When "Done" is clicked in calendar
  document.querySelector('.btn-done').addEventListener('click', function () {
    const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'one';
    const departVal = document.getElementById('departDate')?.value || '';
    const returnVal = document.getElementById('returnDate')?.value || '';
    updateDateDisplay(departVal, returnVal, tripType);
  
    document.getElementById('datePickerDropdown')?.classList.add('hidden');
  });
  
  // ✅ Passenger display
  function updatePassengerDisplay() {
    if (typeof window.adultCount !== 'number') window.adultCount = 1;
    if (typeof window.childCount !== 'number') window.childCount = 0;
  
    let label = '';
    label += window.adultCount === 1 ? '1 Adult' : `${window.adultCount} Adults`;
    if (window.childCount === 1) label += ', 1 Child';
    else if (window.childCount > 1) label += `, ${window.childCount} Children`;
  
    const displayElem = document.getElementById('passengerInput');
    if (displayElem) displayElem.value = label;
  }
  
  function preloadSearchFromDashboard() {
    const urlParams = new URLSearchParams(window.location.search);
    const fromCode = urlParams.get('from');
    const toCode = urlParams.get('to');
    const fromText = urlParams.get('fromText');
    const toText = urlParams.get('toText');
    const departDate = urlParams.get('departDate');
    const returnDate = urlParams.get('returnDate');
    const adults = urlParams.get('adults') || '1';
    const children = urlParams.get('children') || '0';
    const trip = urlParams.get('trip') || 'one';
    const seatClass = urlParams.get('classId') || 'PE';
    document.getElementById('fromAirport').value = fromText || '';
    document.getElementById('toAirport').value = toText || '';
    document.getElementById('fromAirport').setAttribute('data-code', fromCode || '');
    document.getElementById('toAirport').setAttribute('data-code', toCode || '');
    
    if (!fromCode && !toCode && !departDate) return;
  
    // ✅ Pre-fill date values
    document.getElementById('departDate').value = departDate || '';
    if (trip === 'round') {
      document.getElementById('returnDate').value = returnDate || '';
    }
  
    // ✅ Pre-fill trip type selection
    const oneWayRadio = document.getElementById('oneWay');
    const roundTripRadio = document.getElementById('roundTrip');
    if (trip === 'one' && oneWayRadio) oneWayRadio.checked = true;
    else if (trip === 'round' && roundTripRadio) roundTripRadio.checked = true;
  
    // ✅ Update visible date display
    updateDateDisplay(departDate, returnDate, trip);
  
    // ✅ Update passenger display
    window.adultCount = parseInt(adults);
    window.childCount = parseInt(children);
    updatePassengerDisplay();
  
    // ✅ Build search object
    const searchInfo = {
      from: fromCode,
      to: toCode,
      fromText,
      toText,
      departDate,
      returnDate,
      adults,
      children,
      trip,
      seatClass
    };
  
    // ✅ Fetch flights
    if (trip === 'round') {
      const returnSearch = {
        from: searchInfo.to,
        to: searchInfo.from,
        date: searchInfo.returnDate,
        seatClass: searchInfo.seatClass,
        airlines: searchInfo.airlines || '',
        timeFrom: searchInfo.timeFrom || '00:00',
        timeTo: searchInfo.timeTo || '23:59',
        sortBy: searchInfo.sortBy || ''
      };
  
      Promise.all([
        new Promise(resolve => fetchFlights(searchInfo, resolve)),
        new Promise(resolve => fetchFlights(returnSearch, resolve))
      ]).then(([departData, returnData]) => {
        const pairedData = departData.map((depart, i) => ({
          depart,
          ret: returnData[i % returnData.length] || returnData[0]
        }));
        renderFlights(pairedData, 'flightResults', 'round');
      });
    } else {
      fetchFlights(searchInfo, function(data) {
        renderFlights(data, 'flightResults', 'one');
      });
    }
  }
  
  preloadSearchFromDashboard();
  
  // ✅ Restore display from sessionStorage after reload
  const prevSearch = JSON.parse(sessionStorage.getItem("flightSearch") || '{}');
  if (prevSearch.departDate) {
    updateDateDisplay(prevSearch.departDate, prevSearch.returnDate || '', prevSearch.trip || 'one');
  }
  
  // ✅ Click on visible display triggers calendar
  document.getElementById('dateDisplay').addEventListener('click', () => {
    document.getElementById('departDate').click();
  });
  
  // ✅ Live update when user picks a date
  document.getElementById('departDate').addEventListener('change', (e) => {
    const trip = document.querySelector('input[name="tripType"]:checked')?.value || 'one';
    const departDateVal = e.target.value;
    const returnDateVal = document.getElementById('returnDate').value;
    if (trip === 'round') document.getElementById('returnDate').click();
    updateDateDisplay(departDateVal, returnDateVal, trip);
  });
  
  document.getElementById('returnDate').addEventListener('change', () => {
    const d = document.getElementById('departDate').value;
    const r = document.getElementById('returnDate').value;
    updateDateDisplay(d, r, 'round');
  });
  

  
  // ✅ SEARCH button — updates session and display
  document.getElementById('searchBtn').addEventListener('click', function (e) {
    e.preventDefault();
  
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    const departInput = document.getElementById('departDate');
    const returnInput = document.getElementById('returnDate');
    const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'one';
  
    updateDateDisplay(departInput.value, returnInput?.value || '', tripType);
  
    const departDate = departInput.value;
    const returnDate = returnInput?.value || '';
  
    const fromCode = fromInput.getAttribute('data-code') || extractCode(fromInput.value);
    const toCode = toInput.getAttribute('data-code') || extractCode(toInput.value);
  
    if (!fromCode || !toCode || !departDate) {
      alert("Please complete all required fields (From, To, Depart Date)");
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
      departDate,
      returnDate,
      trip: tripType,
      adults: window.adultCount || 1,
      children: window.childCount || 0,
      seatClass: 'PE'
    };
  
    sessionStorage.setItem('flightSearch', JSON.stringify(searchInfo));
    document.getElementById('flightResults').innerHTML = '';
  
    if (tripType === 'round') {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'depart'));
    } else {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'one'));
    }
  });
  

  // CLASS FILTER BUTTONS (Economy, Business, etc.)
  document.querySelectorAll('.filter-button').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.filter-button').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
    });
  });
   // Save search filters (currently empty function stub)
   function saveSearchInfo(searchInfo) { }
  //  Retrieve previous search from sessionStorage
  function getSearchInfo() {
    return JSON.parse(sessionStorage.getItem("flightSearch") || '{}');
  }

  //FETCH FLIGHTS from backend with filters applied
  function fetchFlights(params, callback) {
    const formData = new FormData();  
    formData.append("origin", params.from);
    formData.append("destination", params.to);
    formData.append("date", params.departDate || params.date); 
    formData.append("seatClass", params.seatClass || "");
    formData.append("airlines", params.airlines || "");
    formData.append("timeFrom", params.timeFrom || "00:00");
    formData.append("timeTo", params.timeTo || "23:59");
    formData.append("sortBy", params.sortBy || "");
  
    console.log("📤 Sending to getflight.php:", Object.fromEntries(formData.entries())); 
  
    fetch("getflight.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => callback(data));
  }
  
  // Render Flight
  function renderFlights(data, containerId, tripType) {
    const container = document.getElementById(containerId);
    if (!container) return;
  
    let html = '';
  
    if (tripType === 'round') {
      for (const pair of data) {
        const { depart, ret } = pair;
  
        html += `
          <div class="flight-card roundtrip-card">
            <div class="airline-section">
              <img src="images/${depart.airline_id}.png" class="airline-logo" alt="${depart.airline_id} Logo">
            </div>
      
            <div class="content-section">
              <div class="top-row">
                <div class="rating-box">
                  <span class="rating-score">4.2</span>
                  <span class="reviews"><strong>Very Good</strong> 54 reviews</span>
                </div>
                <div class="price-box">
                  <span class="from">starting from</span>
                  <span class="price">RM ${(Number(depart.price) + Number(ret.price)).toFixed(2)}</span>
                </div>
              </div>
      
              <!-- Schedule -->
              <div class="schedule">
                <!-- Depart -->
                <div class="flight-detail">
                  <span class="flight-time">
                    <span class="part time">${formatTime(depart.departure_time)} - ${formatTime(depart.arrival_time)}</span>
                    <span class="part stop">non-stop</span> 
                    <span class="part duration">${calculateDuration(depart.departure_time, depart.arrival_time)}</span>
                  </span>
                  <div class="flight-meta">${depart.orig_airport_id} → ${depart.dest_airport_id}</div>
                </div>
      
                <!-- Return -->
                <div class="flight-detail">
                  <span class="flight-time">
                    <span class="part time">${formatTime(ret.departure_time)} - ${formatTime(ret.arrival_time)}</span>
                    <span class="part stop">non-stop</span> 
                    <span class="part duration">${calculateDuration(ret.departure_time, ret.arrival_time)}</span>
                  </span>
                  <div class="flight-meta">${ret.orig_airport_id} → ${ret.dest_airport_id}</div>
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
          </div>
        `;
      }
  
    } else {
      // One-way layout
      for (const flight of data) {
        const duration = calculateDuration(flight.departure_time, flight.arrival_time);
  
        html += `
          <div class="flight-card">
            <div class="airline-section">
              <img src="images/${flight.airline_id}.png" class="airline-logo" alt="${flight.airline_id} Logo">
            </div>
            <div class="content-section">
              <div class="top-row">
                <div class="rating-box">
                  <span class="rating-score">4.2</span>
                  <span class="reviews"><strong>Very Good</strong> 54 reviews</span>
                </div>
                <div class="price-box">
                  <span class="from">starting from</span>
                  <span class="price">RM ${parseFloat(flight.price).toFixed(2)}</span>
                </div>
              </div>
               <div class="schedule">
                <!-- Depart -->
                <div class="flight-detail">
                  <span class="flight-time">
                    <span class="part time">${formatTime(flight.departure_time)} - ${formatTime(flight.arrival_time)}</span>
                    <span class="part stop">non-stop</span> 
                    <span class="part duration">${calculateDuration(flight.departure_time, flight.arrival_time)}</span>
                  </span>
                  <div class="flight-meta">${flight.orig_airport_id} → ${flight.dest_airport_id}</div>
                </div>
              <div class="buttons-row">
                <button class="heart-btn"><i class="fa-solid fa-heart"></i></button>
                <button class="view-details-btn" 
                    data-flight-id="${flight.flight_id}" 
                    data-type="oneway">
                  View Details
                </button>
              </div>
            </div>
          </div>
        `;
      }
    }
  
    container.innerHTML = html;
    attachViewHandlers() 
  }
  function attachViewHandlers() {
    document.querySelectorAll('.view-details-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const type = this.dataset.type;
  
        const searchData = getSearchInfo(); // safely get session data
        const seatClass = searchData.seatClass || 'PE';
  
        // Build query parameters to send to flightDetails.php
        const params = new URLSearchParams();
        params.set('classId', seatClass);
        params.set('departClass', seatClass);
        params.set('returnClass', seatClass);
        params.set('from', searchData.from);
        params.set('to', searchData.to);
        params.set('departDate', searchData.departDate || '');
        params.set('returnDate', searchData.returnDate || '');
        params.set('adults', searchData.adults || '1');
        params.set('children', searchData.children || '0');
  
        if (type === 'depart') {
          const flightId = this.dataset.flightId;
          searchData.depart = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
          console.log("✈️ Selected DEPARTURE flight:", flightId);
  
          const departSection = document.querySelector('.depart-section');
          if (departSection) departSection.remove();
  
          const reversedSearch = {
            from: searchData.to,
            to: searchData.from,
            date: searchData.returnDate,
            seatClass: seatClass,
            airlines: searchData.airlines || '',
            timeFrom: searchData.timeFrom || '00:00',
            timeTo: searchData.timeTo || '23:59',
            sortBy: searchData.sortBy || ''
          };
  
          console.log("🔁 Searching for return flights:", reversedSearch);
          fetchFlights(reversedSearch, data => {
            renderFlights(data, 'flightResults', 'return');
          });
  
        } else if (type === 'return') {
          const flightId = this.dataset.flightId;
          searchData.return = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
          console.log("🔙 Selected RETURN flight:", flightId);
  
          params.set('depart', searchData.depart);
          params.set('return', searchData.return);
          params.set('trip', 'round');
  
          window.location.href = `flightDetails.php?${params.toString()}`;
  
        } else if (type === 'roundtrip') {
          // Roundtrip button has both depart and return ID
          const departId = this.dataset.departId;
          const returnId = this.dataset.returnId;
  
          searchData.depart = departId;
          searchData.return = returnId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
  
          console.log("✈️ Roundtrip selected: Depart =", departId, ", Return =", returnId);
  
          params.set('depart', departId);
          params.set('return', returnId);
          params.set('trip', 'round');
  
          window.location.href = `flightDetails.php?${params.toString()}`;
  
        } else {
          // One-way fallback
          const flightId = this.dataset.flightId;
          searchData.selectedFlight = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
          console.log("🧳 One-way selected flight:", flightId);
  
          params.set('flightId', flightId);
          params.set('trip', 'one');
  
          window.location.href = `flightDetails.php?${params.toString()}`;
        }
      });
    });
  }
  
  
  // CALCULATE DURATION between departure and arrival
  function calculateDuration(start, end) {
    const [sh, sm] = start.split(":"), [eh, em] = end.split(":");
    let mins = (eh * 60 + +em) - (sh * 60 + +sm);
    if (mins < 0) mins += 1440;
    const h = Math.floor(mins / 60), m = mins % 60;
    return `${h}h ${m}m`;
  }

  // Format time to AM/PM (e.g., "14:00" → "2:00 PM")
  function formatTime(time) {
    const [h, m] = time.split(":").map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour = h % 12 || 12;
    return `${hour}:${m.toString().padStart(2, '0')} ${ampm}`;
  }

  // Format slider time and display it
  function showSelectedTime(minutes) {
    const display = document.getElementById('selectedTimeDisplay');
    if (display) display.textContent = 'Selected: ' + formatSliderTime(minutes);
  }

  const timeSlider = document.getElementById('timeRange');
  const startTimeLabel = document.getElementById('startTime');
  if (timeSlider && startTimeLabel) {
    timeSlider.addEventListener('input', function () {
      const minutes = parseInt(this.value, 10);
      showSelectedTime(minutes);
      startTimeLabel.textContent = formatSliderTime(minutes);
    });
    startTimeLabel.textContent = formatSliderTime(timeSlider.value);
  }

 // Format slider time and display it
  function formatSliderTime(minutes) {
    const hour = Math.floor(minutes / 60);
    const min = minutes % 60;
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 === 0 ? 12 : hour % 12;
    return `${displayHour}:${min.toString().padStart(2, '0')}${ampm}`;
  }

  //Extract airport code from input value
  function extractCode(value) {
    const match = value.match(/\(([^)]+)\)$/);
    return match ? match[1] : value.trim();
  }

  // SEARCH BUTTON → Validates & updates sessionStorage with new search
  document.getElementById('searchBtn').addEventListener('click', function (e) {
    e.preventDefault();
  
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    const departDate = document.getElementById('departDate').value;
    const returnDate = document.getElementById('returnDate')?.value || '';
    const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'one';
  
    const fromCode = fromInput.getAttribute('data-code') || extractCode(fromInput.value);
    const toCode = toInput.getAttribute('data-code') || extractCode(toInput.value);
  
    if (!fromCode || !toCode || !departDate) {
      alert("Please complete all required fields (From, To, Depart Date)");
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
      departDate,
      returnDate,
      trip: tripType,
      adults: window.adultCount || 1,
      children: window.childCount || 0,
      seatClass: 'PE'
    };
  
    sessionStorage.setItem('flightSearch', JSON.stringify(searchInfo));
  
    updateDateDisplay(departDate, returnDate, tripType);
    document.getElementById('flightResults').innerHTML = '';
  
    if (tripType === 'round') {
      const returnSearch = {
        from: searchInfo.to,
        to: searchInfo.from,
        date: searchInfo.returnDate,
        seatClass: searchInfo.seatClass || 'PE',
        airlines: searchInfo.airlines || '',
        timeFrom: searchInfo.timeFrom || '00:00',
        timeTo: searchInfo.timeTo || '23:59',
        sortBy: searchInfo.sortBy || ''
      };
  
      Promise.all([
        new Promise(resolve => fetchFlights(searchInfo, resolve)),      // Depart
        new Promise(resolve => fetchFlights(returnSearch, resolve))     // Return
      ]).then(([departData, returnData]) => {
        const pairedData = departData.map((depart, i) => ({
          depart,
          ret: returnData[i % returnData.length] || returnData[0]
        }));
        renderFlights(pairedData, 'flightResults', 'round');
      });
  
    } else {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'one'));
    }
  });
  
  
  // CANCEL FILTERS
  document.getElementById('cancelBtn').addEventListener('click', function () {
    // 1. Remove selected seat class filters
    document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('selected'));
  
    // 2. Uncheck all airline checkboxes
    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(cb => cb.checked = false);
  
    // 3. Reset time slider if exists
    const timeSlider = document.getElementById('timeRange');
    if (timeSlider) {
      timeSlider.value = 0;
      showSelectedTime(0); // Reset display text
    }
  
    // 4. Reset sort dropdown
    const sortSelect = document.getElementById('sortBy');
    if (sortSelect) {
      sortSelect.value = '';
    }
  
    // 5. Clear filters from searchInfo
    const searchInfo = getSearchInfo();
    searchInfo.seatClass = '';
    searchInfo.airlines = '';
    searchInfo.timeFrom = '00:00';
    searchInfo.timeTo = '23:59';
    searchInfo.sortBy = '';
  
    // 6. Save the cleaned filter values
    saveSearchInfo(searchInfo);
  
    // ⚠️ DO NOT call fetchFlights here — keep current results!
  });
  
  
  // APPLY FILTERS BUTTON
  document.querySelector('.apply-btn').addEventListener('click', function () {
    const searchInfo = getSearchInfo();
  
    if (!searchInfo.from || !searchInfo.to || !searchInfo.departDate) {
      alert("Please complete all required fields in the search bar (From, To, Depart Date)");
      return;
    }
  
    if (searchInfo.from === searchInfo.to) {
      alert("Departure and destination airports cannot be the same.");
      return;
    }
  
    // Extract filters
    const seatClassBtn = document.querySelector('.filter-button.selected');
    const seatClassText = seatClassBtn ? seatClassBtn.textContent.trim() : '';
    const airlineNameToCode = { 'AirAsia': 'AK', 'Mas': 'MH', 'FireFly': 'FY' };
    const airlineChecks = Array.from(document.querySelectorAll('.checkbox-group input[type="checkbox"]:checked'));
    const airlines = airlineChecks
      .map(cb => airlineNameToCode[cb.parentElement.textContent.trim()] || cb.parentElement.textContent.trim())
      .join(',');
  
    const timeFrom = '06:00';
    const timeTo = '23:59';
    const sortBy = '';
  
    // ✅ Update filters in searchInfo
    searchInfo.seatClass = seatClassText === 'Economy' ? 'EC' :
                           seatClassText === 'Premium Economy' ? 'PE' :
                           seatClassText === 'Business Class' ? 'BC' :
                           seatClassText === 'First Class' ? 'FC' : 'PE';
    searchInfo.airlines = airlines;
    searchInfo.timeFrom = timeFrom;
    searchInfo.timeTo = timeTo;
    searchInfo.sortBy = sortBy;
  
    sessionStorage.setItem("flightSearch", JSON.stringify(searchInfo));
    updateDateDisplay(searchInfo.departDate, searchInfo.returnDate, searchInfo.trip);
  
    // ✅ Full fetch for round trip
    if (searchInfo.trip === 'round') {
      const returnSearch = {
        from: searchInfo.to,
        to: searchInfo.from,
        date: searchInfo.returnDate,
        seatClass: searchInfo.seatClass,
        airlines: searchInfo.airlines,
        timeFrom: searchInfo.timeFrom,
        timeTo: searchInfo.timeTo,
        sortBy: searchInfo.sortBy
      };
  
      Promise.all([
        new Promise(resolve => fetchFlights(searchInfo, resolve)),      // Depart
        new Promise(resolve => fetchFlights(returnSearch, resolve))     // Return
      ]).then(([departData, returnData]) => {
        const pairedData = departData.map((depart, i) => ({
          depart,
          ret: returnData[i % returnData.length] || returnData[0]
        }));
        renderFlights(pairedData, 'flightResults', 'round');
      });
  
    } else {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'one'));
    }
  });
  

  // HEART LIKE BUTTON INTERACTION
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fa-heart')) {
      e.target.classList.toggle('liked');
    }
    if (e.target.classList.contains('heart-btn')) {
      const icon = e.target.querySelector('.fa-heart');
      if (icon) icon.classList.toggle('liked');
    }
  });

  const style = document.createElement('style');
  style.textContent = `.fa-heart.liked { color: red !important; }`;
  document.head.appendChild(style);
})

