  // Get query parameters from previous page
  const urlParams = new URLSearchParams(window.location.search);
  const from = urlParams.get('from') || 'From where?';
  const to = urlParams.get('to') || 'Where to?';
  const passenger = urlParams.get('passenger') || '1 adult';

  // Helper function to format time from minutes past midnight
  function formatTime(value) {
    const hours = Math.floor(value / 60);
    const minutes = value % 60;
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 === 0 ? 12 : hours % 12;
    return `${displayHours}:${minutes.toString().padStart(2, '0')}${ampm}`;
  }

  // Helper function to calculate duration
  function calculateDuration(departure, arrival) {
    const [depH, depM] = departure.split(':').map(Number);
    const [arrH, arrM] = arrival.split(':').map(Number);

    let depMinutes = depH * 60 + depM;
    let arrMinutes = arrH * 60 + arrM;

    if (arrMinutes < depMinutes) {
      arrMinutes += 24 * 60;  // overnight flight handling
    }

    const durationMinutes = arrMinutes - depMinutes;
    const hours = Math.floor(durationMinutes / 60);
    const minutes = durationMinutes % 60;

    return `${hours}h ${minutes}m`;
  }

  // Event listener for the time range slider
  const range = document.getElementById('timeRange');
  const startTime = document.getElementById('startTime'); // Corrected variable name
  if (range && startTime) {
    startTime.textContent = formatTime(parseInt(range.value)); // Set initial value
    range.addEventListener('input', () => {
      startTime.textContent = formatTime(parseInt(range.value));
    });
  }

  // Event listener for filter buttons (Seat class, Rating)
  document.querySelectorAll('.filter-button, .rating-buttons button').forEach(btn => {
    btn.addEventListener('click', () => {
      const parentGroup = btn.closest('.filter-group');
      if (parentGroup) {
        // Check if the clicked button is already selected
        if (btn.classList.contains('selected')) {
          // If already selected, remove the selected class
          btn.classList.remove('selected');
        } else {
          // If not selected, remove 'selected' from all buttons in the same group
          parentGroup.querySelectorAll('button').forEach(b => b.classList.remove('selected'));
          // Add 'selected' to the clicked button
          btn.classList.add('selected');
        }
      }
    });
  });

  // Make sort options clickable and highlight selected
  document.querySelectorAll('.sort-option').forEach(option => {
      option.addEventListener('click', () => {
          // Remove 'selected' class from all sort options
          document.querySelectorAll('.sort-option').forEach(opt => {
              opt.classList.remove('selected');
          });
          // Add 'selected' class to the clicked option
          option.classList.add('selected');
      });
  });

// Function to fetch and render flights
function fetchAndRenderFlights() {
  const seatClassMap = {
    "Economy": "EC",
    "Premium Economy": "PE",
    "Business Class": "BC",
    "First Class": "FC"
  };

  // Get selected seat class
  let selectedClass = "";
  document.querySelectorAll(".filter-button").forEach(btn => {
    if (btn.classList.contains("selected")) {
      selectedClass = seatClassMap[btn.innerText.trim()];
    }
  });

  // Get time range (convert minutes to HH:MM:SS)
  let minutes = parseInt(document.getElementById("timeRange").value);
  let hours = Math.floor(minutes / 60);
  let mins = minutes % 60;
  let timeFrom = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:00`;
  let timeTo = "23:00:00"; // default end time for slider

  // Prepare the POST request
  const formData = new FormData();
  formData.append("seatClass", selectedClass);
  formData.append("timeFrom", timeFrom);
  formData.append("timeTo", timeTo);
  formData.append("date", "2025-06-05"); // add more fields as needed

  //get data from database
  fetch("getflight.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    console.log(data);
    renderFlights(data);  // This was missing!
    // Ensure 'Best' is selected after rendering flights
    document.querySelectorAll('.sort-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    const bestOption = document.querySelector('.sort-option:nth-child(2)');
    if (bestOption) {
        bestOption.classList.add('selected');
    }
  })
  .catch(err => console.error("Error fetching flights:", err));
}

// Call fetchAndRenderFlights on page load
document.addEventListener('DOMContentLoaded', () => {
  fetchAndRenderFlights();
});

//read selected seat class
document.querySelector(".apply-btn").addEventListener("click", function () {
  fetchAndRenderFlights(); // Call the function when apply button is clicked
});

function renderFlights(flights) {
  const container = document.getElementById('flightResults');
  if (!container) {
    console.error("Container #flightResults not found!");
    return;
  }

  container.innerHTML = ''; // Clear previous content

//create container box for flight result 
  flights.forEach(flight => {
    const duration = calculateDuration(flight.departure_time, flight.arrival_time);
    const flightBox = `
      <div class="flight-card">
        <div class="airline-section">
          <img src="https://upload.wikimedia.org/wikipedia/commons/c/ce/Emirates_logo.svg" alt="${flight.airline_id} Logo" class="airline-logo">
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
              <span class="price">$${flight.price || 'N/A'}</span>
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

  //cauculate time flight
  function formatTime(timeString) {
  if (!timeString) return 'N/A';
  const [hour, minute] = timeString.split(':');
  const h = parseInt(hour);
  const ampm = h >= 12 ? 'pm' : 'am';
  const hour12 = h % 12 === 0 ? 12 : h % 12;
  return `${hour12}:${minute} ${ampm}`;
}

//cauculate duration
  function calculateDuration(departure, arrival) {
    const [depH, depM] = departure.split(':').map(Number);
    const [arrH, arrM] = arrival.split(':').map(Number);

    let depMinutes = depH * 60 + depM;
    let arrMinutes = arrH * 60 + arrM;

    if (arrMinutes < depMinutes) {
      arrMinutes += 24 * 60;  // overnight flight handling
    }

    const durationMinutes = arrMinutes - depMinutes;
    const hours = Math.floor(durationMinutes / 60);
    const minutes = durationMinutes % 60;

    return `${hours}h ${minutes}m`;
  }

//change heart color 
document.getElementById('flightResults').addEventListener('click', (event) => {
  const heartBtn = event.target.closest('.heart-btn');
  if (heartBtn) {
    heartBtn.classList.toggle('favorited');
  }

  const viewBtn = event.target.closest('.view-details-btn');
  if (viewBtn) {
    // Get the flight ID from the data attribute
    const flightId = viewBtn.dataset.flightId;
    // Redirect to flightDetails.php with the flight ID in the URL
    window.location.href = `flightDetails.php?flightId=${flightId}`;
  }
});
  
  function formatDuration(minutes) {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return `${h}h ${m}m`;
  }
}