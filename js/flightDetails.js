document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const departId = urlParams.get('depart');
  const returnId = urlParams.get('return');
  const oneWayId = urlParams.get('flightId');
  const returnSection = document.getElementById('return-flight-section');

  window.departFlightPrice = null;
  window.returnFlightPrice = null;

  const selectedClass =
    urlParams.get('departClass') ||
    urlParams.get('returnClass') ||
    urlParams.get('classId') ||
    sessionStorage.getItem("selectedSeatClass") ||
    "PE";

  highlightSeatClass(selectedClass, 'depart');

  if (departId && returnId) {
    loadFlightDetails(departId, 'depart');
    loadFlightDetails(returnId, 'return');
  } else if (oneWayId) {
    loadFlightDetails(oneWayId, 'depart');
    if (returnSection) returnSection.style.display = 'none';
  } else {
    alert('No flight selected');
  }

  // REMOVE ALL CLICK LISTENERS from .seat-option
  document.querySelectorAll('.seat-option').forEach(btn => {
    const newBtn = btn.cloneNode(true); // clone without any listeners
    btn.replaceWith(newBtn);            // replace original with clone
  });
});

function highlightSeatClass(classCode, type = 'depart') {
  document.querySelectorAll(`#${type}-seat-classes .seat-option`).forEach(option => {
    option.classList.toggle('highlighted', option.dataset.class === classCode);
  });
}

function loadFlightDetails(flightId, type) {
  const urlParams = new URLSearchParams(window.location.search);
  let classParam;

  if (type === 'depart') {
    classParam = urlParams.get('departClass') || urlParams.get('classId') || sessionStorage.getItem("selectedDepartClass") || "PE";
    sessionStorage.setItem("selectedDepartClass", classParam);
  } else {
    classParam = urlParams.get('returnClass') || sessionStorage.getItem("selectedReturnClass") || "PE";
    sessionStorage.setItem("selectedReturnClass", classParam);
  }

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
      } else {
        const logoImage = document.getElementById("return-airline-logo");
        if (logoImage) {
          logoImage.src = `images/${flight.airline_id}.png`;
          logoImage.alt = `${flight.airline_name} Logo`;
        }
        window.returnFlightPrice = price;
      }

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

// Check login status from PHP
const userLoggedIn = window.userLoggedIn !== undefined ? window.userLoggedIn : false;

if (userLoggedIn) {
  localStorage.setItem('user_logged_in', 'true');
} else {
  localStorage.removeItem('user_logged_in');
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

if (bookButton) {
  bookButton.addEventListener('click', function(e) {
    // Check if user is logged in
   

    // Proceed with booking
    const totalText = document.getElementById('total-flight-price')?.textContent || 'RM 0.00';
    const totalPrice = parseFloat(totalText.replace('RM', '').trim()).toFixed(2);

    const urlParams = new URLSearchParams(window.location.search);
    const flightPrice = (window.departFlightPrice || 0) + (window.returnFlightPrice || 0);
    urlParams.set('price', totalPrice);

    const flightSearch = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");

    const adults = flightSearch.adults || '1';
    const children = flightSearch.children || '0';

    sessionStorage.setItem("selectedAdults", adults);
    sessionStorage.setItem("selectedChildren", children);

    const searchData = {
      selectedFlight: urlParams.get('flightId') || urlParams.get('depart'),
      returnFlight: urlParams.get('return') || null,
      seatClass: sessionStorage.getItem("selectedSeatClass") || "PE",
      departDate: urlParams.get('departDate') || '',
      returnDate: urlParams.get('returnDate') || '',
      from: urlParams.get('from') || '',
      to: urlParams.get('to') || '',
      adults: adults,
      children: children,
      trip: urlParams.get('return') ? 'round' : 'one',
      flightPrice: flightPrice
    };

    sessionStorage.setItem("flightSearch", JSON.stringify(searchData));

    // Redirect to passenger page with params
    window.location.href = `passengerCheck.php?${urlParams.toString()}`;
  });
}
