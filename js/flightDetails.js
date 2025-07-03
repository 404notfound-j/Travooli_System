document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const departId = urlParams.get('depart');
  const returnId = urlParams.get('return');
  const oneWayId = urlParams.get('flightId'); // fallback for one-way
// Highlight user-selected seat class
  const selectedClass = sessionStorage.getItem("selectedSeatClass");
    if (selectedClass) {
      document.querySelectorAll('.seat-option').forEach(option => {
      if (option.dataset.class === selectedClass) {
        option.classList.add('highlighted'); 
      } else {
        option.classList.remove('highlighted');
      }
    });
  }
  const returnSection = document.getElementById('return-flight-section'); // wrap your return UI in this div

  if (departId && returnId) {
    loadFlightDetails(departId, 'depart');
    loadFlightDetails(returnId, 'return');
  } else if (oneWayId) {
    loadFlightDetails(oneWayId, 'depart');
    if (returnSection) returnSection.style.display = 'none'; // Hide return section if one-way
  } else {
    alert('No flight selected');
  }
});

function loadFlightDetails(flightId, type) {
  const urlParams = new URLSearchParams(window.location.search);
  const classParam =
    type === 'depart'
      ? urlParams.get('departClass') || urlParams.get('classId') || "PE"
      : urlParams.get('returnClass') || "PE";

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

      document.getElementById(`${type}-departure-time`).textContent = formattedDeparture;
      document.getElementById(`${type}-arrival-time`).textContent = formattedArrival;
      document.getElementById(`${type}-departure-airport-code`).textContent = flight.orig_airport_id;
      document.getElementById(`${type}-arrival-airport-code`).textContent = flight.dest_airport_id;
      document.getElementById(`${type}-flight-duration`).textContent = duration;

      const airlineHeader = document.getElementById("depart-flight-airline-plane");
      if (airlineHeader && type === 'depart') {
        airlineHeader.textContent = `${flight.airline_name || flight.airline_id}`;
      }

      const originInfoEl = document.getElementById(`${type}-origin-airport-info`);
      if (originInfoEl && type === 'depart') {
        originInfoEl.textContent = `${flight.origin_airport_full || ''}, ${flight.origin_airport_address || ''}`;
      }

      document.getElementById(`${type}-airline-name`).textContent = flight.airline_name;
      document.getElementById(`${type}-aircraft-type`).textContent = flight.flight_id;

      const priceEl = document.getElementById(`${type}-flight-price`);
      if (priceEl) priceEl.textContent = `RM ${parseFloat(flight.price).toFixed(2)}`;

      // ✅ Set airline logo image (for DEPART only)
      if (type === 'depart') {
        const airlineImage = document.getElementById("depart-airplane-image");
        if (airlineImage) {
          airlineImage.src = `images/${flight.airline_name}.jpg`; 
        }
      }
    })
    .catch(err => console.error(err));
}


// Utility functions
function formatTimeTo12Hour(timeString) {
  const [hourStr, minuteStr] = timeString.split(':');
  let hour = parseInt(hourStr);
  const minute = minuteStr;
  const ampm = hour >= 12 ? 'pm' : 'am';
  hour = hour % 12 || 12;
  return `${hour}:${minute} ${ampm}`;
}

function calculateDuration(dep, arr) {
  const [depH, depM] = dep.split(':').map(Number);
  const [arrH, arrM] = arr.split(':').map(Number);
  let depTotal = depH * 60 + depM;
  let arrTotal = arrH * 60 + arrM;
  if (arrTotal < depTotal) arrTotal += 1440; // +24h
  return arrTotal - depTotal;
}

function formatDuration(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${hours}h ${mins}m`;
}

// Like button toggle
const likeButton = document.querySelector('.like-button');
if (likeButton) {
  likeButton.addEventListener('click', () => {
    likeButton.classList.toggle('liked');
  });
}

const bookButton = document.querySelector('.book-now-btn');
if (bookButton) {
  bookButton.addEventListener('click', () => {
    const departPriceText = document.getElementById('depart-flight-price')?.textContent;
    const returnPriceText = document.getElementById('return-flight-price')?.textContent;

    // Extract numeric value (remove 'RM ')
    const departPrice = departPriceText ? parseFloat(departPriceText.replace('RM', '').trim()) : 0;
    const returnPrice = returnPriceText ? parseFloat(returnPriceText.replace('RM', '').trim()) : 0;

    const totalPrice = (departPrice + returnPrice).toFixed(2);

    const currentParams = new URLSearchParams(window.location.search);
    currentParams.set('price', totalPrice);
    if (currentParams.has('depart')) {
      // already have depart & return from round trip
    } else if (currentParams.has('flightId')) {
      // one-way flight
    }
    window.location.href = `passengerCheck.php?${currentParams.toString()}`;
  });
}
