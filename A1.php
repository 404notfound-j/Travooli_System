<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

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

include 'connection.php';

// Get airline ID from the flight
$airline_id = null;
$flight_id = isset($_SESSION['selected_flight_id']) ? $_SESSION['selected_flight_id'] : 
            (isset($_SESSION['selected_depart_flight_id']) ? $_SESSION['selected_depart_flight_id'] : null);

if ($flight_id) {
    $sql = "SELECT airline_id FROM flight_info_t WHERE flight_id = '$flight_id'";
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $airline_id = $row['airline_id'];
    }
}

// We'll use JavaScript to get the airline_id from sessionStorage
// The hardcoded value is removed

// Calculate average rating for this airline
$avg_rating = 0;
$total_reviews = 0;
$rating_text = "No reviews yet";

// We'll set the airline_id via JavaScript, so we'll calculate ratings after that
// The PHP code will be executed after the airline_id is set by JavaScript
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Flight Details</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="A1.css">
  <link rel="stylesheet" href="css/feedback.css">
  
  <!-- ✅ Inject login status for JS -->
  <script>
    window.userLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
  </script>
</head>
<body>
  <header>
    <?php include 'userHeader.php'; ?>
  </header>

  <div class="container">
    <div class="title-section">
      <div>
      <h2 id="depart-flight-airline-plane">Loading flight info...</h2>
      <p>
        <img src="icon/airportLocation.svg" alt="Location Icon" class="icon-svg">
        <span id="depart-origin-airport-info">Loading origin airport...</span>
      </p>
        <div>
        <div class="top-rating">
            <div class="top-rating-info">
                <span class="top-rating-score" id="avg-rating">0</span>
            </div>
            <span class="top-rating-text" id="rating-text">No reviews yet </span> 
            <span class="top-reviews" id="top-reviews">16 reviews</span>
        </div>
    </div>
      </div>
      <div class="price-cta">
      <div class="total-price-box">
        <strong id="total-flight-price">RM 0.00</strong>
      </div>
        <div class="action-buttons">
        <button class="like-button"><i class="fa-regular fa-heart"></i></button> 
        <button class="share-button"><i class="fa-solid fa-share"></i></button> 
        <button class="book-now-btn">Book now</button> 
        </div>
      </div>
    </div>
    <img src="" class="airline-picture" id="depart-airplane-image" >
    <div class="features-header"">
      <h3>Basic Economy Features</h3>
      <div class="seat" id="depart-seat-classes">
      <span class="seat-option" data-class="EC">Economy</span>
        <span class="seat-option" data-class="PE">Premium Economy</span>
        <span class="seat-option" data-class="BC">Business Class</span>
        <span class="seat-option" data-class="FC">First Class</span>
      </div>
    </div>

    <div class="policies">
        <div class="policy-header">  
            <h1>Airlines Policies</h1> 
        </div> 
        <div class="p-item">
            <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight cleaning, installation of cabin HEPA filters.</div>
            <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight health screening questions.</div>
        </div>
    </div>
    <div class="flight-box">
      <div class="header-row">
        <div class="flight-title" id="depart-date">Depart Flight</div>
        <div id="depart-flight-duration">Loading duration...</div>
      </div>

      <div class="details-row-1">
        <div class="airline-info-with-logo">
        <img src="" width='150px' alt="Airline Logo" id="depart-airline-logo">
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
    <img src="" width='150px' alt="Return Airline Logo" id="return-airline-logo">
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

    <section class="feedback-section" id="feedback-section">
        <div class="feedback-container">
            <!-- Reviews Header -->
            <div class="reviews-header">
                <h2 class="reviews-title">Reviews</h2>
                <div class="rating-display">
                    <span class="rating-score" id="feedback-avg-rating">0</span>
                    <div class="rating-details">
                        <div class="rating-stars" id="feedback-rating-stars">
                            <!-- Stars will be added by JavaScript -->
                        </div>
                        <span class="rating-label" id="feedback-rating-label">No reviews yet</span>
                    </div>
                </div>
            </div>
            
            <div class="reviews-divider"></div>
            
            <!-- Reviews List -->
            <div class="reviews-list" id="reviews-list">
                <!-- Reviews will be loaded by JavaScript -->
                <p id="no-reviews-message">Loading reviews...</p>
            </div>
        </div>
    </section>

<script src="js/flightDetails.js"></script>
<script src="js/airlineReviews.js"></script>
</body>
</html>

