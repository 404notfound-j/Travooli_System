<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - Hotel Booking</title>
    <link rel="stylesheet" href="css/hotelBook.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include 'userHeader.php'; ?>
    </header>

    <!-- Search Bar Section -->
    <section class="hotel-search-section">
        <div class="hotel-search-bar-wrapper">
            <form class="hotel-search-bar" onsubmit="return false;">
                <div class="search-input-group">
                    <div class="input-group">
                        <img src="icon/hotelDestination.svg" alt="Destination" class="search-icon">
                        <input type="text" id="destination" name="destination" placeholder="Destination?" required>
                        <span class="input-border"></span>
                    </div>
                </div>
                <div class="search-divider"></div>
                <div class="search-input-group hotel-date-picker-wrapper" id="hotelDatePicker">
                    <div class="input-group">
                        <img src="icon/calendar.svg" alt="Calendar" class="search-icon" style="width:32px;height:32px;">
                        <input type="text" id="hotelDateInput" placeholder="Check in - Check out" readonly>
                        <span class="input-border"></span>
                    </div>
                    
                    <!-- Hotel Date Picker Dropdown -->
                    <div class="hotel-date-picker-dropdown" id="hotelDatePickerDropdown">
                        <div class="hotel-date-picker-container">
                            <!-- Field Section -->
                            <div class="hotel-field-section">
                                <div class="hotel-date-options">
                                    <!-- Date Fields -->
                                    <div class="hotel-date-fields">
                                        <div class="hotel-date-field focused" id="checkinField">
                                            <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                                            <input type="text" placeholder="Check in" id="checkinDate" readonly>
                                        </div>
                                        <div class="hotel-date-field" id="checkoutField">
                                            <img src="icon/calendar.svg" alt="Calendar" class="icon" style="width: 32px; height: 32px;">
                                            <input type="text" placeholder="Check out" id="checkoutDate" readonly>
                                        </div>
                                    </div>
                                    
                                    <!-- Done Button -->
                                    <button class="btn-done" id="hotelDateDone">Done</button>
                                </div>
                            </div>
                            
                            <!-- Divider -->
                            <div class="hotel-calendar-divider"></div>
                            
                            <!-- Calendar Section -->
                            <div class="hotel-calendar-section">
                                <div class="hotel-calendar-navigation">
                                    <button class="hotel-nav-btn hotel-prev-month">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    
                                    <div class="hotel-calendar-months">
                                        <!-- Month 1 -->
                                        <div class="hotel-calendar-month">
                                            <div class="hotel-month-header">
                                                <h4 class="hotel-month-year" id="hotelCurrentMonth1">January 2025</h4>
                                            </div>
                                            <div class="hotel-date-grid">
                                                <div class="hotel-calendar-row hotel-header-row">
                                                    <div class="hotel-calendar-date hotel-day-header">S</div>
                                                    <div class="hotel-calendar-date hotel-day-header">M</div>
                                                    <div class="hotel-calendar-date hotel-day-header">T</div>
                                                    <div class="hotel-calendar-date hotel-day-header">W</div>
                                                    <div class="hotel-calendar-date hotel-day-header">T</div>
                                                    <div class="hotel-calendar-date hotel-day-header">F</div>
                                                    <div class="hotel-calendar-date hotel-day-header">S</div>
                                                </div>
                                                <div id="hotelCalendarDates1"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Month 2 -->
                                        <div class="hotel-calendar-month">
                                            <div class="hotel-month-header">
                                                <h4 class="hotel-month-year" id="hotelCurrentMonth2">February 2025</h4>
                                            </div>
                                            <div class="hotel-date-grid">
                                                <div class="hotel-calendar-row hotel-header-row">
                                                    <div class="hotel-calendar-date hotel-day-header">S</div>
                                                    <div class="hotel-calendar-date hotel-day-header">M</div>
                                                    <div class="hotel-calendar-date hotel-day-header">T</div>
                                                    <div class="hotel-calendar-date hotel-day-header">W</div>
                                                    <div class="hotel-calendar-date hotel-day-header">T</div>
                                                    <div class="hotel-calendar-date hotel-day-header">F</div>
                                                    <div class="hotel-calendar-date hotel-day-header">S</div>
                                                </div>
                                                <div id="hotelCalendarDates2"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="hotel-nav-btn hotel-next-month">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-divider"></div>
                <div class="search-divider"></div>
                <div class="search-input-group guest-room-selector" id="guestRoomSelector">
                    <div class="input-group">
                        <img src="icon/person.svg" alt="Person" class="search-icon">
                        <button type="button" id="guestRoomBtn">
                            <span id="guestRoomPlaceholder">1 adult, 1 room</span>
                        </button>
                        <span class="input-border"></span>
                    </div>
                    <div class="guest-room-dropdown" id="guestRoomDropdown" style="display:none;">
                        <div class="guest-room-container">
                            <!-- Adult Row -->
                            <div class="guest-room-row">
                                <span class="guest-room-label">Adult:</span>
                                <div class="guest-room-counter">
                                    <button type="button" class="guest-counter-btn minus" data-type="adult">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="guest-counter-value" id="hotelAdultCount">1</span>
                                    <button type="button" class="guest-counter-btn plus" data-type="adult">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Child Row -->
                            <div class="guest-room-row">
                                <span class="guest-room-label">Child:</span>
                                <div class="guest-room-counter">
                                    <button type="button" class="guest-counter-btn minus" data-type="child">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="guest-counter-value" id="hotelChildCount">0</span>
                                    <button type="button" class="guest-counter-btn plus" data-type="child">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Room Row -->
                            <div class="guest-room-row">
                                <span class="guest-room-label">Room:</span>
                                <div class="guest-room-counter">
                                    <button type="button" class="guest-counter-btn minus" data-type="room">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="guest-counter-value" id="hotelRoomCount">1</span>
                                    <button type="button" class="guest-counter-btn plus" data-type="room">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="hotel-booking-section">
        <div class="hotel-booking-main-layout">
            <!-- Filters Sidebar -->
            <div class="filters">
              <h2>Filters</h2>
              <div class="filter-group">
                <h4>Room style</h4>
                <button class="filter-button">Budget</button>
                <button class="filter-button">Family</button>
                <button class="filter-button">Luxury</button>
                <button class="filter-button">Trendy</button>
              </div>
              <hr>
              <div class="filter-group">
                <h4>Price</h4>
                <input type="range" min="50" max="1200" value="400" class="price-range" id="priceRange">
                <div class="range-labels">
                  <span>$50</span>
                  <span>$1200</span>
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
                <h4>Freebies</h4>
                <div class="checkbox-group">
                  <label><input type="checkbox"> Free breakfast</label>
                  <label><input type="checkbox"> Free parking</label>
                  <label><input type="checkbox"> Free internet</label>
                  <label><input type="checkbox"> Free airport shuttle</label>
                </div>
              </div>
              <div class="action-buttons">
                <button class="cancel-btn" onclick="location.reload();">Cancel</button>
                <button class="apply-btn">Apply Filters</button>
              </div>
            </div>
            
            <!-- Hotel Results -->
            <main class="hotel-booking-content">
                <div class="hotel-list hotel-list-column">
                    <!-- Hotel Card 1 -->
                    <article class="hotel-card hotel-card-wide">
                        <div class="hotel-card-img-col">
                            <img src="background/hotel1.jpg" alt="Grand Palace Hotel" class="hotel-card-image">
                            <span class="hotel-card-img-count">9 images</span>
                        </div>
                        <div class="hotel-card-info-col">
                            <div class="hotel-card-header-row">
                                <div class="hotel-card-title">Grand Palace Hotel</div>
                                <div class="hotel-card-price-block">
                                    <span class="hotel-card-price-label">starting from</span>
                                    <span class="hotel-card-price-main">$120<span class="hotel-card-price-night">/night</span></span>
                                    <span class="hotel-card-price-tax">excl. tax</span>
                                </div>
                            </div>
                            <div class="hotel-card-location-row">
                                <img src="icon/location.svg" alt="Location" style="width:16px;height:16px;"> Kuala Lumpur, Malaysia
                            </div>
                            <div class="hotel-card-meta-row">
                                <span class="hotel-card-stars">
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                </span>
                                <span class="hotel-card-meta">5 Star Hotel</span>
                                <span class="hotel-card-meta"><img src="icon/aminities.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> <b>25+</b> Amenities</span>
                            </div>
                            <div class="hotel-card-review-row">
                                <span class="hotel-card-review-score">4.7</span>
                                <span class="hotel-card-review-label">Excellent</span>
                                <span class="hotel-card-review-count">210 reviews</span>
                            </div>
                            <hr class="hotel-card-divider">
                            <div class="hotel-card-action-row">
                                <button class="hotel-card-fav-btn" type="button">
                                    <img src="icon/heartHotelBook.svg" alt="Favorite" class="heart-icon">
                                </button>
                                <a href="hotelDetails.php?hotel_id=1" class="hotel-card-view-btn">View Place</a>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Hotel Card 2 -->
                    <article class="hotel-card hotel-card-wide">
                        <div class="hotel-card-img-col">
                            <img src="background/hotel2.jpg" alt="Eresin Hotels Sultanahmet" class="hotel-card-image">
                            <span class="hotel-card-img-count">9 images</span>
                        </div>
                        <div class="hotel-card-info-col">
                            <div class="hotel-card-header-row">
                                <div class="hotel-card-title">Eresin Hotels Sultanahmet - Boutique Class</div>
                                <div class="hotel-card-price-block">
                                    <span class="hotel-card-price-label">starting from</span>
                                    <span class="hotel-card-price-main">$104<span class="hotel-card-price-night">/night</span></span>
                                    <span class="hotel-card-price-tax">excl. tax</span>
                                </div>
                            </div>
                            <div class="hotel-card-location-row">
                                <img src="icon/location.svg" alt="Location" style="width:16px;height:16px;"> Istanbul, Turkey
                            </div>
                            <div class="hotel-card-meta-row">
                                <span class="hotel-card-stars">
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                </span>
                                <span class="hotel-card-meta">5 Star Hotel</span>
                                <span class="hotel-card-meta"><img src="icon/aminities.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> 20+ Amenities</span>
                            </div>
                            <div class="hotel-card-review-row">
                                <span class="hotel-card-review-score">4.2</span>
                                <span class="hotel-card-review-label">Very Good</span>
                                <span class="hotel-card-review-count">54 reviews</span>
                            </div>
                            <hr class="hotel-card-divider">
                            <div class="hotel-card-action-row">
                                <button class="hotel-card-fav-btn" type="button">
                                    <img src="icon/heartHotelBook.svg" alt="Favorite" class="heart-icon">
                                </button>
                                <a href="hotelDetails.php?hotel_id=2" class="hotel-card-view-btn">View Place</a>
                            </div>
                        </div>
                    </article>
                    
                    <!-- Hotel Card 3 -->
                    <article class="hotel-card hotel-card-wide">
                        <div class="hotel-card-img-col">
                            <img src="background/hotel3.jpg" alt="Urban Stay Suites" class="hotel-card-image">
                            <span class="hotel-card-img-count">9 images</span>
                        </div>
                        <div class="hotel-card-info-col">
                            <div class="hotel-card-header-row">
                                <div class="hotel-card-title">Urban Stay Suites</div>
                                <div class="hotel-card-price-block">
                                    <span class="hotel-card-price-label">starting from</span>
                                    <span class="hotel-card-price-main">$89<span class="hotel-card-price-night">/night</span></span>
                                    <span class="hotel-card-price-tax">excl. tax</span>
                                </div>
                            </div>
                            <div class="hotel-card-location-row">
                                <img src="icon/location.svg" alt="Location" style="width:16px;height:16px;"> Johor Bahru, Malaysia
                            </div>
                            <div class="hotel-card-meta-row">
                                <span class="hotel-card-stars">
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                  <i class="fas fa-star"></i>
                                </span>
                                <span class="hotel-card-meta">4 Star Hotel</span>
                                <span class="hotel-card-meta"><img src="icon/aminities.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> 15+ Amenities</span>
                            </div>
                            <div class="hotel-card-review-row">
                                <span class="hotel-card-review-score">4.0</span>
                                <span class="hotel-card-review-label">Very Good</span>
                                <span class="hotel-card-review-count">98 reviews</span>
                            </div>
                            <hr class="hotel-card-divider">
                            <div class="hotel-card-action-row">
                                <button class="hotel-card-fav-btn" type="button">
                                    <img src="icon/heartHotelBook.svg" alt="Favorite" class="heart-icon">
                                </button>
                                <a href="hotelDetails.php?hotel_id=3" class="hotel-card-view-btn">View Place</a>
                            </div>
                        </div>
                    </article>
                </div>
            </main>
        </div>
    </section>

    <script src="js/hotelBook.js"></script>

    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html>


