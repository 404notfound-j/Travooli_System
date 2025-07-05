<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Travooli Flight Search</title>
  <link rel="stylesheet" href="./css/booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="page-container">
    <header>
    <?php include 'userHeader.php'; ?>
    </header>
    <?php
      include 'connection.php';
      
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
      ?> <div class="flight-search">
    <!-- From Input -->
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
    
    <!-- Divider -->
    <div class="divider"></div>
    
    <!-- To Input -->
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
    
    <!-- Divider -->
    <div class="divider"></div>
    
    <!-- Date Picker Section -->
    <div class="date-picker-wrapper">
        <div class="search-input">
            <div class="input-group" style="position: relative;">
                <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                <input type="text" placeholder="Depart" id="dateInput" readonly style="opacity:0;position:absolute;left:0;top:0;width:100%;height:100%;z-index:2;cursor:pointer;">
                <span id="dateDisplay" style="position:absolute;left:48px;top:0;height:100%;display:flex;align-items:center;z-index:1;color:#888;font-size:16px;pointer-events:none;">Depart</span>
                <span class="input-border"></span>
            </div>
        </div>
        
        <!-- Date Picker Dropdown -->
        <div class="date-picker-dropdown" id="datePickerDropdown">
            <div class="date-picker-container">
                <!-- Field Section -->
                <div class="field-section">
                    <div class="trip-options">
                        <!-- Radio Group -->
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
                        
                        <!-- Date Fields -->
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
                        
                        <!-- Done Button -->
                        <button class="btn-done">Done</button>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="calendar-divider"></div>
                
                <!-- Calendar Section -->
                <div class="calendar-section">
                    <div class="calendar-navigation">
                        <button class="nav-btn prev-month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="calendar-months">
                            <!-- Month 1 -->
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
                            
                            <!-- Month 2 -->
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
    
    <!-- Divider -->
    <div class="divider"></div>
    
    <!-- Passenger Input -->
    <div class="search-input" id="passengerSection">
        <div class="input-group">
            <img src="icon/person.svg" alt="Person" class="icon" style="width: 28px; height: 28px;">
            <input type="text" placeholder="1 adult" id="passengerInput" readonly>
            <span class="input-border"></span>
        </div>
        
        <!-- Passenger Dropdown -->
        <div class="passenger-dropdown" id="passengerDropdown">
            <div class="passenger-container">
                <!-- Adult Row -->
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
                
                <!-- Child Row -->
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
    
    <!-- Search Button -->
    <button class="btn search-btn" id="searchBtn">Search</button>
</div>
  </div> <!-- End of page-container -->
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
          <label><input type="checkbox">AirAsia</label>
          <label><input type="checkbox">Mas</label>
          <label><input type="checkbox">FireFly</label>
        </div>
      </div>
      <hr>
      <div class="action-buttons">
      <button class="cancel-btn" id="cancelBtn">Cancel</button>
        <button class="apply-btn">Apply Filters</button>
      </div>
    </div>
    <div class="main-panel">
  <div id="flightResults" class="results-container"></div>
</div>
</div>
<script src="js/script.js"> </script>
<script src="js/flightBook.js"> </script>
</body>
</html>

