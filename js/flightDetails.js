document.addEventListener('DOMContentLoaded', () => {
  // Retrieve the entire flightSearch object from sessionStorage
  const searchData = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');

  const departId = searchData.depart; // Get depart flight ID from sessionStorage
  const returnId = searchData.return; // Get return flight ID from sessionStorage
  const oneWayId = searchData.selectedFlight; // Get one-way flight ID from sessionStorage
  const tripType = searchData.trip; // Get trip type from sessionStorage

  const returnSection = document.getElementById('return-flight-section');

  window.departFlightPrice = null;
  window.returnFlightPrice = null;

  // Initial load: determine the selected seat class from searchData, defaulting to 'PE'
  let currentSelectedClass = searchData.seatClass || "PE";

  // --- NEW: Define classMap here ---
  const classMap = {
    'EC': 'Economy Class',
    'PE': 'Premium Economy',
    'BC': 'Business Class',
    'FC': 'First Class'
  };
  // --- END NEW ---

  highlightSeatClass(currentSelectedClass, 'depart'); // Highlight for depart flight initially

  // Store the initial flight IDs for later use in seat class selection
  window.currentDepartFlightId = departId || oneWayId;
  window.currentReturnFlightId = returnId;
  window.currentTripType = tripType;

  if (window.currentDepartFlightId && tripType === 'round' && window.currentReturnFlightId) {
    loadFlightDetails(window.currentDepartFlightId, 'depart', currentSelectedClass);
    loadFlightDetails(window.currentReturnFlightId, 'return', currentSelectedClass);
  } else if (window.currentDepartFlightId && tripType === 'one') {
    loadFlightDetails(window.currentDepartFlightId, 'depart', currentSelectedClass);
    if (returnSection) returnSection.style.display = 'none';
  } else {
    alert('No flight selected or invalid trip type.');
  }

  document.querySelectorAll('.seat-option').forEach(button => {
    button.addEventListener('click', function() {
      const newClassCode = this.dataset.class;

      currentSelectedClass = newClassCode;

      searchData.seatClass = newClassCode;
      searchData.classId = newClassCode; // Ensure classId is also updated in flightSearch for payment.js
      sessionStorage.setItem('flightSearch', JSON.stringify(searchData));

      highlightSeatClass(newClassCode, 'depart');

      window.departFlightPrice = null;
      window.returnFlightPrice = null;

      if (window.currentDepartFlightId && window.currentTripType === 'round' && window.currentReturnFlightId) {
        loadFlightDetails(window.currentDepartFlightId, 'depart', newClassCode);
        loadFlightDetails(window.currentReturnFlightId, 'return', newClassCode);
      } else if (window.currentDepartFlightId && window.currentTripType === 'one') {
        loadFlightDetails(window.currentDepartFlightId, 'depart', newClassCode);
      }
    });
  });
});

function highlightSeatClass(classCode, type = 'depart') {
  document.querySelectorAll(`#${type}-seat-classes .seat-option`).forEach(option => {
    option.classList.toggle('highlighted', option.dataset.class === classCode);
  });
}

function loadFlightDetails(flightId, type, classCode) {
  const classParam = classCode;

  fetch(`getFlightDetails.php?flightId=${flightId}&classId=${classParam}`)
    .then(res => res.json())
    .then(flight => {
      if (flight.error) {
        alert(flight.error);
        return;
      }

      const formattedDeparture = formatTimeTo12Hour(flight.departure_time);
      const formattedArrival = formatTimeTo12Hour(flight.arrival_time);
      const durationMinutes = calculateDuration(flight.departure_time, flight.arrival_time);
      const duration = formatDuration(durationMinutes);

      const updateText = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
      };

      updateText(`${type}-departure-time`, formattedDeparture);
      updateText(`${type}-arrival-time`, formattedArrival);
      updateText(`${type}-departure-airport-code`, flight.orig_airport_id);
      updateText(`${type}-arrival-airport-code`, flight.dest_airport_id);
      updateText(`${type}-flight-duration`, duration);
      updateText(`${type}-airline-name`, flight.airline_name);
      updateText(`${type}-aircraft-type`, flight.flight_id);

      const price = parseFloat(flight.price);
      updateText(`${type}-flight-price`, `RM ${price.toFixed(2)}`);

      // --- NEW: Update the class name display ---
      const classMap = { // Re-define classMap inside or move to global scope if not already
        'EC': 'Economy Class',
        'PE': 'Premium Economy',
        'BC': 'Business Class',
        'FC': 'First Class'
      };
      const classDisplayName = classMap[classCode] || 'Unknown Class';
      const classDisplayEl = document.getElementById('flight-class-display'); // This ID must exist in your HTML
      if (classDisplayEl) {
          classDisplayEl.textContent = classDisplayName;
      }
      // --- END NEW ---

      // Retrieve searchData again to ensure it's the latest from sessionStorage before modification
      const searchData = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');

      if (type === 'depart') {
        updateText("depart-flight-airline-plane", flight.airline_name || flight.airline_id);
        updateText(`${type}-origin-airport-info`, `${flight.origin_airport_full || ''}, ${flight.origin_airport_address || ''}`);
        const airlineImage = document.getElementById("depart-airplane-image");
        if (airlineImage) airlineImage.src = `images/${flight.airline_id}.jpg`;
        const logoImage = document.getElementById("depart-airline-logo");
        if (logoImage) {
          logoImage.src = `images/${flight.airline_id}.png`;
          logoImage.alt = `${flight.airline_name} Logo`;
        }
        window.departFlightPrice = price;
        searchData.departFlightPricePerPax = price;
        
        // Store airline_id in sessionStorage for feedback display
        searchData.airline_id = flight.airline_id;
      } else {
        const logoImage = document.getElementById("return-airline-logo");
        if (logoImage) {
          logoImage.src = `images/${flight.airline_id}.png`;
          logoImage.alt = `${flight.airline_name} Logo`;
        }
        window.returnFlightPrice = price;
        searchData.returnFlightPricePerPax = price;
        
        // If this is a return flight, we still want to keep the depart flight's airline_id
        if (!searchData.airline_id) {
          searchData.airline_id = flight.airline_id;
        }
      }

      sessionStorage.setItem('flightSearch', JSON.stringify(searchData));

      updateTotalFlightPriceIfReady();
      highlightSeatClass(classParam, type);
    })
    .catch(err => console.error(err));
}

function updateTotalFlightPriceIfReady() {
  const totalDisplay = document.getElementById('total-flight-price');
  if (!totalDisplay || window.departFlightPrice === null) return;

  const searchData = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');
  const adults = parseInt(searchData.adults) || 1;
  const children = parseInt(searchData.children) || 0;
  const passengerCount = adults + children;

  const ret = window.returnFlightPrice !== null ? window.returnFlightPrice : 0;
  const total = (window.departFlightPrice + ret) * passengerCount;
  totalDisplay.textContent = `RM ${total.toFixed(2)}`;

  searchData.totalPrice = total;
  sessionStorage.setItem('flightSearch', JSON.stringify(searchData));
}

function formatTimeTo12Hour(timeString) {
  const [hourStr, minuteStr] = timeString.split(':');
  let hour = parseInt(hourStr);
  const ampm = hour >= 12 ? 'pm' : 'am';
  hour = hour % 12 || 12;
  return `${hour}:${minuteStr} ${ampm}`;
}

function calculateDuration(dep, arr) {
  const [depH, depM] = dep.split(':').map(Number);
  const [arrH, arrM] = arr.split(':').map(Number);
  let depTotal = depH * 60 + depM;
  let arrTotal = arrH * 60 + arrM;
  if (arrTotal < depTotal) arrTotal += 1440;
  return arrTotal - depTotal;
}

function formatDuration(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours}h ${mins}m`;
}

const likeButton = document.querySelector('.like-button');
if (likeButton) {
  likeButton.addEventListener('click', () => {
    likeButton.classList.toggle('liked');
  });
}


window.handleBookNowClick = function(event) {
  if (!userLoggedIn) {
    event.preventDefault();
    if (typeof window.showLoginReminder === 'function') {
      window.showLoginReminder();
    } else {
      alert('Please sign in or create an account before booking.');
    }
    return false;
  }
  return true;
};

const bookButton = document.querySelector('.book-now-btn');
const userLoggedIn = window.userLoggedIn !== undefined ? window.userLoggedIn : false;

window.handleBookNowClick = function (event) {
  if (!userLoggedIn) {
    event.preventDefault();
    alert('Please sign in or create an account before booking.');
    return false;
  }
  return true;
};

if (bookButton) {
  bookButton.addEventListener('click', function (e) {
    if (!window.handleBookNowClick(e)) return;

    const totalText = document.getElementById('total-flight-price')?.textContent || 'RM 0.00';
    const totalPrice = parseFloat(totalText.replace('RM', '').trim()).toFixed(2);

    const flightSearch = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");

    flightSearch.totalPrice = totalPrice;
    sessionStorage.setItem("flightSearch", JSON.stringify(flightSearch));

    window.location.href = 'passengerCheck.php';
  });
}