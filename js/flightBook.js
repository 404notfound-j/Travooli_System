// Get query parameters from previous page
const urlParams = new URLSearchParams(window.location.search);
const from = urlParams.get('from') || 'From where?';
const to = urlParams.get('to') || 'Where to?';
const passenger = urlParams.get('passenger') || '1 adult';
let storedFromCode = "";
let storedToCode = "";
let currentTrip = "depart"; 

// Airline shortcodes mapping
const airlineCodeMap = {
  "AirAsia": "AK",
  "BatikAir": "OD",
  "Mas": "MH",
  "FireFly": "FY"
};

// Format date to yyyy-mm-dd
function formatToISODate(dateStr) {
  const currentYear = new Date().getFullYear();
  const [day, month] = dateStr.split('/');
  if (!day || !month) return "";
  return `${currentYear}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
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
    
      const sortBy = sortLabel.includes("cheapest") ? "cheapest" : "best"; // Add more if needed
    
      fetchAndRenderFlights({
        sortBy: sortBy,
        trip: selectingReturn ? "return" : currentTrip
      });
    });    
  }
)};

function fetchAndRenderFlights(options = {}) {
  const seatClassMap = {
    "Economy": "EC",
    "Premium Economy": "PE",
    "Business Class": "BC",
    "First Class": "FC"
  };

  let selectedClass = "";
  if (options.forceSeatClass) {
    selectedClass = options.forceSeatClass;
  } else {
    document.querySelectorAll(".filter-button").forEach(btn => {
      if (btn.classList.contains("selected")) {
        selectedClass = seatClassMap[btn.innerText.trim()];
      }
    });
  }
  if (options.trip) {
    currentTrip = options.trip; 
  }  
  let minutes = parseInt(document.getElementById("timeRange").value);
  let hours = Math.floor(minutes / 60);
  let mins = minutes % 60;
  let timeFrom = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:00`;

  const formData = new FormData();
  if (!options.sortBy || options.sortBy === "best") {
    formData.append("seatClass", selectedClass);
  }
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

  console.log("User Search Input:");
  for (const [key, value] of formData.entries()) {
    console.log(`  ${key}: ${value}`);
  }

  fetch("getflight.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      console.log("Backend returned:", data.length, "flight(s)");
      console.log(data);
      renderFlights(data);
    })
    .catch(err => console.error("Error fetching flights:", err));
}

// Handle search or filter click
function handleSearchOrFilter(isFilter = false) {
  storedFromCode = document.getElementById('fromAirport').value.split('(')[1]?.replace(')', '').trim() || '';
  storedToCode = document.getElementById('toAirport').value.split('(')[1]?.replace(')', '').trim() || '';  
  const departRaw = document.getElementById('departDate').value;
  const returnRaw = document.getElementById('returnDate').value;
  const depart = formatToISODate(departRaw);
  const returnDate = formatToISODate(returnRaw);
  const tripType = document.querySelector('input[name="tripType"]:checked')?.value || "oneway";
  const origin = from.split('(')[1]?.replace(')', '').trim() || "";
  const destination = to.split('(')[1]?.replace(')', '').trim() || "";
  const fromAirportInput = document.getElementById('fromAirport').value;
  const toAirportInput = document.getElementById('toAirport').value;
  const resultsContainer = document.getElementById('flightResults');
  if (resultsContainer) {
    resultsContainer.style.display = 'block';
    resultsContainer.innerHTML = '';
  }
  
  if (isFilter) {
    // If user is selecting return flight, skip filter re-fetch
    if (sessionStorage.getItem("selectingReturn") === "true") {
      console.log("User is selecting return flight — skipping filter re-fetch");
      return;
    }
    fetchAndRenderFlights({ trip: currentTrip })
}else{
    // Search logic
    if (tripType === "round" && returnDate) {
      // Fetch departing flight
      fetchAndRenderFlights({ origin, destination, date: depart, trip: "depart", forceSeatClass: "PE" });
      // Fetch returning flight
      fetchAndRenderFlights({ origin, destination, date: depart, trip: "depart", forceSeatClass: "PE" });
    } else {
      // Oneway: send only depart data
      fetchAndRenderFlights({ origin, destination, date: depart, trip: "depart", forceSeatClass: "PE" });
    }
  }
}

// Render flights to page
function renderFlights(flights) {
  const container = document.getElementById('flightResults');
  if (!container) return;
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
          <img src="" alt="${flight.airline_id} Logo" class="airline-logo">
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
    container.innerHTML += flightBox;
  });
}

// Favorite and view details interaction
function setupFlightResultEvents() {
  document.getElementById('flightResults').addEventListener('click', (event) => {
    const heartBtn = event.target.closest('.heart-btn');
    if (heartBtn) {
      heartBtn.classList.toggle('favorited');
    }

    const viewBtn = event.target.closest('.view-details-btn');
if (viewBtn) {
  const flightId = viewBtn.dataset.flightId;
  const selectingReturn = sessionStorage.getItem("selectingReturn") === "true";
  const tripType = document.querySelector('input[name="tripType"]:checked')?.value || "oneway";

  if (selectingReturn) {
    // === You are now selecting return ===
    sessionStorage.setItem("selectedReturnFlight", flightId);
    sessionStorage.setItem("selectingReturn", "false"); // Clear flag

    const departFlightId = sessionStorage.getItem("selectedDepartFlight");
    const returnFlightId = sessionStorage.getItem("selectedReturnFlight");

    window.location.href = `flightDetails.php?depart=${departFlightId}&return=${returnFlightId}`;
    return;
  }

  if (tripType === "round") {
    // === Selecting departure ===
    const from = document.getElementById('fromAirport').value;
    const to = document.getElementById('toAirport').value;
    const returnRaw = document.getElementById('returnDate').value;

    sessionStorage.setItem("selectedDepartFlight", flightId);
    sessionStorage.setItem("departFrom", from);
    sessionStorage.setItem("departTo", to);
    sessionStorage.setItem("selectingReturn", "true");

    const fromCode = from.includes('(') ? from.split('(')[1].replace(')', '').trim() : '';
    const toCode = to.includes('(') ? to.split('(')[1].replace(')', '').trim() : '';
    const returnDate = formatToISODate(returnRaw);

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
    // === One-way booking ===
    window.location.href = `flightDetails.php?flightId=${flightId}`;
  }
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

document.addEventListener('DOMContentLoaded', initializeFlightBookingUI);
