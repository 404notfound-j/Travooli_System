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
  
  stars.forEach((star, index) => {
    star.addEventListener('mouseenter', () => {
      for (let i = 0; i <= index; i++) {
        stars[i].style.color = '#605DEC';
      }
    });
    
    star.addEventListener('mouseleave', () => {
      if (currentRating === 0) {
        stars.forEach(s => s.style.color = '#a8a8b7');
      } else {
        stars.forEach((s, i) => {
          s.style.color = i < currentRating ? '#605DEC' : '#a8a8b7';
        });
      }
    });
    
    star.addEventListener('click', () => {
      currentRating = index + 1;
      stars.forEach((s, i) => {
        s.style.color = i < currentRating ? '#605DEC' : '#a8a8b7';
        s.classList.toggle('selected', i < currentRating);
      });
    });
  });
  
  document.querySelector('.submit-btn').addEventListener('click', function() {
    const reviewText = document.querySelector('textarea').value.trim();
    const userName = "James Doe"; // Get from user session - consider passing from PHP if available
    
    if (currentRating === 0) {
      alert('Please select a rating before submitting.');
      return;
    }
    
    if (reviewText === '') {
      alert('Please enter your review before submitting.');
      return;
    }
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_review.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
      if (this.status === 200) {
        try {
          const response = JSON.parse(this.responseText);
          if (response.success) {
            alert('Thank you for your review! It has been added to the flight details page.');
            stars.forEach(s => {
              s.style.color = '#a8a8b7';
              s.classList.remove('selected');
            });
            document.querySelector('textarea').value = '';
            currentRating = 0;
          } else {
            alert('Error: ' + response.message);
          }
        } catch (e) {
          alert('Thank you for your review! It has been added to the flight details page.');
          stars.forEach(s => {
            s.style.color = '#a8a8b7';
            s.classList.remove('selected');
          });
          document.querySelector('textarea').value = '';
          currentRating = 0;
        }
      } else {
        alert('There was an error submitting your review. Please try again.');
      }
    };
    
    xhr.onerror = function() {
      alert('There was an error submitting your review. Please try again.');
    };
    
    const data = `rating=${currentRating}&review=${encodeURIComponent(reviewText)}&user=${encodeURIComponent(userName)}&type=flight`;
    xhr.send(data);
  });
  
  document.querySelector('.cancel-btn').addEventListener('click', function() {
    stars.forEach(s => {
      s.style.color = '#a8a8b7';
      s.classList.remove('selected');
    });
    document.querySelector('textarea').value = '';
    currentRating = 0;
  });
  
  // Make showCancelConfirmation available globally as it's called by onclick in PHP
  window.showCancelConfirmation = function() {
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
                   '&actionType=cancelFlight';

    document.body.appendChild(iframe);

    window.addEventListener('message', function handler(event) {
      if (event.data === 'closeModal') {
        document.body.removeChild(iframe);
        window.removeEventListener('message', handler);
      } else if (event.data && event.data.action === 'cancelFlight' && event.data.confirmed) {
        fetch('cancelFlight.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ action: 'cancel' })
        })
        .then(response => response.text())
        .then(message => {
          alert(message);
          window.location.href = 'U_dashboard.php';
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