//from dashboard
const urlParams = new URLSearchParams(window.location.search);
const fromCode = urlParams.get('from') || '';
const toCode = urlParams.get('to') || '';
const rawDepartDate = urlParams.get('departDate') || '';
const rawReturnDate = urlParams.get('returnDate') || '';
const departDate = convertToDDMM(rawDepartDate);
const returnDate = convertToDDMM(rawReturnDate);
const trip = urlParams.get('trip') || 'one';
const adults = parseInt(urlParams.get('adults')) || 1;
const children = parseInt(urlParams.get('children')) || 0;
const passengerLabel = `${adults} adult${adults > 1 ? 's' : ''}${children > 0 ? ', ' + children + ' child' + (children > 1 ? 'ren' : '') : ''}`;
const passenger = passengerLabel;
let storedFromCode = fromCode;
let storedToCode = toCode;
let currentTrip = (trip === 'round') ? 'depart' : 'oneway';

// Airline shortcodes mapping
const airlineCodeMap = {
  "AirAsia": "AK",
  "Mas": "MH",
  "FireFly": "FY"
};
document.addEventListener('DOMContentLoaded', () => {
  initializeFlightBookingUI();

  if (storedFromCode && storedToCode && departDate) {
    const fromInput = document.getElementById('fromAirport');
    const toInput = document.getElementById('toAirport');
    const departInput = document.getElementById('departDate');
    const returnInput = document.getElementById('returnDate');

    if (fromInput) fromInput.value = storedFromCode;
    if (toInput) toInput.value = storedToCode;
    if (departInput) departInput.value = rawDepartDate;
    if (returnInput && trip === "round") returnInput.value = rawReturnDate;

    const tripRadio = document.querySelector(`input[name="tripType"][value="${trip}"]`);
    if (tripRadio) tripRadio.checked = true;

    // Only fetch DEPARTURE flights
    const origin = storedFromCode;
    const destination = storedToCode;
    const depart = formatToISODate(convertToDDMM(rawDepartDate));

    sessionStorage.setItem("selectingReturn", "false");

    fetchAndRenderFlights({
      origin: origin,
      destination: destination,
      date: depart,
      trip: "depart"
    });
  }
});


function convertToDDMM(dateStr) {
  const parts = dateStr.split(' ');
  if (parts.length < 3) return ''; // Not enough parts to process

  const dayRaw = parts[2];
  const monthAbbr = parts[1];

  if (!dayRaw || !monthAbbr) return '';

  const day = dayRaw.padStart(2, '0');
  const monthMap = {
    Jan: '01', Feb: '02', Mar: '03', Apr: '04',
    May: '05', Jun: '06', Jul: '07', Aug: '08',
    Sep: '09', Oct: '10', Nov: '11', Dec: '12'
  };

  const month = monthMap[monthAbbr];
  return day && month ? `${day}/${month}` : '';
}

function formatToISODate(ddmm) {
  if (!ddmm || !ddmm.includes('/')) return ''; // Prevents crash
  const [day, month] = ddmm.split('/');
  if (!day || !month) return '';
  const year = new Date().getFullYear();
  return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
}


// Convert minutes to time format
function formatTime(timeStr) {
  if (typeof timeStr === 'number') {
    const hours = Math.floor(timeStr / 60);
    const minutes = timeStr % 60;
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 === 0 ? 12 : hours % 12;
    return `${displayHours}:${minutes.toString().padStart(2, '0')}${ampm}`;
  }
  const [h, m] = timeStr.split(':').map(Number);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const displayHours = h % 12 === 0 ? 12 : h % 12;
  return `${displayHours}:${m.toString().padStart(2, '0')}${ampm}`;
}

function calculateDuration(departure, arrival) {
  const [depH, depM] = departure.split(':').map(Number);
  const [arrH, arrM] = arrival.split(':').map(Number);
  let depMinutes = depH * 60 + depM;
  let arrMinutes = arrH * 60 + arrM;
  if (arrMinutes < depMinutes) arrMinutes += 24 * 60;
  const durationMinutes = arrMinutes - depMinutes;
  const hours = Math.floor(durationMinutes / 60);
  const minutes = durationMinutes % 60;
  return `${hours}h ${minutes}m`;
}

function getSelectedAirlines() {
  const selectedCodes = [];
  document.querySelectorAll('.filter-group .checkbox-group input[type="checkbox"]').forEach(checkbox => {
    if (checkbox.checked) {
      const label = checkbox.parentElement.textContent.trim();
      const code = airlineCodeMap[label];
      if (code) selectedCodes.push(code);
    }
  });
  return selectedCodes;
}

const range = document.getElementById('timeRange');
const startTime = document.getElementById('startTime');
if (range && startTime) {
  startTime.textContent = formatTime(parseInt(range.value));
  range.addEventListener('input', () => {
    startTime.textContent = formatTime(parseInt(range.value));
  });
}

function setupFilterButtons() {
  document.querySelectorAll('.filter-button, .rating-buttons button').forEach(btn => {
    btn.addEventListener('click', () => {
      const parentGroup = btn.closest('.filter-group');
      if (parentGroup) {
        if (btn.classList.contains('selected')) {
          btn.classList.remove('selected');
        } else {
          parentGroup.querySelectorAll('button').forEach(b => b.classList.remove('selected'));
          btn.classList.add('selected');
        }
      }
    });
  });
}

function setupSortOptions() {
  document.querySelectorAll('.sort-option').forEach(option => {
    option.addEventListener('click', () => {
      const selectingReturn = sessionStorage.getItem("selectingReturn") === "true";

      document.querySelectorAll('.sort-option').forEach(opt => opt.classList.remove('selected'));
      option.classList.add('selected');
      const sortLabel = option.textContent.trim().toLowerCase();
      const sortBy = sortLabel.includes("cheapest") ? "cheapest" : "best";

      fetchAndRenderFlights({
        sortBy: sortBy,
        trip: selectingReturn ? "return" : currentTrip
      });
    });
  });
}

function fetchAndRenderFlights(options = {}) {
  const seatClassMap = {
    "Economy": "EC",
    "Premium Economy": "PE",
    "Business Class": "BC",
    "First Class": "FC"
  };

  let selectedClass = "PE";
  document.querySelectorAll(".filter-button").forEach(btn => {
    if (btn.classList.contains("selected")) {
      selectedClass = seatClassMap[btn.innerText.trim()] || "PE";
    }
  });

  if (options.trip) currentTrip = options.trip;

  let minutes = parseInt(document.getElementById("timeRange").value);
  let hours = Math.floor(minutes / 60);
  let mins = minutes % 60;
  let timeFrom = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:00`;

  const formData = new FormData();
  formData.append("seatClass", selectedClass);
  console.log("Selected Seat Class:", selectedClass);
  formData.append("timeFrom", timeFrom);
  formData.append("timeTo", "23:00:00");

  const selectedAirlines = getSelectedAirlines();
  if (selectedAirlines.length > 0) {
    formData.append("airlines", selectedAirlines.join(","));
  }

  if (options.origin) formData.append("origin", options.origin);
  if (options.destination) formData.append("destination", options.destination);
  if (options.date) formData.append("date", options.date);
  if (options.trip) formData.append("trip", options.trip);
  if (options.sortBy === "cheapest") formData.append("sortBy", "cheapest");
  for (let [key, value] of formData.entries()) {
    console.log(`  ${key}: ${value}`);
  }

  fetch("getflight.php", {
    method: "POST",
    body: formData
  })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(data => {
      console.log("Received response from getflight.php:", data);
      renderFlights(data);
    })
    .catch(err => {
    });
}

function extractAirportCode(value) {
  if (value.includes('(')) {
    return value.split('(')[1].replace(')', '').trim();
  }
  return value.trim(); 
}

// Handle search or filter click
function handleSearchOrFilter(isFilter = false) {
  const fromAirportInput = document.getElementById('fromAirport').value;
  const toAirportInput = document.getElementById('toAirport').value;
  const departRaw = document.getElementById('departDate').value;
  const returnRaw = document.getElementById('returnDate').value;
  const depart = formatToISODate(departRaw);
  const returnDate = formatToISODate(returnRaw);
  const resultsContainer = document.getElementById('flightResults');
 


  let tripType = document.querySelector('input[name="tripType"]:checked')?.value || "oneway";

  if (!fromAirportInput || !toAirportInput || !departRaw || (tripType === "round" && !returnRaw)) {
    alert("Please complete all required fields including Return Date for Round Trip.");
    return;
  }
 console.log("Search Submitted:");
console.log("From:", fromAirportInput);
console.log("To:", toAirportInput);
console.log("Depart Date:", departRaw);
console.log("Return Date:", returnRaw);
console.log("Passenger(s):", passenger);
console.log("Trip Type:", tripType);
const origin = fromAirportInput.includes('(') ? fromAirportInput.split('(')[1].replace(')', '').trim() : fromAirportInput;
const destination = toAirportInput.includes('(') ? toAirportInput.split('(')[1].replace(')', '').trim() : toAirportInput;
  if (resultsContainer) {
    resultsContainer.style.display = 'block';
    resultsContainer.innerHTML = '';
  }

  if (isFilter) {
    if (sessionStorage.getItem("selectingReturn") === "true") return;
    fetchAndRenderFlights({ trip: currentTrip });
  } else {
    if (tripType === "round") {
      fetchAndRenderFlights({ origin, destination, date: depart, trip: "depart" });
      sessionStorage.setItem("selectingReturn", "false");
    } else {
      fetchAndRenderFlights({ origin, destination, date: depart, trip: "depart" });
    }    
  }
}

// Render flights to page
function renderFlights(flights) {
  const container = document.getElementById('flightResults');
  if (!container) return;

  let fullHTML = '';
  flights.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));

  if (flights.length > 0) {
    const cheapestPrice = parseFloat(flights[0].price).toFixed(2);
    const cheapestTextElement = document.querySelector('.sort-option');
    if (cheapestTextElement) {
      cheapestTextElement.innerHTML = `Cheapest<br><span>RM ${cheapestPrice}</span>`;
    }
  }

  flights.forEach(flight => {
    const duration = calculateDuration(flight.departure_time, flight.arrival_time);
    const flightBox = `
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
            <button class="view-details-btn" data-flight-id="${flight.flight_id}">View Details</button>
          </div>
        </div>
      </div>
    `;
    fullHTML += flightBox;
  });

  container.innerHTML = fullHTML; // ✅ Commit all at once
}

function setupFlightResultEvents() {
  const container = document.getElementById('flightResults');
  if (!container) return;

  console.log("📌 Event listener attached to #flightResults");

  container.addEventListener('click', (event) => {
    const heartBtn = event.target.closest('.heart-btn');
    if (heartBtn) {
      heartBtn.classList.toggle('favorited');
      return;
    }
      const viewBtn = event.target.closest('.view-details-btn');
      if (!viewBtn) return;
    
      // ✅ Prevent auto-clicking or programmatic trigger
      if (!event.isTrusted) {
        console.warn("⛔ Ignored synthetic or auto click");
        return;
      }
    
      console.log("🖱️ View Details clicked");
    
      // Proceed as normal...
    

    const flightId = viewBtn.dataset.flightId;
    let selectedClass = "PE";
    document.querySelectorAll(".filter-button").forEach(btn => {
      if (btn.classList.contains("selected")) {
        const label = btn.innerText.trim();
        const seatClassMap = {
          "Economy": "EC",
          "Premium Economy": "PE",
          "Business Class": "BC",
          "First Class": "FC"
        };
        selectedClass = seatClassMap[label] || "PE";
      }
    });

    sessionStorage.setItem("selectedSeatClass", selectedClass);
    const adultCount = parseInt(document.getElementById('adultCount')?.value) || 1;
    const childCount = parseInt(document.getElementById('childCount')?.value) || 0;
    sessionStorage.setItem("adultCount", adultCount);
    sessionStorage.setItem("childCount", childCount);

    const selectingReturn = sessionStorage.getItem("selectingReturn") === "true";

    // Force tripType to 'round' if selectingReturn is true
    let tripType = "one";
    if (selectingReturn) {
      tripType = "round";
    } else {
      tripType = document.querySelector('input[name="tripType"]:checked')?.value || "one";
    }    

    if (selectingReturn) {
      sessionStorage.setItem("selectedReturnFlight", flightId);
      sessionStorage.setItem("selectedReturnClass", selectedClass);
      sessionStorage.setItem("selectingReturn", "false");

      const departFlightId = sessionStorage.getItem("selectedDepartFlight");
      const returnFlightId = flightId;
      const departClass = sessionStorage.getItem("selectedDepartClass") || "PE";
      const returnClass = selectedClass;
      window.location.href = `flightDetails.php?depart=${departFlightId}&return=${returnFlightId}&departClass=${departClass}&returnClass=${returnClass}`;
      return;
    }

    if (tripType === "round") {
      const from = document.getElementById('fromAirport').value;
      const to = document.getElementById('toAirport').value;
      const returnRaw = document.getElementById('returnDate').value;

      const fromCode = extractAirportCode(from);
      const toCode = extractAirportCode(to);
      const returnDate = formatToISODate(returnRaw);

      sessionStorage.setItem("selectedDepartFlight", flightId);
      sessionStorage.setItem("departFrom", fromCode);
      sessionStorage.setItem("departTo", toCode);
      sessionStorage.setItem("selectingReturn", "true");

      const resultsContainer = document.getElementById('flightResults');
      if (resultsContainer) {
        resultsContainer.innerHTML = `<h3>Select Return Flight (${toCode} → ${fromCode})</h3>`;
      }

      fetchAndRenderFlights({
        origin: toCode,
        destination: fromCode,
        date: returnDate,
        trip: "return"
      });
    } else {
      window.location.href = `flightDetails.php?flightId=${flightId}&classId=${selectedClass}`;
    }
  });
}


// Clear user searches on load
function clearUserSearches() {
  localStorage.removeItem('userSearches');
  console.log('User searches cleared.');
}

// Attach main events
function initializeFlightBookingUI() {
  clearUserSearches();
  setupFilterButtons();
  setupSortOptions();
  setupFlightResultEvents();
  const searchBtn = document.getElementById('searchBtn');
  if (searchBtn) searchBtn.addEventListener('click', () => handleSearchOrFilter(false));
  const applyBtn = document.querySelector('.apply-btn');
  if (applyBtn) applyBtn.addEventListener('click', () => handleSearchOrFilter(true));
}
