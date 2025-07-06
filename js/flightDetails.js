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

document.querySelectorAll('.seat-option').forEach(btn => {
  btn.addEventListener('click', function () {
    const selectedClass = this.dataset.class;
    sessionStorage.setItem("selectedSeatClass", selectedClass);
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('depart') && urlParams.has('return')) {
      const isDepart = document.getElementById('depart-airplane-image')?.contains(this);
      if (isDepart) {
        urlParams.set('departClass', selectedClass);
      } else {
        urlParams.set('returnClass', selectedClass);
      }
    } else {
      urlParams.set('classId', selectedClass);
    }

    window.location.search = urlParams.toString();
  });
});

const likeButton = document.querySelector('.like-button');
if (likeButton) {
  likeButton.addEventListener('click', () => {
    likeButton.classList.toggle('liked');
  });
}

const bookButton = document.querySelector('.book-now-btn');
if (bookButton) {
  bookButton.addEventListener('click', () => {
    const totalText = document.getElementById('total-flight-price')?.textContent || 'RM 0.00';
    const totalPrice = parseFloat(totalText.replace('RM', '').trim()).toFixed(2);

    const urlParams = new URLSearchParams(window.location.search);
    const flightPrice = (window.departFlightPrice || 0) + (window.returnFlightPrice || 0);
    urlParams.set('price', totalPrice);

    // ✅ Get actual selected values from existing flightSearch
    const flightSearch = JSON.parse(sessionStorage.getItem("flightSearch") || "{}");

    const adults = flightSearch.adults || '1';
    const children = flightSearch.children || '0';

    // ✅ Save correctly
    sessionStorage.setItem("selectedAdults", adults);
    sessionStorage.setItem("selectedChildren", children);

    // ✅ Save searchData with correct values
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
    window.location.href = `passengerCheck.php?${urlParams.toString()}`;
  });
}
