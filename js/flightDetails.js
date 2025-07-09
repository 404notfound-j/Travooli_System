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

  highlightSeatClass(currentSelectedClass, 'depart'); // Highlight for depart flight initially

  // Store the initial flight IDs for later use in seat class selection
  // This is crucial because when a seat class changes, we need to re-fetch details
  // for the same depart/return flights but with the new class.
  window.currentDepartFlightId = departId || oneWayId; // Use oneWayId if it's a one-way trip
  window.currentReturnFlightId = returnId;
  window.currentTripType = tripType; // Store the trip type as well

  if (window.currentDepartFlightId && tripType === 'round' && window.currentReturnFlightId) {
    loadFlightDetails(window.currentDepartFlightId, 'depart', currentSelectedClass);
    loadFlightDetails(window.currentReturnFlightId, 'return', currentSelectedClass);
  } else if (window.currentDepartFlightId && tripType === 'one') {
    loadFlightDetails(window.currentDepartFlightId, 'depart', currentSelectedClass);
    if (returnSection) returnSection.style.display = 'none';
  } else {
    alert('No flight selected or invalid trip type.');
    // Optionally redirect back to search page if no valid data
    // window.location.href = 'U_dashboard.php';
  }

  // --- START NEW/MODIFIED CODE FOR SEAT CLASS SELECTION ---
  // REMOVED THE PREVIOUS BLOCK THAT CLONED AND REPLACED BUTTONS, AS IT PREVENTED EVENT LISTENERS.
  document.querySelectorAll('.seat-option').forEach(button => {
    button.addEventListener('click', function() {
      const newClassCode = this.dataset.class; // Get the new class code from data-class attribute

      // 1. Update current selected class
      currentSelectedClass = newClassCode;

      // 2. Update searchData in sessionStorage with the new seatClass
      searchData.seatClass = newClassCode;
      sessionStorage.setItem('flightSearch', JSON.stringify(searchData));

      // 3. Highlight the newly selected seat class
      highlightSeatClass(newClassCode, 'depart'); // Apply highlight to depart section

      // 4. Re-fetch flight details with the new seat class
      // Reset prices to null before fetching new ones, to ensure total is recalculated correctly
      window.departFlightPrice = null;
      window.returnFlightPrice = null;

      if (window.currentDepartFlightId && window.currentTripType === 'round' && window.currentReturnFlightId) {
        loadFlightDetails(window.currentDepartFlightId, 'depart', newClassCode);
        loadFlightDetails(window.currentReturnFlightId, 'return', newClassCode);
      } else if (window.currentDepartFlightId && window.currentTripType === 'one') {
        loadFlightDetails(window.currentDepartFlightId, 'depart', newClassCode);
      }
      // updateTotalFlightPriceIfReady() will be called inside loadFlightDetails after fetch completes for both (or one) flights
    });
  });
  // --- END NEW/MODIFIED CODE ---
});

function highlightSeatClass(classCode, type = 'depart') {
  document.querySelectorAll(`#${type}-seat-classes .seat-option`).forEach(option => {
    option.classList.toggle('highlighted', option.dataset.class === classCode);
  });
}

// Modified loadFlightDetails to accept classCode directly
function loadFlightDetails(flightId, type, classCode) {
  // Use the classCode passed to the function, which comes from searchData.seatClass
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
        // Store per-passenger price for the depart flight in sessionStorage
        searchData.departFlightPricePerPax = price; // NEW LINE
      } else {
        const logoImage = document.getElementById("return-airline-logo");
        if (logoImage) {
          logoImage.src = `images/${flight.airline_id}.png`;
          logoImage.alt = `${flight.airline_name} Logo`;
        }
        window.returnFlightPrice = price;
        // Store per-passenger price for the return flight in sessionStorage
        searchData.returnFlightPricePerPax = price; // NEW LINE
      }

      // Always update session storage after modifying searchData within this function
      sessionStorage.setItem('flightSearch', JSON.stringify(searchData)); // Moved to ensure updates are saved

      updateTotalFlightPriceIfReady();
      highlightSeatClass(classParam, type);
    })
    .catch(err => console.error(err));
}

function updateTotalFlightPriceIfReady() {
  const totalDisplay = document.getElementById('total-flight-price');
  if (!totalDisplay || window.departFlightPrice === null) return;

  // Retrieve searchData again to ensure it's the latest from sessionStorage
  const searchData = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');
  const adults = parseInt(searchData.adults) || 1;
  const children = parseInt(searchData.children) || 0;
  const passengerCount = adults + children;

  const ret = window.returnFlightPrice !== null ? window.returnFlightPrice : 0;
  const total = (window.departFlightPrice + ret) * passengerCount;
  totalDisplay.textContent = `RM ${total.toFixed(2)}`;

  // This is the place where initial total flight price is stored for passengerCheck.php to read
  searchData.totalPrice = total; // Ensure total price is updated in session storage
  sessionStorage.setItem('flightSearch', JSON.stringify(searchData)); // Save updated searchData
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


// Function to handle Book Now click
window.handleBookNowClick = function(event) {
  if (!userLoggedIn) {
    event.preventDefault(); // stop default action (like navigation)
    if (typeof window.showLoginReminder === 'function') {
      window.showLoginReminder(); // show your custom modal popup
    } else {
      alert('Please sign in or create an account before booking.');
    }
    return false;
  }
  return true;
};

const bookButton = document.querySelector('.book-now-btn');
const userLoggedIn = window.userLoggedIn !== undefined ? window.userLoggedIn : false;

// This function handles the login check (Duplicate, but kept for context. The one above is preferred)
window.handleBookNowClick = function (event) {
  if (!userLoggedIn) {
    event.preventDefault();
    alert('Please sign in or create an account before booking.');
    return false;
  }
  return true;
};

// Handle Book Now click
if (bookButton) {
  bookButton.addEventListener('click', function (e) {
    if (!window.handleBookNowClick(e)) return;

    const totalText = document.getElementById('total-flight-price')?.textContent || 'RM 0.00';
    const totalPrice = parseFloat(totalText.replace('RM', '').trim()).toFixed(2);

    // Retrieve the flightSearch object from sessionStorage (it's already the most updated)
    const flightSearch = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");

    // Update flightSearch object with final price and ensure all necessary data is present
    flightSearch.totalPrice = totalPrice; // Add the calculated total price

    // Store the updated flightSearch object back into sessionStorage
    sessionStorage.setItem("flightSearch", JSON.stringify(flightSearch));

    // Redirect to booking page without URL parameters
    window.location.href = 'passengerCheck.php';
  });
}
