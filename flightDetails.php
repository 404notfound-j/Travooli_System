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
    <?php include 'userHeader.php'; ?>
  </header>
  <div class="Flight-Container">
    <div class="title-section">
      <div>
      <h2 id="flight-airline-plane">Loading flight info...</h2>
      <p>
        <i class="fa-solid fa-map-pin"></i>
        <span id="origin-airport-info">Loading origin airport...</span>
      </p>
        <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px;">
        <div class="rating-info">
            <span class="rating-score">4.2</span> <!-- Static for now -->
            <span class="rating-text">Very Good 54 reviews</span> <!-- Static for now -->
        </div>

    </div>

      </div>
      <div class="price-cta">
        <strong style="font-size: 24px; color: #5c63f2;" id="flight-price">Loading price...</strong> <!-- Static for now, could be dynamic -->
        <button class="like-button"><i class="fa-solid fa-heart"></i></button> <!-- Static for now -->
        <button><i class="fa-solid fa-share-nodes"></i></button> <!-- Static for now -->
        <button class="book-now-btn">Book now</button> <!-- Static for now -->
      </div>
    </div>
    <!-- Main airplane image might be dynamic based on airline/aircraft -->
    <img src="../Images/airasia.jpg" alt="Plane" class="main-airplane-image" id="main-airplane-image">

    <div class="features-header"">
      <h3 style="color: #5c63f2;">Basic Economy Features</h3>
      <div class="feature-tags">
        <button>Economy</button>
        <button>Premium Economy</button>
        <button>Business Class</button>
        <button>First Class</button>
      </div>
    </div>

    <div class="policies">
      <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight cleaning, installation of cabin HEPA filters.</div>
      <div class="policy-item"><i class="fa-regular fa-clock"></i> Pre-flight health screening questions.</div>
    </div>
    <div class="flight-box">
      <div class="header-row">
        <div class="flight-title" id="depart-date">Loading date...</div>
        <div id="flight-duration">Loading duration...</div>
      </div>

      <div class="details-row-1">
        <div class="airline-info-with-logo">
          <!-- Airline logo will be dynamic -->
          <img src="../Images/air-asia.png" width='80px' alt="Airline Logo" id="airline-logo">
          <div class="airline-details">
            <strong id="airline-name">Loading airline...</strong>
            <div class="aircraft" id="aircraft-type">Loading aircraft...</div>
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
            <strong id="departure-time">Loading...</strong>
            <span id="departure-airport-code">Loading...</span>
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
            <strong id="arrival-time">Loading...</strong>
            <span id="arrival-airport-code">Loading...</span>
          </div>
        </div>
      </div>
    </div>
</div>
    <?php 
    // Start session to access saved reviews
    session_start();
    
    // Get flight reviews from session
    $flightReviews = isset($_SESSION['flight_reviews']) ? $_SESSION['flight_reviews'] : array();
    ?>
    
    <section class="feedback-section">
        <div class="feedback-container">
            <!-- Reviews Header -->
            <div class="reviews-header">
                <h2 class="reviews-title">Reviews</h2>
                <div class="rating-display">
                    <span class="rating-score">4.2</span>
                    <div class="rating-details">
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span class="rating-label">Very good</span>
                    </div>
                </div>
            </div>
            
            <div class="reviews-divider"></div>
            
            <!-- Reviews List -->
            <div class="reviews-list">
                <?php 
                // Display user-submitted reviews first
                if (!empty($flightReviews)) {
                    foreach ($flightReviews as $index => $review) {
                        // Generate star rating HTML
                        $starsHtml = '';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['rating']) {
                                $starsHtml .= '<i class="fas fa-star"></i>';
                            } else {
                                $starsHtml .= '<i class="far fa-star"></i>';
                            }
                        }
                        ?>
                        <!-- User Review -->
                        <div class="review-item">
                            <div class="review-content">
                                <img src="icon/profile.svg" alt="<?php echo htmlspecialchars($review['user']); ?>" class="user-avatar">
                                <div class="review-text">
                                    <div class="review-header">
                                        <span class="user-name"><?php echo htmlspecialchars($review['user']); ?></span>
                                        <span class="separator">|</span>
                                        <div class="review-rating">
                                            <?php echo $starsHtml; ?>
                                        </div>
                                    </div>
                                    <p class="review-comment"><?php echo htmlspecialchars($review['review']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="review-divider"></div>
                        <?php
                    }
                }
                
                // Default reviews
                $defaultUsers = [
                    ['name' => 'Omar Siphron', 'rating' => 5],
                    ['name' => 'Cristofer Ekstrom Bothman', 'rating' => 4],
                    ['name' => 'Kaiya Lubin', 'rating' => 5],
                    ['name' => 'Erin Septimus', 'rating' => 3],
                    ['name' => 'Terry George', 'rating' => 4]
                ];
                
                foreach ($defaultUsers as $index => $user) {
                    // Generate star rating HTML
                    $starsHtml = '';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $user['rating']) {
                            $starsHtml .= '<i class="fas fa-star"></i>';
                        } else {
                            $starsHtml .= '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                    <!-- Default Review -->
                    <div class="review-item">
                        <div class="review-content">
                            <img src="icon/profile.svg" alt="<?php echo htmlspecialchars($user['name']); ?>" class="user-avatar">
                            <div class="review-text">
                                <div class="review-header">
                                    <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                                    <span class="separator">|</span>
                                    <div class="review-rating">
                                        <?php echo $starsHtml; ?>
                                    </div>
                                </div>
                                <p class="review-comment">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                            </div>
                        </div>
                    </div>
                    <?php if ($index < count($defaultUsers) - 1) { ?>
                        <div class="review-divider"></div>
                    <?php } ?>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>
    
    <?php include 'u_footer_1.php'; ?>
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

