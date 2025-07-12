<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Travooli Flight Search</title>
  <link rel="stylesheet" href="css/booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="page-container">
    <header>
    <?php include 'userHeader.php'; ?>
    </header>
    <?php
      include 'connection.php'; // Include your database connection file

      // Fetch airports from database
      $airports = [];
      $query = "SELECT airport_id, airport_full, airport_short, city_full FROM airport_t ORDER BY city_full ASC";
      $result = mysqli_query($connection, $query);
      if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
              $airports[] = $row;
          }
      } else {
          echo "<div class='error-message'>Database connection failed. Please try again later.</div>";
      }
      ?>
      <div class="flight-search">
        <div class="search-input">
            <div class="input-group">
                <img src="icon/from-destination.svg" alt="" class="icon">
                <input type="text"
                       placeholder="From where?"
                       id="fromAirport"
                       autocomplete="off"
                       readonly>
                <div class="airport-dropdown" id="fromDropdown">
                    <?php foreach($airports as $airport): ?>
                        <div class="airport-option"
                             data-id="<?= htmlspecialchars($airport['airport_id']) ?>"
                             data-code="<?= htmlspecialchars($airport['airport_short']) ?>"
                             data-city="<?= htmlspecialchars($airport['city_full']) ?>"
                             data-name="<?= htmlspecialchars($airport['airport_full']) ?>">
                            <div class="airport-main">
                                <span class="airport-city"><?= htmlspecialchars($airport['city_full']) ?></span>
                                <span class="airport-code"><?= htmlspecialchars($airport['airport_short']) ?></span>
                            </div>
                            <div class="airport-name"><?= htmlspecialchars($airport['airport_full']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <span class="input-border"></span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="search-input">
            <div class="input-group">
                <img src="icon/destination.svg" alt="" class="icon">
                <input type="text"
                       placeholder="Where to?"
                       id="toAirport"
                       autocomplete="off"
                       readonly>
                <div class="airport-dropdown" id="toDropdown">
                    <?php foreach($airports as $airport): ?>
                        <div class="airport-option"
                             data-id="<?= htmlspecialchars($airport['airport_id']) ?>"
                             data-code="<?= htmlspecialchars($airport['airport_short']) ?>"
                             data-city="<?= htmlspecialchars($airport['city_full']) ?>"
                             data-name="<?= htmlspecialchars($airport['airport_full']) ?>">
                            <div class="airport-main">
                                <span class="airport-city"><?= htmlspecialchars($airport['city_full']) ?></span>
                                <span class="airport-code"><?= htmlspecialchars($airport['airport_short']) ?></span>
                            </div>
                            <div class="airport-name"><?= htmlspecialchars($airport['airport_full']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <span class="input-border"></span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="date-picker-wrapper">
            <div class="search-input">
                <div class="input-group" style="position: relative;">
                    <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                    <input type="text" placeholder="Depart" id="dateInput" readonly style="opacity:0;position:absolute;left:0;top:0;width:100%;height:100%;z-index:2;cursor:pointer;">
                    <span id="dateDisplay" style="position:absolute;left:48px;top:0;height:100%;display:flex;align-items:center;z-index:1;color:#888;font-size:16px;pointer-events:none;">Depart</span>
                    <span class="input-border"></span>
                </div>
            </div>

            <div class="date-picker-dropdown" id="datePickerDropdown">
                <div class="date-picker-container">
                    <div class="field-section">
                        <div class="trip-options">
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="roundTrip" name="tripType" value="round" checked>
                                    <label for="roundTrip">Round trip</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="oneWay" name="tripType" value="one">
                                    <label for="oneWay">One way</label>
                                </div>
                            </div>

                            <div class="date-fields">
                                <div class="date-field focused">
                                    <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                                    <input type="text" placeholder="Depart" id="departDate" readonly>
                                </div>
                                <div class="date-field" id="returnField">
                                    <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                                    <input type="text" placeholder="Return" id="returnDate" readonly>
                                </div>
                            </div>

                            <button class="btn-done">Done</button>
                        </div>
                    </div>

                    <div class="calendar-divider"></div>

                    <div class="calendar-section">
                        <div class="calendar-navigation">
                            <button class="nav-btn prev-month">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <div class="calendar-months">
                                <div class="calendar-month">
                                    <div class="month-header">
                                        <h4 class="month-year" id="currentMonth1">January 2025</h4>
                                    </div>
                                    <div class="date-grid">
                                        <div class="calendar-row header-row">
                                            <div class="calendar-date day-header">S</div>
                                            <div class="calendar-date day-header">M</div>
                                            <div class="calendar-date day-header">T</div>
                                            <div class="calendar-date day-header">W</div>
                                            <div class="calendar-date day-header">T</div>
                                            <div class="calendar-date day-header">F</div>
                                            <div class="calendar-date day-header">S</div>
                                        </div>
                                        <div id="calendarDates1"></div>
                                    </div>
                                </div>

                                <div class="calendar-month">
                                    <div class="month-header">
                                        <h4 class="month-year" id="currentMonth2">February 2025</h4>
                                    </div>
                                    <div class="date-grid">
                                        <div class="calendar-row header-row">
                                            <div class="calendar-date day-header">S</div>
                                            <div class="calendar-date day-header">M</div>
                                            <div class="calendar-date day-header">T</div>
                                            <div class="calendar-date day-header">W</div>
                                            <div class="calendar-date day-header">T</div>
                                            <div class="calendar-date day-header">F</div>
                                            <div class="calendar-date day-header">S</div>
                                    </div>
                                        <div id="calendarDates2"></div>
                                    </div>
                                </div>
                            </div>

                            <button class="nav-btn next-month">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="search-input" id="passengerSection">
            <div class="input-group">
                <img src="icon/person.svg" alt="Person" class="icon" style="width: 28px; height: 28px;">
                <input type="text" placeholder="1 adult" id="passengerInput" readonly>
                <span class="input-border"></span>
            </div>

            <div class="passenger-dropdown" id="passengerDropdown">
                <div class="passenger-container">
                    <div class="passenger-row">
                        <span class="passenger-label">Adult:</span>
                        <div class="passenger-counter">
                            <button class="counter-btn minus" data-type="adult">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="counter-value" id="adultCount">1</span>
                            <button class="counter-btn plus" data-type="adult">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="passenger-row">
                        <span class="passenger-label">Child:</span>
                        <div class="passenger-counter">
                            <button class="counter-btn minus" data-type="child">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="counter-value" id="childCount">0</span>
                            <button class="counter-btn plus" data-type="child">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn search-btn" id="searchBtn">Search</button>
    </div>
  </div>

  <div class="main-content">
    <div class="filters">
      <h2>Filters</h2>
      <div class="filter-group">
        <h4>Seat class</h4>
        <button class="filter-button">Economy</button>
        <button class="filter-button">Premium Economy</button>
        <button class="filter-button">Business Class</button>
        <button class="filter-button">First Class</button>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Time</h4>
          <input type="range" id="timeRange" min="360" max="1380" step="30" value="360">
          <div class="range-labels">
          <span id="startTime">6:00AM</span>
          <span>11:00PM</span>
        </div>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Rating</h4>
        <div class="rating-buttons">
          <button>0+</button>
          <button>1+</button>
          <button>2+</button>
          <button>3+</button>
          <button>4+</button>
        </div>
      </div>
      <hr>
      <div class="filter-group">
        <h4>Airlines</h4>
        <div class="checkbox-group">
        <label class="flex">
            <input type="checkbox" value="AK">
            <span>AirAsia</span>
            </label>

            <label class="flex">
            <input type="checkbox" value="MH">
            <span>Mas</span>
            </label>

            <label class="flex">
            <input type="checkbox" value="FY">
            <span>FireFly</span>
        </label>
        </div>
      </div>
      <hr>
      <div class="action-buttons">
      <button class="cancel-btn" id="cancelBtn">Cancel</button>
        <button class="apply-btn">Apply Filters</button>
      </div>
    </div>
    <div class="main-panel">
    <div id="flightResults" class="flight-results-container"></div>
</div>
</div>
  
  <?php
  // Get airline ratings and reviews
  $airlines = [];
  $sql = "SELECT airline_id, airline_name FROM airline_t";
  $result = mysqli_query($connection, $sql);
  
  if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $airline_id = $row['airline_id'];
      $airline = [
        'airline_id' => $airline_id,
        'airline_name' => $row['airline_name'],
        'avg_rating' => 0,
        'total_reviews' => 0,
        'rating_text' => 'No reviews yet',
        'feedbacks' => []
      ];
      
      // Get average rating
      $rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total 
                    FROM flight_feedback_t 
                    WHERE airline_id = '$airline_id'";
      $rating_result = mysqli_query($connection, $rating_sql);
      
      if ($rating_result && mysqli_num_rows($rating_result) > 0) {
        $rating_row = mysqli_fetch_assoc($rating_result);
        if ($rating_row['total'] > 0) {
          $airline['avg_rating'] = round($rating_row['avg_rating'], 1);
          $airline['total_reviews'] = $rating_row['total'];
          
          // Determine rating text
          if ($airline['avg_rating'] >= 4.5) {
            $airline['rating_text'] = "Excellent";
          } else if ($airline['avg_rating'] >= 4.0) {
            $airline['rating_text'] = "Very good";
          } else if ($airline['avg_rating'] >= 3.0) {
            $airline['rating_text'] = "Good";
          } else if ($airline['avg_rating'] >= 2.0) {
            $airline['rating_text'] = "Fair";
          } else {
            $airline['rating_text'] = "Poor";
          }
        }
      }
      
      // Get recent feedbacks
      $feedback_sql = "SELECT f.*, u.fst_name, u.lst_name 
                      FROM flight_feedback_t f 
                      JOIN user_detail_t u ON f.user_id = u.user_id 
                      WHERE f.airline_id = '$airline_id' 
                      ORDER BY f_feedback_id DESC 
                      LIMIT 3";
      $feedback_result = mysqli_query($connection, $feedback_sql);
      
      if ($feedback_result && mysqli_num_rows($feedback_result) > 0) {
        while ($feedback_row = mysqli_fetch_assoc($feedback_result)) {
          $airline['feedbacks'][] = $feedback_row;
        }
      }
      
      $airlines[] = $airline;
    }
  }
  ?>



  <script src="js/flightBook.js"> </script>

  <?php include 'u_footer_1.php'; ?>
  <?php include 'u_footer_2.php'; ?>
</body>
</html>