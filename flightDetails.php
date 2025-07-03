<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Flight Details</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/flightDetails.css">
</head>
<body>
  <header>
    <?php include 'userHeader.php'; 
  if (isset($_GET['flightId'])) {
    $_SESSION['selected_flight_id'] = $_GET['flightId'];
}
if (isset($_GET['classId'])) {
    $_SESSION['selectedClass'] = $_GET['classId'];
}

if (isset($_GET['depart'])) {
    $_SESSION['selected_depart_flight_id'] = $_GET['depart'];
}
if (isset($_GET['return'])) {
    $_SESSION['selected_return_flight_id'] = $_GET['return'];
}
    ?>
  </header>
  <div class="container">
    <div class="title-section">
      <div>
      <h2 id="depart-flight-airline-plane">Loading flight info...</h2>
      <p>
        <i class="fa-solid fa-map-pin"></i>
        <span id="depart-origin-airport-info">Loading origin airport...</span>
      </p>
        <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px;">
        <div class="rating-info">
            <span class="rating-score">4.2</span> <!-- Static for now -->
            <span class="rating-text">Very Good 54 reviews</span> <!-- Static for now -->
        </div>
    </div>
      </div>
      <div class="price-cta">
        <strong style="font-size: 24px; color: #5c63f2;" id="depart-flight-price">Loading price...</strong> <!-- Static for now, could be dynamic -->
        <button class="like-button"><i class="fa-solid fa-heart"></i></button> <!-- Static for now -->
        <button><i class="fa-solid fa-share-nodes"></i></button> <!-- Static for now -->
        <button class="book-now-btn">Book now</button> 
      </div>
    </div>
    <!-- Main airplane image might be dynamic based on airline/aircraft -->
    <img src="images/plane.jpg" class="airline-picture" id="depart-airplane-image" >
    <div class="features-header"">
      <h3 style="color: #5c63f2;">Basic Economy Features</h3>
      <div class="seat">
      <span class="seat-option" data-class="EC">Economy</span>
        <span class="seat-option" data-class="PE">Premium Economy</span>
        <span class="seat-option" data-class="BC">Business Class</span>
        <span class="seat-option" data-class="FC">First Class</span>
      </div>
    </div>

    <div class="policies">
      <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight cleaning, installation of cabin HEPA filters.</div>
      <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight health screening questions.</div>
    </div>
    <div class="flight-box">
      <div class="header-row">
        <div class="flight-title" id="depart-date">Depart date</div>
        <div id="depart-flight-duration">Loading duration...</div>
      </div>

      <div class="details-row-1">
        <div class="airline-info-with-logo">
          <!-- Airline logo will be dynamic -->
          <img src="../Images/air-asia.png" width='80px' alt="Airline Logo" id="airline-logo">
          <div class="airline-details">
          <strong id="depart-airline-name">Loading airline...</strong>
            <div class="aircraft" id="depart-aircraft-type">Loading aircraft...</div>
          </div>
        </div>

        <div class="icon-group-row-1">
           <i class="fa-solid fa-plane"></i>
          <div class="flight-icon-group separated">
            <i class="fa-solid fa-wifi"></i>
            <i class="fa-solid fa-clock"></i>
            <i class="fa-solid fa-utensils"></i>
            <i class="fa-solid fa-suitcase-rolling"></i>
            <i class="fa-solid fa-wheelchair"></i>
          </div>
        </div>
      </div>

      <div class="details-row-2">
        <div class="flight-time-info-left">
          <div class="flight-time">
            <strong id="depart-departure-time">Loading...</strong>
            <span id="depart-departure-airport-code">Loading...</span>
          </div>
        </div>

        <div class="plane-line-middle">
            <span class="dot"></span>
            <div class="horizontal-line"></div>
            <i class="fa-solid fa-plane"></i>
            <div class="horizontal-line"></div>
            <span class="dot"></span>
        </div>

        <div class="flight-time-info-right">
          <div class="flight-time">
            <strong id="depart-arrival-time">Loading...</strong>
            <span id="depart-arrival-airport-code">Loading...</span>
          </div>
        </div>
      </div>
    </div>
    <div class="flight-box" id="return-flight-section">
  <div class="header-row">
    <div class="flight-title" id="return-date">Return Flight</div>
    <div id="return-flight-duration">Loading duration...</div>
  </div>

  <div class="details-row-1">
    <div class="airline-info-with-logo">
      <img src="../Images/air-asia.png" width='80px' alt="Return Airline Logo" id="return-airline-logo">
      <div class="airline-details">
        <strong id="return-airline-name">Loading airline...</strong>
        <div class="aircraft" id="return-aircraft-type">Loading aircraft...</div>
      </div>
    </div>

    <div class="icon-group-row-1">
      <i class="fa-solid fa-plane"></i>
      <div class="flight-icon-group separated">
        <i class="fa-solid fa-wifi"></i>
        <i class="fa-solid fa-clock"></i>
        <i class="fa-solid fa-utensils"></i>
        <i class="fa-solid fa-suitcase-rolling"></i>
        <i class="fa-solid fa-wheelchair"></i>
      </div>
    </div>
  </div>

  <div class="details-row-2">
    <div class="flight-time-info-left">
      <div class="flight-time">
        <strong id="return-departure-time">Loading...</strong>
        <span id="return-departure-airport-code">Loading...</span>
      </div>
    </div>

    <div class="plane-line-middle">
      <span class="dot"></span>
      <div class="horizontal-line"></div>
      <i class="fa-solid fa-plane"></i>
      <div class="horizontal-line"></div>
      <span class="dot"></span>
    </div>

    <div class="flight-time-info-right">
      <div class="flight-time">
        <strong id="return-arrival-time">Loading...</strong>
        <span id="return-arrival-airport-code">Loading...</span>
      </div>
    </div>
  </div>
</div>
    <?php include 'feedback.php'; ?>

<script src="js/flightDetails.js"></script>

<!-- The script for highlighting seat class buttons can remain or be moved to flightDetails.js -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Get the seatClass parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedSeatClass = urlParams.get('seatClass');

    if (selectedSeatClass) {
      // Find all seat class buttons
      const buttons = document.querySelectorAll('.feature-tags button');

      // Loop through buttons and add 'selected' class if text matches
      buttons.forEach(button => {
        if (button.textContent.trim() === selectedSeatClass) {
          button.classList.add('selected');
        }
      });
    }
  });
</script>

</body>
</html>

