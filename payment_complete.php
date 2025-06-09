<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Payment Complete</title>
  <link rel="stylesheet" href="css/payment_complete.css">
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
              <h2>12:00 pm</h2>
              <p>Newark(EWR)</p>
            </div>
            <div class="flight-path">
              <div class="line"></div>
              <i class="fas fa-plane"></i>
              <div class="line"></div>
            </div>
            <div class="arrival">
              <h2>12:00 pm</h2>
              <p>Newark(EWR)</p>
            </div>
          </div>
          <div class="boarding-pass">
            <div class="passenger-info">
              <img src="icon/avatar.svg" alt="Passenger" class="avatar">
              <div>
                <h3>James Doe</h3>
                <p>Boarding Pass N'123</p>
              </div>
              <span class="class">Business Class</span>
            </div>
            <div class="flight-details">
              <div class="detail">
                <div class="icon">
                  <img src="icon/calendar1.svg" alt="Calendar">
                </div>
                <div>
                  <p>Date</p>
                  <span>Newark(EWR)</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/timmer.svg" alt="Clock">
                </div>
                <div>
                  <p>Flight time</p>
                  <span>12:00</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/door.svg" alt="Gate">
                </div>
                <div>
                  <p>Gate</p>
                  <span>A12</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/seat.svg" alt="Seat">
                </div>
                <div>
                  <p>Seat</p>
                  <span>128</span>
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
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
          <i class="fas fa-star"></i>
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
        <button class="cancel-flight-btn">
          Cancel Flight
        </button>
      </section>
    </main>
  </div>
  <?php include 'u_footer_1.php'; ?>
  <?php include 'u_footer_2.php'; ?>
</body>
</html>