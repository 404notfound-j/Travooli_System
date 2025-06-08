<?php include 'userHeader.php'; ?>

<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBook.css">
<link rel="stylesheet" href="css/hotelDetails.css">

<?php
// Placeholder hotel data (replace with DB query later)
$hotels = [
    1 => [
        'name' => 'CVK Park Bosphorus Hotel Istanbul',
        'location' => 'Gümüssuyu Mah. Inönü Cad. No:8, Istanbul 34437',
        'stars' => 5,
        'price' => 240,
        'images' => [
            'background/hotel1_main.jpg',
            'background/hotel1_1.jpg',
            'background/hotel1_2.jpg',
            'background/hotel1_3.jpg',
            'background/hotel1_4.jpg',
        ],
        'rating' => 4.2,
        'review_label' => 'Very good',
        'review_count' => 371,
        'description' => "Located in Taksim Gmsuyu, the heart of Istanbul, the CVK Park Bosphorus Hotel Istanbul has risen from the ashes of the historic Park Hotel, which also served as Foreign Affairs Palace 120 years ago and is hosting its guests by assuming this hospitality mission. With its 452 luxurious rooms and suites, 8500 m2 SPA and fitness area, 18 meeting rooms including 4 dividable ones and 3 terraces with Bosphorus view, Istanbul's largest terrace with Bosphorus view (4500 m2) and latest technology infrastructure, CVK Park Bosphorus Hotel Istanbul is destined to be the popular attraction point of the city. Room and suite categories at various sizes with city and Bosphorus view, as well as 68 separate luxury suites, are offered to its special guests as a wide variety of selection.",
        'amenities' => [
            'Near park',
            'Near nightlife',
            'Near theater',
            'Clean Hotel',
        ],
    ],
    2 => [
        'name' => 'Eresin Hotels Sultanahmet - Boutique Class',
        'location' => 'Istanbul, Turkey',
        'stars' => 5,
        'price' => 104,
        'images' => [
            'background/hotel2.jpg',
            'background/hotel2_1.jpg',
            'background/hotel2_2.jpg',
            'background/hotel2_3.jpg',
            'background/hotel2_4.jpg',
        ],
        'amenities' => [
            'Free breakfast',
            'Free parking',
            'Free internet',
            'Free airport shuttle',
        ],
        'rating' => 4.2,
        'review_label' => 'Very Good',
        'review_count' => 54,
        'description' => 'Stay in the heart of Istanbul at Eresin Hotels Sultanahmet. Boutique luxury and comfort await you.',
    ],
    3 => [
        'name' => 'Urban Stay Suites',
        'location' => 'Johor Bahru, Malaysia',
        'stars' => 4,
        'price' => 89,
        'images' => [
            'background/hotel3.jpg',
            'background/hotel3_1.jpg',
            'background/hotel3_2.jpg',
            'background/hotel3_3.jpg',
            'background/hotel3_4.jpg',
        ],
        'amenities' => [
            'Free breakfast',
            'Free parking',
            'Free internet',
        ],
        'rating' => 4.0,
        'review_label' => 'Very Good',
        'review_count' => 98,
        'description' => 'Urban Stay Suites offers modern comfort and convenience in Johor Bahru. Perfect for families and business travelers.',
    ],
];

$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 1;
$hotel = $hotels[$hotel_id] ?? $hotels[1];
?>

<div class="hotel-details-dark-bg">
    <div class="hotel-details-container">
        <div class="hotel-details-header-row">
            <div>
                <h1 class="hotel-details-title"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                <div class="hotel-details-stars">
                    <?php for ($i = 0; $i < $hotel['stars']; $i++) echo '★'; ?>
                    <span class="hotel-details-star-label"><?php echo $hotel['stars']; ?> Star Hotel</span>
                </div>
                <div class="hotel-details-location">
                    <img src="icon/destination.svg" alt="Location" style="width:18px;vertical-align:middle;"> 
                    <?php echo htmlspecialchars($hotel['location']); ?>
                </div>
                <div class="hotel-details-review-summary">
                    <span class="hotel-details-review-score"><?php echo $hotel['rating']; ?></span>
                    <span class="hotel-details-review-label"><?php echo $hotel['review_label']; ?></span>
                    <span class="hotel-details-review-count"><?php echo $hotel['review_count']; ?> reviews</span>
                </div>
            </div>
            <div class="hotel-details-header-actions">
                <div class="hotel-details-price-block">
                    <span class="hotel-details-price-main">$<?php echo $hotel['price']; ?><span class="hotel-details-price-night">/night</span></span>
                </div>
                <button class="hotel-card-fav-btn" type="button">
                    <svg class="heart-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 21s-6.5-5.2-9-8.5C-1.5 7.5 2.5 3 7 3c2.1 0 4.1 1.2 5 3.1C13.9 4.2 15.9 3 18 3c4.5 0 8.5 4.5 4 9.5-2.5 3.3-9 8.5-9 8.5z"/>
                    </svg>
                </button>
                <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-book-btn">Book now</a>
            </div>
        </div>
        <div class="hotel-details-gallery-flex">
            <div class="hotel-details-gallery-main">
                <img src="<?php echo $hotel['images'][0]; ?>" alt="Main image" class="hotel-details-main-image">
            </div>
            <div class="hotel-details-gallery-side">
                <?php for ($i = 1; $i < 5; $i++): ?>
                    <?php if (!empty($hotel['images'][$i])): ?>
                        <img src="<?php echo $hotel['images'][$i]; ?>" alt="Gallery image" class="hotel-details-side-image">
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
        <div class="hotel-details-overview-block">
            <h2>Overview</h2>
            <p><?php echo htmlspecialchars($hotel['description']); ?></p>
        </div>
        <div class="hotel-details-review-amenities-row">
            <div class="hotel-details-review-box">
                <div class="hotel-details-review-score-lg"><?php echo $hotel['rating']; ?></div>
                <div class="hotel-details-review-label-lg"><?php echo $hotel['review_label']; ?></div>
                <div class="hotel-details-review-count-lg"><?php echo $hotel['review_count']; ?> reviews</div>
            </div>
            <div class="hotel-details-amenities-list">
                <?php foreach ($hotel['amenities'] as $amenity): ?>
                    <div class="hotel-details-amenity-card">
                        <img src="icon/stars.svg" alt="Amenity" class="hotel-details-amenity-icon">
                        <span><?php echo htmlspecialchars($amenity); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lower Section: Available Rooms -->
<div class="hotel-details-lower-section">
    <h2 class="hotel-details-section-title">Available Rooms</h2>
    <div class="hotel-details-rooms-list">
        <div class="hotel-details-room-row">
            <img src="background/room1.jpg" alt="Room 1" class="hotel-details-room-img">
            <div class="hotel-details-room-desc">Superior room - 1 double bed or 2 twin beds</div>
            <div class="hotel-details-room-price">$240<span class="hotel-details-room-night">/night</span></div>
            <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
        </div>
        <div class="hotel-details-room-row">
            <img src="background/room2.jpg" alt="Room 2" class="hotel-details-room-img">
            <div class="hotel-details-room-desc">Superior room - City view  - 1 double bed or 2 twin beds</div>
            <div class="hotel-details-room-price">$280<span class="hotel-details-room-night">/night</span></div>
            <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
        </div>
        <div class="hotel-details-room-row">
            <img src="background/room3.jpg" alt="Room 3" class="hotel-details-room-img">
            <div class="hotel-details-room-desc">Superior room - City view - 1 double bed or 2 twin beds</div>
            <div class="hotel-details-room-price">$320<span class="hotel-details-room-night">/night</span></div>
            <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
        </div>
        <div class="hotel-details-room-row">
            <img src="background/room4.jpg" alt="Room 4" class="hotel-details-room-img">
            <div class="hotel-details-room-desc">Superior room - City view - 1 double bed or 2 twin beds</div>
            <div class="hotel-details-room-price">$350<span class="hotel-details-room-night">/night</span></div>
            <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
        </div>
    </div>
    <hr class="hotel-details-divider">
    <h2 class="hotel-details-section-title">Reviews</h2>
    <div class="hotel-details-reviews-summary-row">
        <div class="hotel-details-reviews-score">4.2</div>
        <div class="hotel-details-reviews-label">Very good</div>
    </div>
    <div class="hotel-details-reviews-list">
        <div class="hotel-details-review-item">
            <img src="background/user1.jpg" alt="Omar Siphron" class="hotel-details-review-avatar">
            <div class="hotel-details-review-content">
                <div class="hotel-details-review-user">Omar Siphron <span class="hotel-details-review-stars">★★★★★</span></div>
                <div class="hotel-details-review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
        <div class="hotel-details-review-item">
            <img src="background/user2.jpg" alt="Cristofer Ekstrom Bothman" class="hotel-details-review-avatar">
            <div class="hotel-details-review-content">
                <div class="hotel-details-review-user">Cristofer Ekstrom Bothman <span class="hotel-details-review-stars">★★★★★</span></div>
                <div class="hotel-details-review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
        <div class="hotel-details-review-item">
            <img src="background/user3.jpg" alt="Kaiya Lubin" class="hotel-details-review-avatar">
            <div class="hotel-details-review-content">
                <div class="hotel-details-review-user">Kaiya Lubin <span class="hotel-details-review-stars">★★★★★</span></div>
                <div class="hotel-details-review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
        <div class="hotel-details-review-item">
            <img src="background/user4.jpg" alt="Erin Septimus" class="hotel-details-review-avatar">
            <div class="hotel-details-review-content">
                <div class="hotel-details-review-user">Erin Septimus <span class="hotel-details-review-stars">★★★★★</span></div>
                <div class="hotel-details-review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
        <div class="hotel-details-review-item">
            <img src="background/user5.jpg" alt="Terry George" class="hotel-details-review-avatar">
            <div class="hotel-details-review-content">
                <div class="hotel-details-review-user">Terry George <span class="hotel-details-review-stars">★★★★★</span></div>
                <div class="hotel-details-review-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
    </div>
</div>

<?php include 'u_footer_2.php'; ?>
<script src="js/hotelDetails.js"></script>
