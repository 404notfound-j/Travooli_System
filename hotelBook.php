<?php
// Start session to check login status
session_start();
include 'connection.php';

// Debug information
// echo "<!-- GET params: " . print_r($_GET, true) . " -->";

$connection = mysqli_connect($servername, $username, $password, $dbname);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get search parameter if exists
$search_location = isset($_GET['destination']) ? trim($_GET['destination']) : '';

// Store check-in and check-out dates in session if provided
if (isset($_GET['checkin']) && isset($_GET['checkout'])) {
    $_SESSION['checkin_date'] = $_GET['checkin'];
    $_SESSION['checkout_date'] = $_GET['checkout'];
}

// Pagination settings
$hotels_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $hotels_per_page;

// --- Filter parameters ---
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$freebies = isset($_GET['freebies']) ? $_GET['freebies'] : [];

// Get total number of hotels for pagination
$count_query = "SELECT COUNT(DISTINCT h.hotel_id) as total FROM hotel_t h JOIN hotel_room_t hr ON h.hotel_id = hr.hotel_id";

// Add search filter if location is provided
if (!empty($search_location)) {
    $search_location = mysqli_real_escape_string($connection, $search_location);
    $count_query .= " WHERE h.city LIKE '%$search_location%' OR h.address LIKE '%$search_location%'";
}

$count_result = mysqli_query($connection, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_hotels = $count_row['total'];
$total_pages = ceil($total_hotels / $hotels_per_page);

// Ensure current page is within valid range
if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

// Get min/max price for slider
$price_query = "SELECT MIN(price_per_night) as min_price, MAX(price_per_night) as max_price FROM hotel_room_t";
$price_result = mysqli_query($connection, $price_query);
$price_row = mysqli_fetch_assoc($price_result);
$slider_min_price = $price_row['min_price'];
$slider_max_price = $price_row['max_price'];

// Get selected price range from GET or use slider min/max
$min_price = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : $slider_min_price;
$max_price = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : $slider_max_price;

// Get hotel data from database with ratings
$query = "SELECT h.hotel_id, h.name, h.city, h.address, 
          MIN(hr.price_per_night) as min_price,
          (SELECT COUNT(*) FROM hotel_feedback_t hf WHERE hf.hotel_id = h.hotel_id) as review_count,
          (SELECT AVG(rating) FROM hotel_feedback_t hf WHERE hf.hotel_id = h.hotel_id) as avg_rating
          FROM hotel_t h
          JOIN hotel_room_t hr ON h.hotel_id = hr.hotel_id";

// Add search filter if location is provided
$where = [];
if (!empty($search_location)) {
    $search_location = mysqli_real_escape_string($connection, $search_location);
    $where[] = "(h.city LIKE '%$search_location%' OR h.address LIKE '%$search_location%')";
}
// Rating filter
if ($rating > 0) {
    $where[] = "(SELECT AVG(rating) FROM hotel_feedback_t hf WHERE hf.hotel_id = h.hotel_id) >= $rating";
}
// Freebies filter
if (!empty($freebies)) {
    foreach ($freebies as $freebie) {
        $freebie_esc = mysqli_real_escape_string($connection, $freebie);
        $where[] = "EXISTS (SELECT 1 FROM h_freebies_t hf JOIN freebies_t f ON hf.freebie_id = f.freebie_id WHERE hf.hotel_id = h.hotel_id AND f.name = '$freebie_esc')";
    }
}
// Price filter
if ($min_price > $slider_min_price || $max_price < $slider_max_price) {
    $where[] = "EXISTS (SELECT 1 FROM hotel_room_t hr WHERE hr.hotel_id = h.hotel_id AND hr.price_per_night >= $min_price AND hr.price_per_night <= $max_price)";
}
if ($where) {
    $query .= " WHERE " . implode(' AND ', $where);
}

$query .= " GROUP BY h.hotel_id, h.name, h.city, h.address";
$query .= " LIMIT $offset, $hotels_per_page";

// For debugging
// echo "<!-- Query: " . htmlspecialchars($query) . " -->";

$result = mysqli_query($connection, $query);

if (!$result) {
    echo "<!-- Query Error: " . mysqli_error($connection) . " -->";
}

$hotels = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate review label based on average rating
        $avg_rating = round($row['avg_rating'], 1);
        if ($avg_rating >= 4.5) {
            $review_label = "Excellent";
        } elseif ($avg_rating >= 4.0) {
            $review_label = "Very Good";
        } elseif ($avg_rating >= 3.0) {
            $review_label = "Good";
        } elseif ($avg_rating >= 2.0) {
            $review_label = "Average";
        } else {
            $review_label = "Poor";
        }
        
        $row['review_label'] = $review_label;
        $hotels[] = $row;
    }
}

// Room type to filter mapping
$room_type_filters = [
    'Standard Room' => ['Budget'],
    'Deluxe Room' => ['Family', 'Trendy'],
    'Executive Room' => ['Family', 'Luxury'],
    'Junior Suite' => ['Luxury', 'Trendy'],
];

// Get selected room styles from GET
$selected_room_styles = isset($_GET['room_styles']) ? $_GET['room_styles'] : [];

// Add this mapping after fetching $hotels
$hotel_images = [
    "The Majestic Hotel Kuala Lumpur" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/626583484.jpg?k=7a986d38de3402820e6da88ce7ce93651586345780c10a1ad353a217728cc6f0&o=",
    "Traders Hotel Kuala Lumpur" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/597937237.jpg?k=2f47177a02991865d42f6eee1fc14b4397824a29fd3f1eb9dceca64f476ca216&o=",
    "Concorde Hotel Kuala Lumpur" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/65700345.jpg?k=afa8c28bf51f6aab8f46bd61a4b4f518a114af6336a245c17526a4c8543eae0c&o=",
    "Corus Hotel Kuala Lumpur" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/68632630.jpg?k=def0b2bcf01d48d229dd99ac827a64fec0de31cbaa726603611a45154f40ea8d&o=",
    "One World Hotel" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/94821441.jpg?k=ed62256439a85b392068c782cfba0e1a1bcf3830fdd3003da271c2bfae0659eb&o=",
    "Sunway Resort Hotel" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/591410121.jpg?k=c0862dfb4087c187daf94499d8036005dca72c6d52d630bb8f326440e4e297ae&o=&hp=1",
    "Hilton Petaling Jaya" => "https://lh3.googleusercontent.com/p/AF1QipMBzIy--aJYUNXkqoPrPKmR1AvjFLtXee8d4KGa=s1360-w1360-h1020-rw",
    "Avani Sepang Goldcoast Resort" => "https://assets.avanihotels.com/image/upload/q_auto,f_auto/media/minor/avani/images/sepang/homepage/avani_sepang_aeril_view_2024_944x510.jpg",
    "Eastern & Oriental Hotel" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/237235864.jpg?k=7773eed4d2fa5ad337f29fc00aa4667ea7cd9fffac3529af6ac9b38fe01b13de&o=&hp=1",
    "Shangri-La's Rasa Sayang Resort" => "https://sitecore-cd-imgr.shangri-la.com/MediaFiles/3/0/2/%7B3029A607-0E7D-49E3-B82A-7AEFD72D2DC2%7D11a2cb322a3e484285cfcb86cac01950.jpg?width=750&height=752&mode=crop&quality=100&scale=both",
    "G Hotel Kelawai" => "https://kelawai.ghotel.com.my/img/asset/bWFpbi9pbWFnZXMvMDlfZ3Jhdml0eS0obmlnaHQpLmpwZw==?w=800&fit=crop&s=8e1f0777c1f80e976b8b5e2a39eee7b9",
    "Lexis Suites Penang" => "https://cdn.galaxy.tf/uploads/2s/cms_image/001/606/458/1606458519_5fc09c9701b78-thumb.jpg",
    "Shangri-La's Tanjung Aru Resort" => "https://sitecore-cd-imgr.shangri-la.com/MediaFiles/9/B/8/%7B9B87086A-2921-44A4-B2AA-64F361D32B58%7D20240327_TAH_YourShangStory_Overview_1920x940.jpg?width=630&height=480&mode=crop&quality=100&scale=both",
    "Sutera Harbour Resort" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/206164320.jpg?k=acb999457e758b460ef668b673ac34ab5d4400035fef677150b19767003c9b2a&o=&hp=1",
    "Le Meridien Kota Kinabalu" => "https://www.cfmedia.vfmleonardo.com/imageRepo/7/0/156/430/587/bkimd-exterior-6764-hor-clsc_S.jpg",
    "Hyatt Regency Kinabalu" => "https://pix10.agoda.net/hotelImages/10396/0/f0a39d8818891685b9bd67792ec17824.jpg?ca=7&ce=1&s=414x232",
    "The Regency Hotel Alor Setar" => "https://pix10.agoda.net/hotelImages/261/261429/261429_16102008550047953048.jpg?ca=6&ce=1&s=1024x768",
    "Hotel Grand Crystal" => "https://q-xx.bstatic.com/xdata/images/hotel/max500/265922580.jpg?k=f352643720506a1677508575083ce0d4a3346d61188d32d4c93a4acbdf6bf21e&o=",
    "The Village Resort Langkawi" => "https://www.thevillalangkawi.com/wp-content/uploads/2024/05/TheVillaLangkawi-scaled.jpeg",
    "Berjaya Langkawi Resort" => "https://cdn.prod.website-files.com/64d618bb0ccc37b64e1d6053/6513b978e0b8553d4ca9968e_BLR_world_luxury_awards_2022_1920x1080.jpeg",
    "Hyatt Regency Kuantan Resort" => "https://www.hotelscombined.com/rimg/himg/4b/60/33/ice-49853-64595122_3XL-544877.jpg?width=968&height=607&crop=true",
    "Swiss-Garden Resort Kuantan" => "https://q-xx.bstatic.com/xdata/images/hotel/max500/581441034.jpg?k=1815a00792bf5ac734136656e3d8b994153dbf77e6d2a2932ac5c9913cae820b&o=",
    "The Zenith Hotel Kuantan" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQPBmlniQ538s-i9vzh4tmKzuKQgJbxli-wQg&s",
    "Merlin Beach Resort" => "https://cache.marriott.com/content/dam/marriott-renditions/HKTMB/hktmb-pool-0109-hor-clsc.jpg?output-quality=70&interpolation=progressive-bilinear&downsize=856px:*",
    "Primula Beach Hotel" => "https://q-xx.bstatic.com/xdata/images/hotel/max500/203782176.jpg?k=e8d253b9affa08ea42da32586d006f02adfef93f3f485ae679ca0616b8a323b9&o=",
    "Grand Puteri Hotel" => "https://pix10.agoda.net/hotelImages/478/478345/478345_17031314200051510075.jpg?ca=6&ce=1&s=414x232",
    "Duyong Marina & Resort" => "https://pix10.agoda.net/hotelImages/251/251683/251683_111117143844086.jpg?ca=0&ce=1&s=414x232",
    "The Taaras Beach & Spa Resort" => "https://res.klook.com/images/fl_lossy.progressive,q_65/c_fill,w_1295,h_1295/w_80,x_15,y_15,g_south_west,l_Klook_water_br_trans_yhcmh3/activities/no0fmnyjoiikcfsnivdm/[50Off]TheTaarasBeachSpaResortIslandRetreatatRedangIsland.jpg",
    "KSL Hotel & Resort" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVzO0D_mBZtoTQumTrnV1CS1-tDDTVSG6LOQ&s",
    "DoubleTree by Hilton Johor Bahru" => "https://www.hilton.com/im/en/JHBDTDI/1835046/jhbdt-poolevening.jpg?impolicy=crop&cw=7360&ch=4120&gravity=NorthWest&xposition=0&yposition=395&rw=768&rh=430",
    "Traders Hotel Puteri Harbour" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQshBXZTD5iPWGE8tDWDuuBfS08AD6SxUqm6g&s",
    "Legoland Malaysia Hotel" => "https://pix10.agoda.net/hotelImages/587/5874073/5874073_18101214240068455620.jpg?ca=7&ce=1&s=414x232",
    "Pullman Kuching" => "https://cf.bstatic.com/xdata/images/hotel/max1024x768/39241664.jpg?k=be9f0b972a3e7505840ab8797d3f75b50caaa21c000d376c8c8b567cfb008253&o=&hp=1",
    "Hilton Kuching" => "https://www.hilton.com/im/en/KUCHITW/4716264/kuchi-outdoor-pool.tif?impolicy=crop&cw=4288&ch=2400&gravity=NorthWest&xposition=0&yposition=223&rw=768&rh=430",
    "The Waterfront Hotel" => "https://q-xx.bstatic.com/xdata/images/hotel/max500/64528988.jpg?k=41915bccd2c8825c5664a027ccdaea21cdb7f6b604976df485e52ed9abd099a6&o=",
    "Borneo Highlands Resort" => "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/07/b1/51/6e/borneo-highlands-resort.jpg?w=1200&h=-1&s=1"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - Hotel Booking</title>
    <link rel="stylesheet" href="css/hotelBook.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/loginReminder.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    <script>
    window.sliderMinPrice = <?php echo (int)$slider_min_price; ?>;
    window.sliderMaxPrice = <?php echo (int)$slider_max_price; ?>;
    window.selectedMinPrice = <?php echo (int)$min_price; ?>;
    window.selectedMaxPrice = <?php echo (int)$max_price; ?>;
    </script>
</head>
<body>
    <header>
        <?php include 'userHeader.php'; ?>
    </header>

    <!-- Search Bar Section -->
    <section class="hotel-search-section">
        <div class="hotel-search-bar-wrapper">
            <form class="hotel-search-bar" method="GET" action="hotelBook.php" id="hotelSearchForm">
                <div class="search-input-group">
                    <div class="input-group">
                        <img src="icon/hotelDestination.svg" alt="Destination" class="search-icon">
                        <input type="text" id="destination" name="destination" placeholder="Destination?" value="<?php echo isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : ''; ?>">
                        <span class="input-border"></span>
                    </div>
                </div>
                <!-- Hidden fields for check-in and check-out -->
                <input type="hidden" name="checkin" id="checkinHidden" value="<?php echo isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : (isset($_SESSION['checkin_date']) ? htmlspecialchars($_SESSION['checkin_date']) : ''); ?>">
                <input type="hidden" name="checkout" id="checkoutHidden" value="<?php echo isset($_GET['checkout']) ? htmlspecialchars($_GET['checkout']) : (isset($_SESSION['checkout_date']) ? htmlspecialchars($_SESSION['checkout_date']) : ''); ?>">
                <input type="hidden" name="adult" id="adultHidden" value="<?php echo isset($_GET['adult']) ? htmlspecialchars($_GET['adult']) : '1'; ?>">
                <input type="hidden" name="child" id="childHidden" value="<?php echo isset($_GET['child']) ? htmlspecialchars($_GET['child']) : '0'; ?>">
                <input type="hidden" name="room" id="roomHidden" value="<?php echo isset($_GET['room']) ? htmlspecialchars($_GET['room']) : '1'; ?>">
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
                                    <button type="button" class="btn-done" id="hotelDateDone">Done</button>
                                </div>
                            </div>
                            
                            <!-- Divider -->
                            <div class="hotel-calendar-divider"></div>
                            
                            <!-- Calendar Section -->
                            <div class="hotel-calendar-section">
                                <div class="hotel-calendar-navigation">
                                    <button type="button" class="hotel-nav-btn hotel-prev-month">
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
                                    
                                    <button type="button" class="hotel-nav-btn hotel-next-month">
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
                <button type="button" class="search-btn">Search</button>
            </form>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="hotel-booking-section">
        <div class="hotel-booking-main-layout">
            <!-- Filters Sidebar -->
            <form id="filterForm" method="GET" action="hotelBook.php">
            <div class="filters">
              <h2>Filters</h2>
              <div class="filter-group">
                <h4>Room style</h4>
                <button class="filter-button<?php if(isset($_GET['room_styles']) && in_array('Budget', $_GET['room_styles'])) echo ' selected'; ?>" type="button">Budget</button>
                <button class="filter-button<?php if(isset($_GET['room_styles']) && in_array('Family', $_GET['room_styles'])) echo ' selected'; ?>" type="button">Family</button>
                <button class="filter-button<?php if(isset($_GET['room_styles']) && in_array('Luxury', $_GET['room_styles'])) echo ' selected'; ?>" type="button">Luxury</button>
                <button class="filter-button<?php if(isset($_GET['room_styles']) && in_array('Trendy', $_GET['room_styles'])) echo ' selected'; ?>" type="button">Trendy</button>
              </div>
              <hr>
              <div class="filter-group">
                <h4>Price</h4>
                <div id="price-slider"></div>
                <div style="margin-top:8px;">
                  <span id="min-price-label"><?php echo htmlspecialchars($min_price); ?></span> - <span id="max-price-label"><?php echo htmlspecialchars($max_price); ?></span>
                </div>
                <input type="hidden" name="minPrice" id="minPrice" value="<?php echo htmlspecialchars($min_price); ?>" data-slider-min="<?php echo htmlspecialchars($slider_min_price); ?>">
                <input type="hidden" name="maxPrice" id="maxPrice" value="<?php echo htmlspecialchars($max_price); ?>" data-slider-max="<?php echo htmlspecialchars($slider_max_price); ?>">
              </div>
              <hr>
              <div class="filter-group">
                <h4>Rating</h4>
                <input type="hidden" name="rating" id="ratingInput" value="<?php echo isset($_GET['rating']) ? htmlspecialchars($_GET['rating']) : '0'; ?>">
                <div class="rating-buttons">
                  <button type="button" class="rating-btn" data-value="0">0+</button>
                  <button type="button" class="rating-btn" data-value="1">1+</button>
                  <button type="button" class="rating-btn" data-value="2">2+</button>
                  <button type="button" class="rating-btn" data-value="3">3+</button>
                  <button type="button" class="rating-btn" data-value="4">4+</button>
                </div>
              </div>
              <hr>
              <div class="filter-group">
                <h4>Freebies</h4>
                <div class="checkbox-group">
                  <label><input type="checkbox" name="freebies[]" value="Free breakfast" <?php if(isset($_GET['freebies']) && in_array('Free breakfast', $_GET['freebies'])) echo 'checked'; ?>> Free breakfast</label>
                  <label><input type="checkbox" name="freebies[]" value="Free parking" <?php if(isset($_GET['freebies']) && in_array('Free parking', $_GET['freebies'])) echo 'checked'; ?>> Free parking</label>
                  <label><input type="checkbox" name="freebies[]" value="Free internet" <?php if(isset($_GET['freebies']) && in_array('Free internet', $_GET['freebies'])) echo 'checked'; ?>> Free internet</label>
                  <label><input type="checkbox" name="freebies[]" value="Free airport shuttle" <?php if(isset($_GET['freebies']) && in_array('Free airport shuttle', $_GET['freebies'])) echo 'checked'; ?>> Free airport shuttle</label>
                </div>
              </div>
              <div class="action-buttons">
                <button type="button" class="cancel-btn" id="cancelFilters">Cancel</button>
                <button type="submit" class="apply-btn" id="applyFilters">Apply Filters</button>
              </div>
            </div>
            </form>
            
            <!-- Hotel Results -->
            <main class="hotel-booking-content">
                <div class="hotel-list hotel-list-column">
                    <?php if (empty($hotels)): ?>
                        <p>No hotels found.</p>
                    <?php else: ?>
                        <?php foreach ($hotels as $index => $hotel): ?>
                            <!-- Hotel Card -->
                            <article class="hotel-card hotel-card-wide">
                                <div class="hotel-card-img-col">
                                    <?php 
                                    $hotel_name = $hotel['name'];
                                    if (isset($hotel_images[$hotel_name])) {
                                        echo '<img src="' . htmlspecialchars($hotel_images[$hotel_name]) . '" alt="' . htmlspecialchars($hotel_name) . '" class="hotel-card-img" style="width:100%;height:auto;object-fit:cover;">';
                                    } else {
                                        echo '<img src="icon/default_hotel.jpg" alt="No image" class="hotel-card-img" style="width:100%;height:auto;object-fit:cover;">';
                                    }
                                    ?>
                                </div>
                                <div class="hotel-card-info-col">
                                    <div class="hotel-card-header-row">
                                        <div class="hotel-card-title"><?php echo $hotel['name']; ?></div>
                                        <div class="hotel-card-price-block">
                                            <span class="hotel-card-price-label">starting from</span>
                                            <span class="hotel-card-price-main">$<?php echo $hotel['min_price']; ?><span class="hotel-card-price-night">/night</span></span>
                                            <span class="hotel-card-price-tax">excl. tax</span>
                                        </div>
                                    </div>
                                    <div class="hotel-card-location-row">
                                        <img src="icon/location.svg" alt="Location" style="width:16px;height:16px;"> <?php echo $hotel['city']; ?>, Malaysia
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
                                    <!-- Recommended Filters Badges -->
                                    <div class="hotel-card-filters-row">
                                        <?php
                                        // Fetch the lowest-priced room type for this hotel
                                        $room_type_query = "SELECT rt.type_name FROM hotel_room_t hr JOIN room_type_t rt ON hr.r_type_id = rt.r_type_id WHERE hr.hotel_id = '" . $hotel['hotel_id'] . "' ORDER BY hr.price_per_night ASC LIMIT 1";
                                        $room_type_result = mysqli_query($connection, $room_type_query);
                                        $room_type_row = mysqli_fetch_assoc($room_type_result);
                                        $type_name = $room_type_row ? $room_type_row['type_name'] : '';
                                        if ($type_name && isset($room_type_filters[$type_name])) {
                                            // Only show badges if no filter is selected, or if this badge is selected
                                            foreach ($room_type_filters[$type_name] as $filter) {
                                                if ($filter === 'Budget') continue; // Skip Budget badge
                                                if (empty($selected_room_styles) || in_array($filter, $selected_room_styles)) {
                                                    echo '<span class="hotel-filter-badge">✔ ' . htmlspecialchars($filter) . '</span> ';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="hotel-card-review-row">
                                        <span class="hotel-card-review-score"><?php echo number_format($hotel['avg_rating'], 1); ?></span>
                                        <span class="hotel-card-review-label"><?php echo $hotel['review_label']; ?></span>
                                        <span class="hotel-card-review-count"><?php echo $hotel['review_count']; ?> reviews</span>
                                    </div>
                                    <hr class="hotel-card-divider">
                                    <div class="hotel-card-action-row">
                                        <button class="hotel-card-fav-btn" type="button">
                                            <img src="icon/heartHotelBook.svg" alt="Favorite" class="heart-icon">
                                        </button>
                                        <a href="hotelDetails.php?hotel_id=<?php echo $hotel['hotel_id']; ?>&checkin=<?php echo isset($_GET['checkin']) ? htmlspecialchars($_GET['checkin']) : ''; ?>&checkout=<?php echo isset($_GET['checkout']) ? htmlspecialchars($_GET['checkout']) : ''; ?>&adult=<?php echo isset($_GET['adult']) ? htmlspecialchars($_GET['adult']) : 1; ?>&child=<?php echo isset($_GET['child']) ? htmlspecialchars($_GET['child']) : 0; ?>&room=<?php echo isset($_GET['room']) ? htmlspecialchars($_GET['room']) : 1; ?>" class="hotel-card-view-btn">View Place</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination Controls -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    // Display only page numbers 1, 2, 3, 4, etc.
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $current_page) {
                            echo '<span class="pagination-current">' . $i . '</span>';
                        } else {
                            echo '<a href="?destination=' . urlencode($search_location) . '&page=' . $i . '" class="pagination-link">' . $i . '</a>';
                        }
                    }
                    ?>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </section>

    <script src="js/hotelBook.js"></script>
    
    <script>
        // Direct form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const searchBtn = document.querySelector('.search-btn');
            const searchForm = document.getElementById('hotelSearchForm');
            const destinationInput = document.getElementById('destination');
            
            if (searchBtn && searchForm) {
                searchBtn.addEventListener('click', function(e) {
                    // Submit the form directly
                    searchForm.submit();
                });
            }
            
            // Handle Enter key press in the destination input
            if (destinationInput && searchForm) {
                destinationInput.addEventListener('keypress', function(e) {
                    // Check if Enter key was pressed
                    if (e.key === 'Enter') {
                        e.preventDefault(); // Prevent default form submission
                        searchForm.submit(); // Submit the form manually
                    }
                });
            }
            
            // Check user login status from PHP session
            const userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            
            // Set login status in localStorage for JavaScript to access
            if (userLoggedIn) {
                localStorage.setItem('user_logged_in', 'true');
            } else {
                localStorage.removeItem('user_logged_in');
            }

            // Update hidden fields when dates are selected
            function updateHotelDateDisplay() {
                const hotelDateInput = document.getElementById('hotelDateInput');
                const checkinHidden = document.getElementById('checkinHidden');
                const checkoutHidden = document.getElementById('checkoutHidden');
                if (selectedCheckinDate && selectedCheckoutDate) {
                    const checkinFormatted = formatDisplayDate(selectedCheckinDate);
                    const checkoutFormatted = formatDisplayDate(selectedCheckoutDate);
                    hotelDateInput.value = `${checkinFormatted} - ${checkoutFormatted}`;
                    if (checkinHidden) checkinHidden.value = selectedCheckinDate.toISOString().slice(0,10);
                    if (checkoutHidden) checkoutHidden.value = selectedCheckoutDate.toISOString().slice(0,10);
                } else if (selectedCheckinDate) {
                    const checkinFormatted = formatDisplayDate(selectedCheckinDate);
                    hotelDateInput.value = `${checkinFormatted} - Check out?`;
                    if (checkinHidden) checkinHidden.value = selectedCheckinDate.toISOString().slice(0,10);
                    if (checkoutHidden) checkoutHidden.value = '';
                } else {
                    hotelDateInput.value = '';
                    hotelDateInput.placeholder = 'Check in - Check out';
                    if (checkinHidden) checkinHidden.value = '';
                    if (checkoutHidden) checkoutHidden.value = '';
                }
            }
        });
    </script>

    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html>


