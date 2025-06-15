<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Payment Complete</title>
  <link rel="stylesheet" href="css/hotelPaymentComplete.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>
    <main class="container">
      <h1 class="title">Have a good trip, Peter!</h1>
      <p class="reference">Booking Reference: <span>#BK0012345678</span></p>
      <p class="description">
          Thank you for booking your travel with <span>Travooli</span> !<br>
          Below is a summary of your trip to Tokyo. 
          A copy of your booking confirmation has been sent to your email address. You can always revisit this information in the My Trips section of our app. Safe travels!
      </p>

      <section class="layout">
        <div class="ticket">
          <div class="flight-times">
            <div class="departure">
              <h2>Thur, Dec 8</h2>
              <p>Check-in</p>
            </div>
            <div class="flight-path">
                <img src="icon/hotelPaymentComplete.svg">
            </div>
            <div class="arrival">
              <h2>Fri, Dec 9</h2>
              <p>Check-out</p>
            </div>
          </div>
          <div class="boarding-pass">
            <div class="passenger-info">
              <img src="icon/avatar.svg" alt="Passenger" class="avatar">
              <div>
                <h3>James Doe</h3>
              </div>
              <span class="class">Superior room - 1 double bed<br>or 2 twin beds</span>
            </div>
            <div class="flight-details">
              <div class="detail">
                <div class="icon">
                  <img src="icon/calendar1.svg" alt="Calendar">
                </div>
                <div>
                  <p>Check-in time</p>
                  <span>12:00 pm</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/timmer.svg" alt="Clock">
                </div>
                <div>
                  <p>Check-out time</p>
                  <span>11:30 pm</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/door.svg" alt="Gate">
                </div>
                <div>
                  <p>Room no.</p>
                  <span>On arrival</span>
                </div>
              </div>
            </div>
            <div class="flight-code">
              <div class="flight-code-content">
                <h3>MYS</h3>
                <p>TSI-MH-A2301</p>
              </div>
              <img src="icon/barcode.svg" alt="Barcode" class="barcode">
            </div>
          </div>
        </div>
        <div class="price-breakdown">
          <h3>Price breakdown</h3>
          <div class="price-items">
            <div class="price-item">
              <span>Subtotal</span>
              <span>RM 340</span>
            </div>
            <div class="price-item">
              <span>Baggage fees</span>
              <span>RM 20</span>
            </div>
            <div class="price-item">
              <span>Multi-meal</span>
              <span>RM 30</span>
            </div>
            <div class="price-item">
              <span>Taxes and Fees</span>
              <span>RM 121</span>
            </div>
            <div class="price-total">
              <span>Amount Paid</span>
              <span>RM 491</span>
          </div>
        </div>
      </section>

      <section class="action-buttons">
        <div class="share-btn">
          <img src="icon/share.svg" alt="btn"> 
        </div>
        <button class="download-btn">
          Download
        </button>
        <button class="home-btn">
          Back to Homepage
        </button>
      </section>
    <!-- Ratings Section -->

      <section class="ratings">
        <p>Your feedback matters to us. Let us know how we can improve your experience.</p>
        <div class="stars">
          <i class="fas fa-star" id="star-1" data-rating="1"></i>
          <i class="fas fa-star" id="star-2" data-rating="2"></i>
          <i class="fas fa-star" id="star-3" data-rating="3"></i>
          <i class="fas fa-star" id="star-4" data-rating="4"></i>
          <i class="fas fa-star" id="star-5" data-rating="5"></i>
        </div>
        <textarea placeholder="Share your thoughts..."></textarea>
        <div class="rating-buttons">
          <button class="cancel-btn">Cancel</button>
          <button class="submit-btn">Submit</button>
        </div>
      </section>

      <section class ="cancel-flight">
        <h1>Cancellation Policy</h1>
        <p>
          This flight has a flexible cancellation policy. If you cancel or change your flight up to 24 hours before the departure date, you may be eligible for a refund or minimal fees, depending on your ticket type. Refundable ticket holders are entitled to a full or partial refund.
        </p>
        <p>
          All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the airline and ticket type. For full details, please review the cancellation policy for this flight during the booking process.
        </p> 
        <button class="cancel-flight-btn" onclick="showCancelConfirmation()">
          Cancel Booking
        </button>
      </section>
    </main>
  </div>
  <?php include 'u_footer_1.php'; ?>
  <?php include 'u_footer_2.php'; ?>
  
  <script>
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
        const data = `rating=${currentRating}&review=${encodeURIComponent(reviewText)}&user=${encodeURIComponent(userName)}&type=hotel`;
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

    // Function to show cancel confirmation popup
    function showCancelConfirmation() {
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
      window.addEventListener('message', function(event) {
        if (event.data === 'closeModal') {
          // Remove the iframe when closed
          document.body.removeChild(iframe);
        } else if (event.data && event.data.action === 'cancelHotel' && event.data.confirmed) {
          // Handle hotel cancellation
          alert('Your hotel booking has been cancelled successfully.');
          // Redirect to homepage or booking list
          window.location.href = 'index.php';
        }
      });
    }
  </script>
</body>
</html>