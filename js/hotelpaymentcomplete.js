document.addEventListener('DOMContentLoaded', function() {
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

      // Save the review using AJAX
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'save_review.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function() {
        if (this.status === 200) {
          try {
            const response = JSON.parse(this.responseText);
            if (response.success) {
              alert('Thank you for your review! It has been added to the hotel details page.');
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
            alert('Thank you for your review! It has been added to the hotel details page.');
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

      // Send the data
      // Include booking_id and hotel_id from PHP variables (set as data attributes on the submit button)
      const bookingId = submitBtn.getAttribute('data-booking-id');
      const hotelId = submitBtn.getAttribute('data-hotel-id');
      const customerId = submitBtn.getAttribute('data-customer-id');
      const data = `rating=${currentRating}&review=${encodeURIComponent(reviewText)}&booking_id=${encodeURIComponent(bookingId)}&hotel_id=${encodeURIComponent(hotelId)}&customer_id=${encodeURIComponent(customerId)}`;
      xhr.send(data);
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

  // Handle back to homepage button
  const homeBtn = document.querySelector('.home-btn');
  if (homeBtn) {
    homeBtn.addEventListener('click', function() {
      window.location.href = 'U_dashboard.php';
    });
  }

  // Expose showCancelBookingReminder globally
  window.showCancelBookingReminder = function(bookingId) {
    // Create an iframe for the confirmation popup
    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.top = '0';
    iframe.style.left = '0';
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.zIndex = '10000';

    // Set the source URL with parameters
    iframe.src = 'confirm_popup.php?title=' + encodeURIComponent('Are you sure to cancel your room?') +
                '&description=' + encodeURIComponent('If you cancel your booking, you may be subject to cancellation fees depending on the hotel\'s policy. Please check the cancellation policy for details.') +
                '&confirmText=' + encodeURIComponent('Cancel Booking') +
                '&confirmClass=btn-danger' +
                '&actionType=cancelHotel';

    // Add to document
    document.body.appendChild(iframe);

    // Listen for messages from the iframe
    window.addEventListener('message', function eventHandler(event) {
      if (event.data === 'closeModal') {
        // Remove the iframe when closed
        document.body.removeChild(iframe);
        window.removeEventListener('message', eventHandler);
      } else if (event.data && event.data.action === 'cancelHotel' && event.data.confirmed) {
        // AJAX to cancel booking
        fetch(window.location.pathname, {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: 'action=cancelBooking&booking_id=' + encodeURIComponent(bookingId)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Your hotel booking has been cancelled successfully.');
            window.location.href = 'U_dashboard.php';
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(() => alert('Network error, please try again.'));
      }
    });
  };
});
