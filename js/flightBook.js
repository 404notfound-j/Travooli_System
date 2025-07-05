//DATE DISPLAY IN SEARCH BAR 
document.addEventListener('DOMContentLoaded', function () {

  // Format a date string into display text like "Mon, Jul 21"
  function formatDateToText(dateStr) {
    let date;
    if (/^\d{2}-\d{2}-\d{4}$/.test(dateStr)) {
      const [day, month, year] = dateStr.split("-");
      date = new Date(`${year}-${month}-${day}`);
    } else {
      date = new Date(dateStr); // ISO or fallback
    }
  
    if (isNaN(date)) return '';
    const weekday = date.toLocaleDateString('en-US', { weekday: 'short' });
    const month = date.toLocaleDateString('en-US', { month: 'short' });
    const day = date.getDate();
    return `${weekday}, ${month} ${day}`;
  }

  //  Update the date display in the search bar
  function updateDateDisplay(depart, ret, tripType) {
    const departText = formatDateToText(depart);
    const returnText = tripType === 'round' && ret ? formatDateToText(ret) : '';
    const display = returnText ? `${departText} - ${returnText}` : departText;
    const displayElem = document.getElementById('dateDisplay');
    if (displayElem) displayElem.textContent = display || 'Select dates';
  }

  // Update passenger count display 
  function updatePassengerDisplay() {
    const adults = window.adultCount || 1;
    const children = window.childCount || 0;
  
    let label = '';
    if (adults === 1) {
      label += '1 Adult';
    } else {
      label += `${adults} Adults`;
    }
  
    if (children === 1) {
      label += ', 1 Child';
    } else if (children > 1) {
      label += `, ${children} Children`;
    }
  
    const displayElem = document.getElementById('passengerInput');
    if (displayElem) displayElem.value = label;
  }

  // LOAD SEARCH DATA FROM DASHBOARD
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
  
    if (!fromCode && !toCode && !departDate) return;
  
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    if (fromInput && fromCode && fromText) {
      fromInput.value = fromText;
      fromInput.setAttribute('data-code', fromCode);
    }
    if (toInput && toCode && toText) {
      toInput.value = toText;
      toInput.setAttribute('data-code', toCode);
    }
  
    const departDateFinal = departDate || '';
    const returnDateFinal = (trip === 'round') ? (returnDate || '') : '';
  
    const departInput = document.getElementById('departDate');
    const returnInput = document.getElementById('returnDate');
    if (departInput) departInput.value = departDateFinal;
    if (returnInput && trip === 'round') returnInput.value = returnDateFinal;
  
    updateDateDisplay(departDateFinal, returnDateFinal, trip);
  
    const oneWayRadio = document.getElementById('oneWay');
    const roundTripRadio = document.getElementById('roundTrip');
    const returnField = document.getElementById('returnField');
    if (trip === 'round' && roundTripRadio) {
      roundTripRadio.checked = true;
      if (returnField) returnField.style.display = 'flex';
    } else if (oneWayRadio) {
      oneWayRadio.checked = true;
      if (returnField) returnField.style.display = 'none';
    }
  
    window.adultCount = parseInt(adults);
    window.childCount = parseInt(children);
    if (typeof updatePassengerDisplay === 'function') updatePassengerDisplay();
  
    // ⬇️ Now define the object before using it
    const searchInfo = {
      from: fromCode,
      to: toCode,
      fromText,
      toText,
      departDate: departDateFinal,
      returnDate: returnDateFinal,
      adults,
      children,
      trip,
      seatClass
    };
  
    sessionStorage.setItem("flightSearch", JSON.stringify(searchInfo));
    document.getElementById('flightResults').innerHTML = '';
  
    if (searchInfo.trip === 'round') {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'depart'));
    } else {
      fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'one'));
    }
  }  

  // INITIALIZE SEARCH BAR FROM URL PARAMETERS
  preloadSearchFromDashboard();

  // DATE HANDLING (Click on display → open picker, update text
  document.getElementById('dateDisplay').addEventListener('click', () => {
    document.getElementById('departDate').click();
  });

  document.getElementById('departDate').addEventListener('change', () => {
    const trip = document.querySelector('input[name="tripType"]:checked')?.value || 'one';
    if (trip === 'round') document.getElementById('returnDate').click();
    updateDateDisplay(departDate.value, returnDate.value, trip);
  });

  document.getElementById('returnDate').addEventListener('change', () => {
    updateDateDisplay(departDate.value, returnDate.value, 'round');
  });

  document.querySelectorAll('input[name="tripType"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const trip = radio.value;
      if (trip === 'round') {
        if (returnDate.value) updateDateDisplay(departDate.value, returnDate.value, 'round');
        else returnDate.click();
      } else {
        updateDateDisplay(departDate.value, '', 'one');
      }
    });
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
    formData.append("date", params.date); 
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
  function renderFlights(flights, containerId, tripType) {
    const container = document.getElementById(containerId);
    console.log(`🧭 Rendering flights for tripType: ${tripType}`);

   // Clear previous results depending on trip type
  if (tripType === 'depart' || tripType === 'one') {
    container.innerHTML = '';
  }

  if (tripType === 'return') {
    const oldReturnSection = container.querySelector('.return-section');
    if (oldReturnSection) oldReturnSection.remove();
  }
  
    // Handle empty results
    if (!flights || flights.length === 0) {
      container.innerHTML += `<p>No ${tripType === 'return' ? 'return' : 'departure'} flights found.</p>`;
      return;
    }
  
    const label = tripType === 'return'
      ? 'Select Return Flight'
      : tripType === 'depart'
      ? 'Select Departure Flight'
      : 'Available Flights';
  
    let html = `
      <div class="flight-section ${tripType}-section">
        <h3 class="flight-section-label">${label}</h3>
    `;
  
    flights.forEach(flight => {
      const duration = calculateDuration(flight.departure_time, flight.arrival_time);
      html += `
        <div class="flight-card">
          <div class="airline-section">
            <img src="images/${flight.airline_id}.png" class="airline-logo" alt="${flight.airline_id} Logo">
            <span class="airline-name">${flight.airline_id}</span>
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
              <div class="flight-detail">
                <span class="flight-time">${formatTime(flight.departure_time)} - ${formatTime(flight.arrival_time)}</span>
                <span class="flight-meta">${flight.airline_id}</span>
              </div>
              <div class="flight-detail">
                <span class="flight-time">non stop</span>
              </div>
              <div class="flight-detail">
                <span class="flight-meta-duration">${duration}</span>
                <span class="flight-meta">${flight.orig_airport_id} - ${flight.dest_airport_id}</span>
              </div>
            </div>
            <div class="buttons-row">
              <button class="heart-btn"><i class="fa-solid fa-heart"></i></button>
              <button class="view-details-btn" 
                  data-flight-id="${flight.flight_id}" 
                  data-type="${tripType}">
                View Details
              </button>
            </div>
          </div>
        </div>
      `;
    });
  
    html += `</div>`;
    container.innerHTML += html;
  
    attachViewHandlers();
  }
  
  
  
  //Attach event handlers to "View Details" buttons 
  function attachViewHandlers() {
    document.querySelectorAll('.view-details-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const flightId = this.dataset.flightId;
        const type = this.dataset.type;
        const searchData = getSearchInfo();
  
        if (type === 'depart') {
          searchData.depart = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
  
          console.log("✈️ Selected DEPARTURE flight:", flightId);
          console.log("📦 Stored searchData after departure:", searchData);
  
          const departSection = document.querySelector('.depart-section');
          if (departSection) departSection.remove();
  
          // ✅ Use latest filters for return flight search
          const fullSearch = getSearchInfo();
          const reversedSearch = {
            from: fullSearch.to,
            to: fullSearch.from,
            date: fullSearch.returnDate,
            seatClass: fullSearch.seatClass || 'PE',
            airlines: fullSearch.airlines || '',
            timeFrom: fullSearch.timeFrom || '00:00',
            timeTo: fullSearch.timeTo || '23:59',
            sortBy: fullSearch.sortBy || ''
          };
  
          console.log("🔁 Reversed return search:", reversedSearch);
  
          fetchFlights(reversedSearch, data => {
            renderFlights(data, 'flightResults', 'return');
          });
  
        } else if (type === 'return') {
          searchData.return = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
  
          console.log("🔙 Selected RETURN flight:", flightId);
          console.log("📦 Stored searchData after return:", searchData);
  
          // Redirect to flight details with both depart and return
          window.location.href = `flightDetails.php?depart=${searchData.depart}&classId=${searchData.seatClass}&return=${searchData.return}&classId=${searchData.seatClass}`;
  
        } else {
          searchData.selectedFlight = flightId;
          sessionStorage.setItem("flightSearch", JSON.stringify(searchData));
  
          console.log("🧳 One-way selected flight:", flightId);
          console.log("📦 Stored searchData for one-way:", searchData);
  
          // Redirect to one-way flight details
          window.location.href = `flightDetails.php?flightId=${flightId}&classId=${searchData.seatClass}`;
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

    updateDateDisplay(departDate, returnDate, tripType);
  });

  // CANCEL FILTERS
  document.getElementById('cancelBtn').addEventListener('click', function () {
    document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('selected'));
    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(cb => cb.checked = false);
    const timeSlider = document.getElementById('timeRange');
    if (timeSlider) {
      timeSlider.value = 0;
      showSelectedTime(0);
    }
    const searchInfo = getSearchInfo();
    searchInfo.seatClass = '';
    searchInfo.airlines = '';
    searchInfo.timeFrom = '00:00';
    searchInfo.timeTo = '23:59';
    searchInfo.sortBy = '';
    saveSearchInfo(searchInfo);
    fetchFlights(searchInfo, data => renderFlights(data, 'flightResults'));
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
  
    const seatClassBtn = document.querySelector('.filter-button.selected');
    const seatClassText = seatClassBtn ? seatClassBtn.textContent.trim() : '';
    let seatClass = '';
    switch (seatClassText) {
      case 'Economy': seatClass = 'EC'; break;
      case 'Premium Economy': seatClass = 'PE'; break;
      case 'Business Class': seatClass = 'BC'; break;
      case 'First Class': seatClass = 'FC'; break;
      default: seatClass = 'PE'; break;
    }
  
    const airlineNameToCode = { 'AirAsia': 'AK', 'Mas': 'MH', 'FireFly': 'FY' };
    const airlineChecks = Array.from(document.querySelectorAll('.checkbox-group input[type="checkbox"]:checked'));
    const airlines = airlineChecks
      .map(cb => airlineNameToCode[cb.parentElement.textContent.trim()] || cb.parentElement.textContent.trim())
      .join(',');
  
    const timeFrom = '06:00';
    const timeTo = '23:59';
 
    searchInfo.seatClass = seatClass;
    searchInfo.airlines = airlines;
    searchInfo.timeFrom = timeFrom;
    searchInfo.timeTo = timeTo;
    searchInfo.sortBy = '';

    sessionStorage.setItem("flightSearch", JSON.stringify(searchInfo));
    updateDateDisplay(searchInfo.departDate, searchInfo.returnDate, searchInfo.trip);
    
    const isRoundTrip = searchInfo.trip === 'round';
    const isDepartSelected = !!searchInfo.depart;
    
    if (isRoundTrip) {
      if (isDepartSelected) {
        // ✅ Apply filters to return ONLY if departure flight is selected
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
    
        console.log('📦 Applying filters to RETURN flight:', returnSearch);
        fetchFlights(returnSearch, data => renderFlights(data, 'flightResults', 'return'));
    
      } else {
        // ✅ Apply filters only to departure
        console.log('📦 Applying filters to DEPARTURE flight:', searchInfo);
        fetchFlights(searchInfo, data => renderFlights(data, 'flightResults', 'depart'));
      }
    
    } else {
      // One-way trip
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
});
