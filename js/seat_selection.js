document.addEventListener('DOMContentLoaded', () => {
  // Retrieve the entire flightSearch object from sessionStorage
  const flightSearch = JSON.parse(sessionStorage.getItem('flightSearch') || '{}');

  // Extract necessary data from the flightSearch object
  // For seat selection, we generally use the depart flight ID as the primary flight for seats
  const flightId = flightSearch.depart || flightSearch.selectedFlight || '';
  const classId = flightSearch.classId || '';
  
  // Use the *calculated* prices and passenger counts from the previous page
  const ticketPrice = parseFloat(flightSearch.ticketPrice || 0);
  const baggagePrice = parseFloat(flightSearch.baggagePrice || 0);
  const mealPrice = parseFloat(flightSearch.mealPrice || 0);
  const taxPrice = parseFloat(flightSearch.taxPrice || 0);
  const totalPrice = parseFloat(flightSearch.finalTotalPrice || 0);

  // Use the *active* passenger counts from the previous page's selection
  const activeAdults = parseInt(flightSearch.activeAdults) || 1;
  const activeChildren = parseInt(flightSearch.activeChildren) || 0;
  const maxSeats = activeAdults + activeChildren; // Total number of seats to be selected

  console.log('Prices from sessionStorage:', {ticketPrice, baggagePrice, mealPrice, taxPrice, totalPrice});

  // Update Price Details card with .toFixed(2)
  document.getElementById('flight-price').textContent = `RM ${ticketPrice.toFixed(2)}`;
  document.getElementById('baggage-price').textContent = `RM ${baggagePrice.toFixed(2)}`;
  document.getElementById('meal-price').textContent = `RM ${mealPrice.toFixed(2)}`;
  document.getElementById('tax-amount').textContent = `RM ${taxPrice.toFixed(2)}`;
  document.getElementById('total').textContent = `RM ${totalPrice.toFixed(2)}`;
  

  // Update Ticket Count Label
  const ticketCountLabel = document.getElementById('ticket-count-label');
  if (ticketCountLabel) {
      ticketCountLabel.textContent = `Tickets (${activeAdults} Adult${activeAdults > 1 ? 's' : ''}${activeChildren > 0 ? ', ' : ''}${activeChildren > 0 ? `${activeChildren} Child${activeChildren > 1 ? 'ren' : ''}` : ''})`;
  }

  const classMap = {
    EC: 'Economy Class',
    PE: 'Premium Economy',
    BC: 'Business Class',
    FC: 'First Class'
  };

  const readableClass = classMap[classId] || 'Unknown Class';
  const classLabel = document.getElementById('class-name');
  if (classLabel) classLabel.textContent = readableClass;

  // --- Function to fetch and display seat availability ---
  async function fetchAndDisplaySeats() {
      if (!flightId) {
          console.warn('Flight ID not found in sessionStorage. Cannot fetch seat availability.');
          return;
      }
      // Ensure classId is passed as well
      if (!classId) {
          console.warn('Class ID not found in sessionStorage. Cannot fetch seat availability precisely.');
      }

      try {
          // MODIFIED FETCH URL: Pass classId
          const response = await fetch(`getSeatAvailability.php?flightId=${flightId}&classId=${classId}`);
          if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
          }
          const seatData = await response.json();
          console.log('Fetched seat data:', seatData);

          if (seatData.error) {
              console.error('Server error fetching seats:', seatData.error);
              alert('Error loading seat availability: ' + seatData.error);
              return;
          }

          // Update seat grid based on fetched data
          seatData.forEach(dbSeat => {
              const seatElement = document.getElementById(dbSeat.seat_number);
              if (seatElement) {
                  seatElement.classList.remove('available', 'selected', 'unavailable'); // Clear existing states
                  if (dbSeat.status === 'booked') { // Status is now 'booked' or 'available'
                      seatElement.classList.add('unavailable'); // Assuming 'unavailable' for booked seats
                      // If a booked seat was somehow selected by JS (unlikely with this flow), deselect it
                      if (seatElement.classList.contains('selected')) {
                          seatElement.classList.remove('selected');
                          sessionStorage.setItem('selectedSeats', JSON.stringify([])); // Clear selected if pre-selected becomes unavailable
                      }
                  } else { // status === 'available'
                      seatElement.classList.add('available');
                  }
              }
          });

          // After updating availability, re-apply any previously selected seats from sessionStorage
          const previouslySelectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
          previouslySelectedSeats.forEach(seatNum => {
              const seatElement = document.getElementById(seatNum);
              if (seatElement && seatElement.classList.contains('available')) { // Only select if still available
                  seatElement.classList.add('selected');
              }
          });
          updateSeatSelectionStatus(); // Update status display after seats are loaded/re-selected

      } catch (error) {
          console.error('Error fetching seat availability:', error);
          alert('Could not load seat availability. Please try again.');
      }
  }
  // --- END NEW FUNCTION ---


  const seats = document.querySelectorAll('.seat');
  seats.forEach(seat => {
    seat.addEventListener('click', () => {
      const selectedSeats = document.querySelectorAll('.seat.selected').length;
      
      if (seat.classList.contains('selected')) {
        seat.classList.remove('selected');
      } else if (seat.classList.contains('available')) { // Only allow selection if the seat is 'available' from DB
        if (selectedSeats < maxSeats) {
          seat.classList.add('selected');
        } else {
          alert(`You can only select up to ${maxSeats} seat(s).`);
        }
      }

      const selectedNumbers = Array.from(document.querySelectorAll('.seat.selected'))
        .map(s => s.dataset.seatNumber);
      sessionStorage.setItem('selectedSeats', JSON.stringify(selectedNumbers));

      updateSeatSelectionStatus();
    });
  });

  function updateSeatSelectionStatus() {
    const selectedNumbers = Array.from(document.querySelectorAll('.seat.selected'))
      .map(s => s.dataset.seatNumber);

    const seatCountEl = document.getElementById('seat-count');
    if (seatCountEl) seatCountEl.textContent = `${selectedNumbers.length} of ${maxSeats} seats selected`;

    const seatListEl = document.getElementById('seat-list');
    if (seatListEl) seatListEl.textContent = selectedNumbers.join(', ') || 'N/A';

    const proceedBtn = document.getElementById('proceed-btn');
    if (proceedBtn) {
      proceedBtn.disabled = selectedNumbers.length !== maxSeats;
      if (proceedBtn.disabled) {
        proceedBtn.classList.add('disabled');
      } else {
        proceedBtn.classList.remove('disabled');
      }

      proceedBtn.addEventListener('click', function () {
        // Ensure all required seats are selected before proceeding
        sessionStorage.setItem('selectedSeats', JSON.stringify(selectedNumbers));
        window.location.href = 'payment.php';
      });
    }
  }

  // Initial calls when the page loads
  updateSeatSelectionStatus(); // Initialize status display
  fetchAndDisplaySeats(); // Fetch seat availability from the database
});