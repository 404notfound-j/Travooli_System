    document.addEventListener('DOMContentLoaded', function() {
      //Price Breakdown
      const total = sessionStorage.getItem('total_price') || '0';
      const ticket = sessionStorage.getItem('ticket_price') || '0';
      const baggage = sessionStorage.getItem('baggage_price') || '0';
      const meal = sessionStorage.getItem('meal_price') || '0';
      const label1 = sessionStorage.getItem('ticket_label') || 'Tickets';
      const adults = parseInt(sessionStorage.getItem('adultCount')) || 1;
      const children = parseInt(sessionStorage.getItem('childCount')) || 0;
    
      // Update the prices
      document.getElementById('flight-price').textContent = `RM ${ticket}`;
      document.querySelector('.meal-price').textContent = `RM ${meal}`;
      document.querySelector('.baggage-price').textContent = `RM ${baggage}`;
      document.getElementById('total').textContent = `RM ${total}`;
      document.getElementById('ticket-count-label').textContent = label1;
      // Generate dynamic passenger label
      let label = 'Tickets (';
      if (adults > 0) label += `${adults} Adult${adults > 1 ? 's' : ''}`;
      if (children > 0) {
        label += adults > 0 ? ', ' : '';
        label += `${children} Child${children > 1 ? 'ren' : ''}`;
      }
      label += ')';
      document.getElementById('ticket-count-label').textContent = label;


      const stars = document.querySelectorAll('.stars i');
      let currentRating = 0;
      
      // Handle star hover and click
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
      document.querySelector('.submit-btn').addEventListener('click', function() {
        const reviewText = document.querySelector('textarea').value.trim();
        const userName = "James Doe"; // Get from user session
        
        if (currentRating === 0) {
          alert('Please select a rating before submitting.');
          return;
        }
        
        if (reviewText === '') {
          alert('Please enter your review before submitting.');
          return;
        }
        
        // Save the review using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_review.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
          if (this.status === 200) {
            try {
              const response = JSON.parse(this.responseText);
              if (response.success) {
                alert('Thank you for your review! It has been added to the flight details page.');
                // Reset form
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
              // Reset form even if there's an error parsing the response
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
        
        // Send the data - specify this is a flight review
        const data = `rating=${currentRating}&review=${encodeURIComponent(reviewText)}&user=${encodeURIComponent(userName)}&type=flight`;
        xhr.send(data);
      });
      
      // Handle cancel button
      document.querySelector('.cancel-btn').addEventListener('click', function() {
        // Reset all stars
        stars.forEach(s => {
          s.style.color = '#a8a8b7';
          s.classList.remove('selected');
        });
        document.querySelector('textarea').value = '';
        currentRating = 0;
      });
    });


 // Create an iframe for the confirmation popup   
    function showCancelConfirmation() {
  const iframe = document.createElement('iframe');
  iframe.style.position = 'fixed';
  iframe.style.top = '0';
  iframe.style.left = '0';
  iframe.style.width = '100%';
  iframe.style.height = '100%';
  iframe.style.border = 'none';
  iframe.style.zIndex = '10000';

  // Set the source URL with parameters
  iframe.src = 'confirm_popup.php?title=' + encodeURIComponent('Are you sure to cancel your flight?') +
               '&description=' + encodeURIComponent('If you cancel your flight, you may be subject to cancellation fees depending on the airline\'s policy. Please check the cancellation policy for details.') +
               '&confirmText=' + encodeURIComponent('Cancel Flight') +
               '&confirmClass=btn-danger' +
               '&actionType=cancelFlight';

  // Add iframe to document
  document.body.appendChild(iframe);

  // Listen for message from iframe
  window.addEventListener('message', function handler(event) {
    if (event.data === 'closeModal') {
      document.body.removeChild(iframe);
      window.removeEventListener('message', handler);
    } else if (event.data && event.data.action === 'cancelFlight' && event.data.confirmed) {
      // User confirmed cancel, send request to cancelFlight.php
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
}