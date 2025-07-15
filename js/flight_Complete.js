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

  // Check if feedback was already submitted for this booking
  const submitBtn = document.querySelector('.submit-btn');
  const bookingId = submitBtn ? submitBtn.getAttribute('data-booking-id') : null;
  const airlineId = submitBtn ? submitBtn.getAttribute('data-airline-id') : null;
  
  // Debug - log the values to console
  console.log('Initial data-booking-id:', bookingId);
  console.log('Initial data-airline-id:', airlineId);
  
  // Use localStorage to track feedback submissions
  const feedbackSubmitted = bookingId && localStorage.getItem('flight_feedback_submitted_' + bookingId) === 'true';
  
  // If feedback was already submitted, disable the form
  if (feedbackSubmitted) {
    const textarea = document.querySelector('.ratings textarea');
    if (textarea) {
      textarea.value = 'You have already submitted feedback for this flight.';
      textarea.disabled = true;
    }
    stars.forEach(s => {
      s.style.color = '#a8a8b7';
      s.style.pointerEvents = 'none';
    });
    if (submitBtn) {
      submitBtn.disabled = true;
    }
  }

  // Handle star hover
  stars.forEach((star, index) => {
    // Mouse enter - fill stars up to this one
    star.addEventListener('mouseenter', () => {
      if (feedbackSubmitted) return;
      for (let i = 0; i <= index; i++) {
        stars[i].style.color = '#605DEC';
      }
    });

    // Mouse leave - return to selected state
    star.addEventListener('mouseleave', () => {
      if (feedbackSubmitted) return;
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
      if (feedbackSubmitted) return;
      currentRating = index + 1;
      // Update all stars to reflect selection
      stars.forEach((s, i) => {
        s.style.color = i < currentRating ? '#605DEC' : '#a8a8b7';
        s.classList.toggle('selected', i < currentRating);
      });
    });
  });
  
  // Handle submit button
  if (submitBtn) {
    submitBtn.addEventListener('click', function() {
      if (feedbackSubmitted) {
        alert('You have already submitted feedback for this flight.');
        return;
      }
      
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
      const airlineId = this.getAttribute('data-airline-id');
      
      // Debug - log the values to console
      console.log('Submitting feedback with:');
      console.log('data-booking-id:', bookingId);
      console.log('data-airline-id:', airlineId);
      
      // Check for null or empty values
      if (!bookingId || bookingId === 'null' || bookingId === 'undefined') {
        alert('Error: Missing booking ID. Please try again or contact support.');
        return;
      }
      
      if (!airlineId || airlineId === 'null' || airlineId === 'undefined') {
        alert('Error: Missing airline ID. Please try again or contact support.');
        return;
      }
      
      // Submit the feedback using FormData
      const formData = new FormData();
      formData.append('action', 'submitFeedback');
      formData.append('f_book_id', bookingId);
      formData.append('airline_id', airlineId);
      formData.append('rating', currentRating);
      formData.append('feedback', reviewText);
      
      fetch('submit_flight_feedback.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Thank you for your review! It has been added to the flight details page.');
          
          // Mark as submitted in localStorage
          localStorage.setItem('flight_feedback_submitted_' + bookingId, 'true');
          
          // Disable the form
          const textarea = document.querySelector('.ratings textarea');
          if (textarea) {
            textarea.value = '';
            textarea.disabled = true;
          }
          stars.forEach(s => {
            s.style.color = '#a8a8b7';
            s.classList.remove('selected');
            s.style.pointerEvents = 'none';
          });
          submitBtn.disabled = true;
        } else {
          if (data.message && data.message.includes('already submitted')) {
            // If the user has already submitted feedback
            alert('You have already submitted feedback for this flight.');
            
            // Mark as submitted in localStorage
            localStorage.setItem('flight_feedback_submitted_' + bookingId, 'true');
            
            // Disable the form
            const textarea = document.querySelector('.ratings textarea');
            if (textarea) {
              textarea.value = '';
              textarea.disabled = true;
            }
            stars.forEach(s => {
              s.style.color = '#a8a8b7';
              s.style.pointerEvents = 'none';
            });
            submitBtn.disabled = true;
          } else {
            // For other errors
            alert('Error: ' + data.message);
          }
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
      if (feedbackSubmitted) {
        alert('You have already submitted feedback for this flight.');
        return;
      }
      
      // Reset all stars
      stars.forEach(s => {
        s.style.color = '#a8a8b7';
        s.classList.remove('selected');
      });
      document.querySelector('textarea').value = '';
      currentRating = 0;
    });
  }
  
  // Handle back to homepage button
  const homeBtn = document.querySelector('.home-btn');
  if (homeBtn) {
    homeBtn.addEventListener('click', function() {
      window.location.href = 'U_dashboard.php';
    });
  }
  
  // Make showCancelConfirmation available globally as it's called by onclick in PHP
  window.showCancelConfirmation = function() {
    // Create an iframe for the confirmation popup
    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.top = '0';
    iframe.style.left = '0';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.zIndex = '10000';

    // Get booking ID from the cancel button or data attribute
    const cancelBtn = document.querySelector('.cancel-flight-btn');
    const bookingId = cancelBtn ? cancelBtn.getAttribute('data-booking-id') : null;

    if (!bookingId) {
      alert('Cannot find booking ID. Please try again or contact support.');
      return;
    }

    // Set the source URL with parameters
    iframe.src = 'confirm_popup.php?title=' + encodeURIComponent('Are you sure to cancel your flight?') +
                '&description=' + encodeURIComponent('If you cancel your flight, you may be subject to cancellation fees depending on the airline\'s policy. Please check the cancellation policy for details.') +
                '&confirmText=' + encodeURIComponent('Cancel Flight') +
                '&confirmClass=btn-danger' +
                '&actionType=cancelFlight';

    // Add to document
    document.body.appendChild(iframe);

    // Listen for messages from the iframe
    window.addEventListener('message', function eventHandler(event) {
      if (event.data === 'closeModal') {
        // Remove the iframe when closed
        document.body.removeChild(iframe);
        window.removeEventListener('message', eventHandler);
      } else if (event.data && event.data.action === 'cancelFlight' && event.data.confirmed) {
        // Show loading state on button
        const cancelBtn = document.querySelector('.cancel-flight-btn');
        const originalText = cancelBtn.textContent;
        cancelBtn.textContent = 'Processing...';
        cancelBtn.disabled = true;
        
        // AJAX to cancel flight with proper JSON and headers
        fetch('cancelFlight.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            booking_id: bookingId
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Your flight booking has been cancelled successfully.');
            if (data.redirect_url) {
              window.location.href = data.redirect_url;
            } else {
              window.location.href = 'U_dashboard.php';
            }
          } else {
            alert('Error: ' + data.message);
            // Reset button state
            cancelBtn.textContent = originalText;
            cancelBtn.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Network error, please try again.');
          // Reset button state
          cancelBtn.textContent = originalText;
          cancelBtn.disabled = false;
        });
        
        // Remove the iframe
        document.body.removeChild(iframe);
        window.removeEventListener('message', eventHandler);
      }
    });
  };
});