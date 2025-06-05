document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const flightId = urlParams.get('flightId');
    if (!flightId) {
      alert('No flight selected');
      return;
    }
    
    fetch(`getFlightDetails.php?flightId=${flightId}`)
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
        // Now fill your HTML elements with flight data
        document.getElementById('flight-airline-plane').textContent = `${flight.airline_id}`;
        document.getElementById('departure-time').textContent = formattedDeparture;
        document.getElementById('departure-airport-code').textContent = flight.orig_airport_id;
        document.getElementById('arrival-time').textContent = formattedArrival;
        document.getElementById('arrival-airport-code').textContent = flight.dest_airport_id;
        document.getElementById('flight-duration').textContent = duration;
        document.getElementById('origin-airport-info').textContent =`${flight['origin_airport_full']}` + `${flight['origin_airport_address']}`;
        document.getElementById('airline-name').textContent = `${flight.airline_id}`;
        document.getElementById('aircraft-type').textContent = `${flight.flight_id}`;

      })
      .catch(err => console.error(err));
  });
  
  const likeButton = document.querySelector('.like-button');
  if (likeButton) {
    likeButton.addEventListener('click', () => {
      likeButton.classList.toggle('liked');
    });
  }

  function formatTimeTo12Hour(timeString) {
    const [hourStr, minuteStr] = timeString.split(':');
    let hour = parseInt(hourStr);
    const minute = minuteStr;
    const ampm = hour >= 12 ? 'pm' : 'am';
    hour = hour % 12 || 12; // convert 0 to 12 for 12 AM
    return `${hour}:${minute} ${ampm}`;
  }
  
  function calculateDuration(dep, arr) {
    const [depH, depM] = dep.split(':').map(Number);
    const [arrH, arrM] = arr.split(':').map(Number);
  
    let depTotal = depH * 60 + depM;
    let arrTotal = arrH * 60 + arrM;
  
    if (arrTotal < depTotal) arrTotal += 24 * 60; // handle overnight flights
  
    return arrTotal - depTotal; // in minutes
  }
  
  function formatDuration(minutes) {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours}h ${mins}m`;
  }
  