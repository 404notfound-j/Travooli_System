document.addEventListener('DOMContentLoaded', function() {
  // Retrieve flight details and selected seats from sessionStorage
  const lastFlightDetails = JSON.parse(sessionStorage.getItem('lastBookingDetails') || '{}'); 
  const lastSelectedSeats = JSON.parse(sessionStorage.getItem('lastSelectedSeats') || '[]'); 
  const lastTotalAmount = parseFloat(sessionStorage.getItem('lastTotalAmount') || 0);

  // Extract prices and counts from lastFlightDetails (which is the flightSearch object)
  const ticket = parseFloat(lastFlightDetails.ticketPrice || 0); 
  const baggage = parseFloat(lastFlightDetails.baggagePrice || 0); 
  const meal = parseFloat(lastFlightDetails.mealPrice || 0); 
  const taxPrice = parseFloat(lastFlightDetails.taxPrice || 0); 
  const finalTotalPrice = parseFloat(lastFlightDetails.finalTotalPrice || lastTotalAmount || 0); 
  const activeAdults = parseInt(lastFlightDetails.activeAdults) || 0; 
  const activeChildren = parseInt(lastFlightDetails.activeChildren) || 0; 


  // --- Populate Seat Numbers on Boarding Pass ---
  // The seat ID on the HTML is "final-seat-list-0" for the first boarding pass (index 0 in PHP loop)
  // Assuming only one boarding pass is displayed for now.
  const seatDisplayElement = document.getElementById('final-seat-list-0');
  if (seatDisplayElement) {
      if (Array.isArray(lastSelectedSeats) && lastSelectedSeats.length > 0) {
          seatDisplayElement.textContent = lastSelectedSeats.join(', ');
      } else if (typeof lastSelectedSeats === 'object' && lastSelectedSeats !== null && (lastSelectedSeats.depart || lastSelectedSeats.return)) {
          // Handles case where selectedSeats might be {depart: [...], return: [...]}
          const combinedSeats = [...(lastSelectedSeats.depart || []), ...(lastSelectedSeats.return || [])];
          seatDisplayElement.textContent = combinedSeats.length > 0 ? combinedSeats.join(', ') : 'N/A';
      } else {
          seatDisplayElement.textContent = 'N/A';
      }
  }


  // --- Ratings Section Logic ---
  const stars = document.querySelectorAll('.stars i');
  let currentRating = 0;

  // Handle star hover
  stars.forEach((star, index) => {
    // Mouse enter - fill stars up to this one
    star.addEventListener('mouseenter', () => {
      for (let i = 0; i <= index; i++) {
        stars[i].style.color = '#605DEC';
      }
    });

    // Mouse leave - return to selected state
    star.addEventListener('mouseleave', () => {
      if (currentRating === 0) {
        // If no rating selected, reset all stars
        stars.forEach(s => s.style.color = '#a8a8b7');
      } else {
        // If rating selected, show selected stars
        stars.forEach((s, i) => {
          s.style.color = i < currentRating ? '#605DEC' : '#a8a8b7';
        });
      }
    });

    // Click - set rating
    star.addEventListener('click', () => {
      currentRating = index + 1;
      // Update all stars to reflect selection
      stars.forEach((s, i) => {
        s.style.color = i < currentRating ? '#605DEC' : '#a8a8b7';
        s.classList.toggle('selected', i < currentRating);
      });
    });
  });
  
  // Handle submit button
  const submitBtn = document.querySelector('.submit-btn');
  if (submitBtn) {
    submitBtn.addEventListener('click', function() {
      const reviewText = document.querySelector('textarea').value.trim();
      
      if (currentRating === 0) {
        alert('Please select a rating before submitting.');
        return;
      }
      
      if (reviewText === '') {
        alert('Please enter your review before submitting.');
        return;
      }
      
      // Get data attributes from button
      const bookingId = this.getAttribute('data-booking-id');
      const flightId = this.getAttribute('data-flight-id');
      const airlineId = this.getAttribute('data-airline-id');
      
      // Submit the feedback using fetch API
      fetch('submit_flight_feedback.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=submitFeedback&f_book_id=${encodeURIComponent(bookingId)}&flight_id=${encodeURIComponent(flightId)}&airline_id=${encodeURIComponent(airlineId)}&rating=${currentRating}&feedback=${encodeURIComponent(reviewText)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Thank you for your review! It has been added to the flight details page.');
          // Reset form
          stars.forEach(s => {
            s.style.color = '#a8a8b7';
            s.classList.remove('selected');
          });
          document.querySelector('textarea').value = '';
          currentRating = 0;
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('There was an error submitting your review. Please try again.');
      });
    });
  }
  
  // Handle cancel button
  const cancelBtn = document.querySelector('.cancel-btn');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function() {
      // Reset all stars
      stars.forEach(s => {
        s.style.color = '#a8a8b7';
        s.classList.remove('selected');
      });
      document.querySelector('textarea').value = '';
      currentRating = 0;
    });
  }
  
  // Make showCancelConfirmation available globally as it's called by onclick in PHP
  window.showCancelConfirmation = function() {
    // Get booking ID from the cancel button or submit button
    const cancelBtn = document.querySelector('.cancel-flight-btn');
    const submitBtn = document.querySelector('.submit-btn');
    
    let bookingId = null;
    
    // Try to get booking ID from various sources
    if (cancelBtn && cancelBtn.getAttribute('data-booking-id')) {
      bookingId = cancelBtn.getAttribute('data-booking-id');
      console.log("Got booking ID from cancel button:", bookingId);
    } else if (submitBtn && submitBtn.getAttribute('data-booking-id')) {
      bookingId = submitBtn.getAttribute('data-booking-id');
      console.log("Got booking ID from submit button:", bookingId);
    } else {
      // Try to get from URL
      const urlParams = new URLSearchParams(window.location.search);
      bookingId = urlParams.get('bookingId');
      console.log("Got booking ID from URL:", bookingId);
    }zz
    
    if (!bookingId || bookingId === 'N/A') {
      alert("Cannot find booking ID. Please try again or contact support.");
      return;
    }
    
    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.top = '0';
    iframe.style.left = '0';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.zIndex = '10000';

    iframe.src = 'confirm_popup.php?title=' + encodeURIComponent('Are you sure to cancel your flight?') +
                   '&description=' + encodeURIComponent('If you cancel your flight, you may be subject to cancellation fees depending on the airline\'s policy. Please check the cancellation policy for details.') +
                   '&confirmText=' + encodeURIComponent('Cancel Flight') +
                   '&confirmClass=btn-danger' +
                   '&actionType=cancelFlight' +
                   '&bookingId=' + encodeURIComponent(bookingId);

    document.body.appendChild(iframe);

    window.addEventListener('message', function handler(event) {
      if (event.data === 'closeModal') {
        document.body.removeChild(iframe);
        window.removeEventListener('message', handler);
      } else if (event.data && event.data.action === 'cancelFlight' && event.data.confirmed) {
        console.log("Sending cancellation request with booking ID:", bookingId);
        
        // Use the booking ID from the event if available, otherwise use the one we found
        const finalBookingId = event.data.bookingId || bookingId;
        
        fetch('cancelFlight.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ 
            action: 'cancel',
            booking_id: finalBookingId 
          })
        })
        .then(response => response.json())
        .then(data => {
          console.log("Cancellation response:", data);
          
          if (data.success) {
            alert(data.message || "Your booking has been successfully cancelled and refund processed.");
            window.location.href = 'U_dashboard.php';
          } else {
            alert("Error: " + (data.error || "An error occurred while cancelling the flight."));
            console.error("Cancellation error:", data);
          }
        })
        .catch(error => {
          console.error('Cancellation failed:', error);
          alert("An error occurred while cancelling the flight.");
        });

        document.body.removeChild(iframe);
        window.removeEventListener('message', handler);
      }
    });
  };
});