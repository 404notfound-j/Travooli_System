<?php include 'userHeader.php'; ?>

<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBook.css">
<link rel="stylesheet" href="css/hotelDetails.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php
// Start session to access saved reviews
session_start();

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

// Get user reviews from session
$userReviews = isset($_SESSION['hotel_reviews']) ? $_SESSION['hotel_reviews'] : array();
?>

<div class="hotel-details-dark-bg">
    <div class="hotel-details-container">
        <div class="hotel-details-header-row">
            <div class="hotel-details-header-left">
                <div class="hotel-details-title-row">
                    <h1 class="hotel-details-title"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    <div class="hotel-details-stars">
                        <span class="hotel-card-stars">
                            <?php for ($i = 0; $i < $hotel['stars']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="hotel-details-star-label"><?php echo $hotel['stars']; ?> Star Hotel</span>
                    </div>
                </div>
                <div class="hotel-details-location">
                    <img src="icon/locationWhite.svg" alt="Location" style="width:18px;vertical-align:middle;"> 
                    <?php echo htmlspecialchars($hotel['location']); ?>
                </div>
                <div style="height: 40px;"></div>
                <div class="hotel-details-review-box">
                    <div class="hotel-details-review-score"><?php echo $hotel['rating']; ?></div>
                    <div class="hotel-details-review-text"><b>Very Good</b> <?php echo $hotel['review_count']; ?> reviews</div>
                </div>
            </div>
            <div class="hotel-details-header-right">
                <div class="hotel-details-price-block">
                    <span class="hotel-details-price-main">$<?php echo $hotel['price']; ?><span class="hotel-details-price-night">/night</span></span>
                </div>
                <div class="hotel-details-action-buttons">
                    <button class="hotel-card-fav-btn" type="button">
                        <object data="icon/heartHotelDetails.svg" type="image/svg+xml" class="heart-icon"></object>
                    </button>
                    <button class="hotel-card-share-btn" type="button">
                        <object data="icon/share.svg" type="image/svg+xml" class="share-icon"></object>
                    </button>
                    <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="hotel-details-book-btn">Book now</a>
                </div>
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
        <hr class="hotel-details-divider">
        <div class="hotel-details-overview-block">
            <h2>Overview</h2>
            <p><?php echo htmlspecialchars($hotel['description']); ?></p>
        </div>
        <div class="hotel-details-review-amenities-row">
            <div class="hotel-details-amenity-review-box">
                <div class="hotel-details-amenity-review-score">4.2</div>
                <div class="hotel-details-amenity-review-text">
                    Very good<br>
                    371 reviews
                </div>
            </div>
            <?php foreach ($hotel['amenities'] as $amenity): ?>
                <div class="hotel-details-amenity-card">
                    <img src="icon/stars.svg" alt="Amenity" class="hotel-details-amenity-icon">
                    <span><?php echo htmlspecialchars($amenity); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <hr class="hotel-details-divider">
    </div>
</div>

<!-- Lower Section: Available Rooms -->
<div class="hotel-details-lower-section">
    <div class="hotel-details-container">
        <h2 class="hotel-details-rooms-title">Available Rooms</h2>
        <div class="hotel-details-rooms-list">
            <div class="hotel-details-room-row">
                <img src="background/room1.jpg" alt="Room 1" class="hotel-details-room-img">
                <div class="hotel-details-room-desc">Superior room - 1 double bed or 2 twin beds</div>
                <div class="hotel-details-room-price">$240<span class="hotel-details-room-night">/night</span></div>
                <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
            </div>
            <hr class="hotel-details-divider2">
            <div class="hotel-details-room-row">
                <img src="background/room2.jpg" alt="Room 2" class="hotel-details-room-img">
                <div class="hotel-details-room-desc">Superior room - City view  - 1 double bed or 2 twin beds</div>
                <div class="hotel-details-room-price">$280<span class="hotel-details-room-night">/night</span></div>
                <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
            </div>
            <hr class="hotel-details-divider2">
            <div class="hotel-details-room-row">
                <img src="background/room3.jpg" alt="Room 3" class="hotel-details-room-img">
                <div class="hotel-details-room-desc">Superior room - City view - 1 double bed or 2 twin beds</div>
                <div class="hotel-details-room-price">$320<span class="hotel-details-room-night">/night</span></div>
                <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
            </div>
            <hr class="hotel-details-divider2">
            <div class="hotel-details-room-row">
                <img src="background/room4.jpg" alt="Room 4" class="hotel-details-room-img">
                <div class="hotel-details-room-desc">Superior room - City view - 1 double bed or 2 twin beds</div>
                <div class="hotel-details-room-price">$350<span class="hotel-details-room-night">/night</span></div>
                <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&checkin=2023-12-01&checkout=2023-12-02&adult=2&child=0&room=1" class="btn btn-primary hotel-details-room-book-btn">Book now</a>
            </div>
        </div>
        <hr class="hotel-details-divider">
        <h2 class="hotel-details-reviews-title">Reviews</h2>
        
        <!-- Reviews Header -->
        <div class="reviews-header">
            <div class="rating-display">
                <span class="rating-score"><?php echo $hotel['rating']; ?></span>
                <div class="rating-details">
                    <div class="rating-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="rating-label"><?php echo $hotel['review_label']; ?></span>
                </div>
            </div>
        </div>
        
        <!-- Reviews List -->
        <div class="reviews-list">
            <?php 
            // Display user-submitted reviews first
            if (!empty($userReviews)) {
                foreach ($userReviews as $index => $review) {
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
</div>

<?php include 'u_footer_2.php'; ?>
<script src="js/hotelDetails.js"></script>
