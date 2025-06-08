<?php include 'userHeader.php'; ?>

<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBook.css">

<style>
.hotel-card-fav-btn {
    background: transparent !important;
    border: 1.5px solid #605DEC !important;
    box-shadow: none !important;
    padding: 6px;
    border-radius: 50%;
    cursor: pointer;
    outline: none;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto;
    z-index: 2;
    transition: border-color 0.2s;
}
.hotel-card-fav-btn .heart-icon {
    color: #605DEC;
    fill: none;
    stroke: #605DEC;
    transition: color 0.2s, fill 0.2s;
}
.hotel-card-fav-btn.selected {
    border-color: #6C63FF !important;
}
.hotel-card-fav-btn.selected .heart-icon {
    color: #605DEC;
    fill: #605DEC;
    stroke: #605DEC;
}
.hotel-card-action-row {
    display: flex;
    align-items: center;
    gap: 16px;
}
.hotel-card-view-btn {
    margin-left: 0;
}
</style>

<!-- Search bar at the top, shorter and centered -->
<div class="hotel-search-bar-wrapper">
<form class="hotel-search-bar" onsubmit="return false;">
    <div class="search-input-group">
        <span class="search-icon"><img src="icon/hotelDestination.svg" alt="Destination"></span>
        <input type="text" id="destination" name="destination" placeholder="Destination?" required>
    </div>
    <div class="search-input-group">
        <span class="search-icon"><img src="icon/calendar.svg" alt="Check in"></span>
        <input type="date" id="checkin" name="checkin" placeholder="Check in" required>
    </div>
    <div class="search-input-group">
        <span class="search-icon"><img src="icon/calendar.svg" alt="Check out"></span>
        <input type="date" id="checkout" name="checkout" placeholder="Check out" required>
    </div>
    <div class="search-input-group guest-room-selector" id="guestRoomSelector">
        <span class="search-icon"><img src="icon/person.svg" alt="Guests"></span>
        <button type="button" id="guestRoomBtn">
            <span id="guestRoomPlaceholder">Guests/Rooms</span>
            <img src="icon/arrow-right.svg" alt="Expand" style="width:12px;vertical-align:middle;transform:rotate(90deg);margin-left:6px;">
        </button>
        <div class="guest-room-dropdown" id="guestRoomDropdown" style="display:none;">
            <div class="row">
                <div class="label">Adult:</div>
                <div class="incrementer">
                    <div class="increment" id="adultMinus"><span>-</span></div>
                    <div class="div" id="adultCount">1</div>
                    <div class="increment" id="adultPlus"><span>+</span></div>
                </div>
            </div>
            <div class="row">
                <div class="label">Child:</div>
                <div class="incrementer">
                    <div class="increment" id="childMinus"><span>-</span></div>
                    <div class="div" id="childCount">0</div>
                    <div class="increment" id="childPlus"><span>+</span></div>
                </div>
            </div>
            <div class="row">
                <div class="label">Room:</div>
                <div class="incrementer">
                    <div class="increment" id="roomMinus"><span>-</span></div>
                    <div class="div" id="roomCount">1</div>
                    <div class="increment" id="roomPlus"><span>+</span></div>
                </div>
            </div>
            <div class="helper-text" id="guestHelperText" style="display:none;">Must have at least 1</div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary search-btn">Search</button>
</form>
</div>

<div class="hotel-booking-main-layout">
    <div class="hotel-booking-filters">
        <h2>Filters</h2>
        <div class="filter-group room-style">
            <h4>Room style</h4>
            <button class="filter-btn">Budget</button>
            <button class="filter-btn">Family</button>
            <button class="filter-btn">Luxury</button>
            <button class="filter-btn">Trendy</button>
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
            <div class="rating-btns">
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
        <div class="filter-actions">
            <button class="btn" type="button" onclick="location.reload();">Reset</button>
            <button class="btn btn-primary" type="button">Apply filters</button>
        </div>
    </div>
    <div class="hotel-booking-content">
        <div class="hotel-list hotel-list-column">
            <!-- Hotel Card 1 -->
            <div class="hotel-card hotel-card-wide">
                <div class="hotel-card-img-col">
                    <img src="background/hotel1.jpg" alt="Hotel 1" class="hotel-card-image">
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
                        <img src="icon/destination.svg" alt="Location" style="width:16px;height:16px;"> Kuala Lumpur, Malaysia
                    </div>
                    <div class="hotel-card-meta-row">
                        <span class="hotel-card-stars">★★★★★</span>
                        <span class="hotel-card-meta">5 Star Hotel</span>
                        <span class="hotel-card-meta"><img src="icon/meal.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> 25+ Aminities</span>
                    </div>
                    <div class="hotel-card-review-row">
                        <span class="hotel-card-review-score">4.7</span>
                        <span class="hotel-card-review-label">Excellent</span>
                        <span class="hotel-card-review-count">210 reviews</span>
                    </div>
                    <div class="hotel-card-action-row">
                        <button class="hotel-card-fav-btn" type="button">
                            <svg class="heart-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-6.5-5.2-9-8.5C-1.5 7.5 2.5 3 7 3c2.1 0 4.1 1.2 5 3.1C13.9 4.2 15.9 3 18 3c4.5 0 8.5 4.5 4 9.5-2.5 3.3-9 8.5-9 8.5z"/>
                            </svg>
                        </button>
                        <a href="hotelDetails.php?hotel_id=1" class="btn btn-primary hotel-card-view-btn">View Place</a>
                    </div>
                </div>
            </div>
            <!-- Hotel Card 2 -->
            <div class="hotel-card hotel-card-wide">
                <div class="hotel-card-img-col">
                    <img src="background/hotel2.jpg" alt="Hotel 2" class="hotel-card-image">
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
                        <img src="icon/destination.svg" alt="Location" style="width:16px;height:16px;"> Istanbul, Turkey
                    </div>
                    <div class="hotel-card-meta-row">
                        <span class="hotel-card-stars">★★★★★</span>
                        <span class="hotel-card-meta">5 Star Hotel</span>
                        <span class="hotel-card-meta"><img src="icon/meal.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> 20+ Aminities</span>
                    </div>
                    <div class="hotel-card-review-row">
                        <span class="hotel-card-review-score">4.2</span>
                        <span class="hotel-card-review-label">Very Good</span>
                        <span class="hotel-card-review-count">54 reviews</span>
                    </div>
                    <div class="hotel-card-action-row">
                        <button class="hotel-card-fav-btn" type="button">
                            <svg class="heart-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-6.5-5.2-9-8.5C-1.5 7.5 2.5 3 7 3c2.1 0 4.1 1.2 5 3.1C13.9 4.2 15.9 3 18 3c4.5 0 8.5 4.5 4 9.5-2.5 3.3-9 8.5-9 8.5z"/>
                            </svg>
                        </button>
                        <a href="hotelDetails.php?hotel_id=2" class="btn btn-primary hotel-card-view-btn">View Place</a>
                    </div>
                </div>
            </div>
            <!-- Hotel Card 3 -->
            <div class="hotel-card hotel-card-wide">
                <div class="hotel-card-img-col">
                    <img src="background/hotel3.jpg" alt="Hotel 3" class="hotel-card-image">
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
                        <img src="icon/destination.svg" alt="Location" style="width:16px;height:16px;"> Johor Bahru, Malaysia
                    </div>
                    <div class="hotel-card-meta-row">
                        <span class="hotel-card-stars">★★★★☆</span>
                        <span class="hotel-card-meta">4 Star Hotel</span>
                        <span class="hotel-card-meta"><img src="icon/meal.svg" alt="Amenities" style="width:18px;vertical-align:middle;"> 15+ Aminities</span>
                    </div>
                    <div class="hotel-card-review-row">
                        <span class="hotel-card-review-score">4.0</span>
                        <span class="hotel-card-review-label">Very Good</span>
                        <span class="hotel-card-review-count">98 reviews</span>
                    </div>
                    <div class="hotel-card-action-row">
                        <button class="hotel-card-fav-btn" type="button">
                            <svg class="heart-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 21s-6.5-5.2-9-8.5C-1.5 7.5 2.5 3 7 3c2.1 0 4.1 1.2 5 3.1C13.9 4.2 15.9 3 18 3c4.5 0 8.5 4.5 4 9.5-2.5 3.3-9 8.5-9 8.5z"/>
                            </svg>
                        </button>
                        <a href="hotelDetails.php?hotel_id=3" class="btn btn-primary hotel-card-view-btn">View Place</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
<script src="js/hotelBook.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hotel-card-fav-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            btn.classList.toggle('selected');
        });
    });
});
</script>

<?php include 'u_footer_2.php'; ?>
