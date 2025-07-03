<?php 
// Start session to check login status
session_start();

include 'userHeader.php'; ?>

<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBook.css">
<link rel="stylesheet" href="css/hotelDetails.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php
include 'connection.php';

// Get hotel_id from URL, default to H0001 (The Majestic Hotel Kuala Lumpur)
$hotel_id = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : 'H0001';

// Retrieve check-in and check-out dates from GET or SESSION
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : (isset($_SESSION['checkin_date']) ? $_SESSION['checkin_date'] : date('Y-m-d'));
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : (isset($_SESSION['checkout_date']) ? $_SESSION['checkout_date'] : date('Y-m-d', strtotime('+1 day')));
$adult = isset($_GET['adult']) ? $_GET['adult'] : 1;
$child = isset($_GET['child']) ? $_GET['child'] : 0;
$room_count = isset($_GET['room']) ? $_GET['room'] : 1;

// Get hotel basic information
$hotel_query = "SELECT h.*, 
                (SELECT AVG(rating) FROM hotel_feedback_t hf WHERE hf.hotel_id = h.hotel_id) as avg_rating,
                (SELECT COUNT(*) FROM hotel_feedback_t hf WHERE hf.hotel_id = h.hotel_id) as review_count,
                (SELECT MIN(price_per_night) FROM hotel_room_t hr WHERE hr.hotel_id = h.hotel_id) as min_price
                FROM hotel_t h 
                WHERE h.hotel_id = '$hotel_id'";

$hotel_result = mysqli_query($connection, $hotel_query);
$hotel = mysqli_fetch_assoc($hotel_result);

// Calculate review label
$avg_rating = round($hotel['avg_rating'], 1);
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

// Get hotel star rating (assumed to be 5)
$stars = 5;

// Get hotel room information
$rooms_query = "SELECT hr.*, rt.type_name 
                FROM hotel_room_t hr
                JOIN room_type_t rt ON hr.r_type_id = rt.r_type_id
                WHERE hr.hotel_id = '$hotel_id'
                ORDER BY hr.price_per_night ASC";
$rooms_result = mysqli_query($connection, $rooms_query);
$rooms = [];
while ($room = mysqli_fetch_assoc($rooms_result)) {
    $rooms[] = $room;
}

// Get hotel amenities
$amenities_query = "SELECT f.freebie_id, f.name
                    FROM h_freebies_t hf
                    JOIN freebies_t f ON hf.freebie_id = f.freebie_id
                    WHERE hf.hotel_id = '$hotel_id'";
$amenities_result = mysqli_query($connection, $amenities_query);
$amenities = [];
while ($amenity = mysqli_fetch_assoc($amenities_result)) {
    $amenities[] = $amenity;
}

// Get user feedback
$feedback_query = "SELECT hf.*, c.fst_name, c.lst_name
                   FROM hotel_feedback_t hf
                   JOIN customer_t c ON hf.customer_id = c.customer_id
                   WHERE hf.hotel_id = '$hotel_id'
                   ORDER BY h_feedback_id DESC
                   LIMIT 5";
$feedback_result = mysqli_query($connection, $feedback_query);
$feedbacks = [];
while ($feedback = mysqli_fetch_assoc($feedback_result)) {
    $feedbacks[] = $feedback;
}

// Hotel images (main image from hotelBook.php mapping)
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
$main_image = isset($hotel_images[$hotel['name']]) ? $hotel_images[$hotel['name']] : 'background/hotel1_main.jpg';
$hotel_images = [$main_image];

// Collect unique room types for this hotel
$unique_types = [];
foreach ($rooms as $room) {
    if (!in_array($room['type_name'], $unique_types)) {
        $unique_types[] = $room['type_name'];
    }
}

// Add images for each unique room type in the desired order
$type_order = ['Standard Room', 'Deluxe Room', 'Executive Room', 'Junior Suite'];
foreach ($type_order as $type) {
    if (in_array($type, $unique_types) && isset($room_type_images[$type])) {
        $hotel_images[] = $room_type_images[$type];
    }
}

// Hotel description (example)
$hotel_overviews = [
    "The Majestic Hotel Kuala Lumpur" => "Located in the heart of Kuala Lumpur, The Majestic Hotel Kuala Lumpur is an iconic five-star luxury hotel that dates back to 1932. The hotel features colonial architecture and is complemented by modern facilities. With its 300 luxurious rooms and suites, spa facilities, multiple dining options, and impeccable service, it offers guests an unforgettable stay experience in Malaysia's vibrant capital city.",
    "Traders Hotel Kuala Lumpur" => "Traders Hotel Kuala Lumpur offers contemporary comfort and stunning views of the Petronas Twin Towers. With direct access to KLCC, modern amenities, and a rooftop SkyBar, it is ideal for both business and leisure travelers.",
    "Concorde Hotel Kuala Lumpur" => "Concorde Hotel Kuala Lumpur is a business-friendly hotel with a prime location on Jalan Sultan Ismail. It features modern rooms, a variety of dining options, and easy access to the city's attractions.",
    "Corus Hotel Kuala Lumpur" => "Corus Hotel Kuala Lumpur is known for its strategic location near KLCC and affordable luxury. The hotel offers comfortable rooms, a relaxing pool, and excellent service for both business and leisure guests.",
    "One World Hotel" => "One World Hotel offers luxury and convenience in the heart of Petaling Jaya, with direct access to 1 Utama Shopping Centre and a range of upscale amenities.",
    "Sunway Resort Hotel" => "Sunway Resort Hotel is a premier destination resort in Sunway City, featuring a water park, shopping mall, and world-class hospitality for families and business travelers.",
    "Hilton Petaling Jaya" => "Hilton Petaling Jaya is a renowned business hotel offering modern comfort, excellent dining, and easy access to Kuala Lumpur and Petaling Jaya attractions.",
    "Avani Sepang Goldcoast Resort" => "Avani Sepang Goldcoast Resort is famous for its overwater villas stretching into the Straits of Malacca, offering a unique tropical escape with stunning sea views.",
    "Eastern & Oriental Hotel" => "Eastern & Oriental Hotel in Penang is a heritage hotel blending colonial charm with modern luxury, located on the waterfront in George Town.",
    "Shangri-La's Rasa Sayang Resort" => "Shangri-La's Rasa Sayang Resort & Spa is a beachfront paradise in Penang, surrounded by lush gardens and offering exceptional service and facilities.",
    "G Hotel Kelawai" => "G Hotel Kelawai is a chic, contemporary hotel in Penang, known for its stylish design, rooftop bar, and proximity to shopping and dining hotspots.",
    "Lexis Suites Penang" => "Lexis Suites Penang features spacious suites with private pools and steam rooms, perfect for a relaxing family getaway by the beach.",
    "Shangri-La's Tanjung Aru Resort" => "Shangri-La's Tanjung Aru Resort & Spa in Kota Kinabalu offers luxury beachfront accommodation, a private marina, and spectacular sunset views.",
    "Sutera Harbour Resort" => "Sutera Harbour Resort is a vast integrated resort in Kota Kinabalu, offering golf, marina, spa, and a range of leisure activities for all ages.",
    "Le Meridien Kota Kinabalu" => "Le Meridien Kota Kinabalu boasts modern rooms, panoramic sea views, and a central location near the city's vibrant markets and waterfront.",
    "Hyatt Regency Kinabalu" => "Hyatt Regency Kinabalu is a luxury hotel in the heart of Kota Kinabalu, offering contemporary rooms, excellent dining, and city or sea views.",
    "The Regency Hotel Alor Setar" => "The Regency Hotel Alor Setar is a comfortable hotel in the city center, ideal for business and leisure travelers exploring Kedah.",
    "Hotel Grand Crystal" => "Hotel Grand Crystal in Alor Setar offers affordable comfort and convenient access to local attractions and government offices.",
    "The Village Resort Langkawi" => "The Village Resort Langkawi is a tranquil retreat surrounded by nature, perfect for a peaceful holiday close to Pantai Cenang.",
    "Berjaya Langkawi Resort" => "Berjaya Langkawi Resort features rainforest chalets and overwater suites, set along a beautiful private beach with plenty of activities.",
    "Hyatt Regency Kuantan Resort" => "Hyatt Regency Kuantan Resort is a beachfront resort on Teluk Cempedak, offering modern rooms, pools, and direct beach access.",
    "Swiss-Garden Resort Kuantan" => "Swiss-Garden Resort Kuantan is a family-friendly beachfront resort with spacious rooms, pools, and recreational facilities on Balok Beach.",
    "The Zenith Hotel Kuantan" => "The Zenith Hotel Kuantan is a modern business hotel in the city center, featuring stylish rooms, a convention center, and a rooftop pool.",
    "Merlin Beach Resort" => "Merlin Beach Resort offers a tropical escape with beachfront access, multiple pools, and a variety of dining options for families and couples.",
    "Primula Beach Hotel" => "Primula Beach Hotel in Kuala Terengganu offers comfortable rooms with sea views, direct beach access, and a relaxing atmosphere.",
    "Grand Puteri Hotel" => "Grand Puteri Hotel is a centrally located hotel in Kuala Terengganu, ideal for business and leisure stays with modern amenities.",
    "Duyong Marina & Resort" => "Duyong Marina & Resort is a waterfront resort in Kuala Terengganu, featuring marina views, spacious villas, and a peaceful setting.",
    "The Taaras Beach & Spa Resort" => "The Taaras Beach & Spa Resort on Redang Island is a luxury beachfront resort known for its crystal-clear waters, white sand, and exclusive villas.",
    "KSL Hotel & Resort" => "KSL Hotel & Resort in Johor Bahru is a popular family hotel with a water park, shopping mall, and comfortable rooms in a central location.",
    "DoubleTree by Hilton Johor Bahru" => "DoubleTree by Hilton Johor Bahru offers modern comfort, warm hospitality, and a convenient location near City Square and Komtar JBCC.",
    "Traders Hotel Puteri Harbour" => "Traders Hotel Puteri Harbour is a contemporary hotel in Iskandar Puteri, offering waterfront views and easy access to Legoland Malaysia.",
    "Legoland Malaysia Hotel" => "Legoland Malaysia Hotel is a themed hotel designed for families, featuring interactive Lego experiences and direct access to Legoland Park.",
    "Pullman Kuching" => "Pullman Kuching is a stylish hotel in the heart of Kuching, offering modern rooms, an outdoor pool, and proximity to the city's attractions.",
    "Hilton Kuching" => "Hilton Kuching is a riverside hotel with panoramic views of the Sarawak River, modern amenities, and excellent dining options.",
    "The Waterfront Hotel" => "The Waterfront Hotel in Kuching offers contemporary comfort, a rooftop pool, and a prime location next to the Sarawak River and Plaza Merdeka.",
    "Borneo Highlands Resort" => "Borneo Highlands Resort is a mountain retreat near Kuching, surrounded by rainforest and offering eco-friendly activities and wellness programs."
];

// Room images by type
$room_type_images = [
    'Standard Room' => 'https://webbox.imgix.net/images/xuqskkzainnhanwf/31032098-62c3-4e0a-8d87-b03e4c92b62a.jpg?auto=format,compress&fit=crop&crop=entropy',
    'Deluxe Room' => 'https://image-tc.galaxy.tf/wijpeg-dghgmolkfh6t706p9wohdl5ow/testing-100.jpg',
    'Executive Room' => 'https://image-tc.galaxy.tf/wijpeg-7h6926mgoerzqfe1ytj1zfkl5/sprh-deluxe-executive-room-king-02.jpg',
    'Junior Suite' => 'https://d2e5ushqwiltxm.cloudfront.net/wp-content/uploads/sites/133/2018/11/17102807/Prestige-Suite1.jpg'
];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - Hotel Details</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/hotelBook.css">
    <link rel="stylesheet" href="css/hotelDetails.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<div class="hotel-details-dark-bg">
    <div class="hotel-details-container">
        <div class="hotel-details-header-row">
            <div class="hotel-details-header-left">
                <div class="hotel-details-title-row">
                    <h1 class="hotel-details-title"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    <div class="hotel-details-stars">
                        <span class="hotel-card-stars">
                            <?php for ($i = 0; $i < $stars; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="hotel-details-star-label"><?php echo $stars; ?> Star Hotel</span>
                    </div>
                </div>
                <div class="hotel-details-location">
                    <img src="icon/locationWhite.svg" alt="Location" style="width:18px;vertical-align:middle;"> 
                    <?php echo htmlspecialchars($hotel['address']); ?>
                </div>
                <div style="height: 40px;"></div>
                <div class="hotel-details-review-box">
                    <div class="hotel-details-review-score"><?php echo $avg_rating; ?></div>
                    <div class="hotel-details-review-text"><b><?php echo $review_label; ?></b> <?php echo $hotel['review_count']; ?> reviews</div>
                </div>
            </div>
            <div class="hotel-details-header-right">
                <div class="hotel-details-price-block">
                    <span class="hotel-details-price-main">$<?php echo $hotel['min_price']; ?><span class="hotel-details-price-night">/night</span></span>
                </div>
                <div class="hotel-details-action-buttons">
                    <button class="hotel-card-fav-btn" type="button">
                        <object data="icon/heartHotelDetails.svg" type="image/svg+xml" class="heart-icon"></object>
                    </button>
                    <button class="hotel-card-share-btn" type="button">
                        <object data="icon/share.svg" type="image/svg+xml" class="share-icon"></object>
                    </button>
                    <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&r_type_id=RT001&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>&adult=<?php echo $adult; ?>&child=<?php echo $child; ?>&room=<?php echo $room_count; ?>" class="hotel-details-book-btn" onclick="return handleBookNowClick(event, this.href)">Book now</a>
                </div>
            </div>
        </div>
        <div class="hotel-details-gallery-flex">
            <div class="hotel-details-gallery-main" style="width:100%;">
                <img src="<?php echo $hotel_images[0]; ?>" alt="Main image" class="hotel-details-main-image" style="width:100%;height:auto;object-fit:cover;">
            </div>
        </div>
        <hr class="hotel-details-divider">
        <div class="hotel-details-overview-block">
            <h2>Overview</h2>
            <p><?php echo htmlspecialchars($hotel_overviews[$hotel['name']] ?? "No overview available for this hotel."); ?></p>
        </div>
        <div class="hotel-details-review-amenities-row">
            <div class="hotel-details-amenity-review-box">
                <div class="hotel-details-amenity-review-score"><?php echo $avg_rating; ?></div>
                <div class="hotel-details-amenity-review-text">
                    <?php echo $review_label; ?><br>
                    <?php echo $hotel['review_count']; ?> reviews
                </div>
            </div>
            <?php foreach ($amenities as $amenity): ?>
                <div class="hotel-details-amenity-card">
                    <?php 
                    // Map freebie_id to icon file, fallback to stars.svg
                    $icon_file = 'icon/' . strtolower($amenity['freebie_id']) . '.svg';
                    if (!file_exists($icon_file)) {
                        $icon_file = 'icon/stars.svg';
                    }
                    ?>
                    <img src="<?php echo $icon_file; ?>" alt="Amenity" class="hotel-details-amenity-icon">
                    <span><?php echo htmlspecialchars($amenity['name']); ?></span>
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
            <?php 
            foreach ($rooms as $index => $room): 
                $room_img = isset($room_type_images[$room['type_name']]) ? $room_type_images[$room['type_name']] : $main_image;
                // Concise room descriptions (6-10 words)
                $room_desc = '';
                switch ($room['type_name']) {
                    case 'Standard Room':
                        $room_desc = 'Comfortable, affordable, double or twin beds.';
                        break;
                    case 'Deluxe Room':
                        $room_desc = 'Spacious, upgraded amenities, premium bedding.';
                        break;
                    case 'Executive Room':
                        $room_desc = 'Elegant, extra space, luxury beds, exclusive perks.';
                        break;
                    case 'Junior Suite':
                        $room_desc = 'Suite with living area, deluxe beds, stylish comfort.';
                        break;
                    default:
                        $room_desc = '';
                }
            ?>
            <div class="hotel-details-room-row">
                    <img src="<?php echo $room_img; ?>" alt="Room" class="hotel-details-room-img">
                    <div class="hotel-details-room-desc">
                        <b><?php echo $room['type_name']; ?></b><br>
                        <?php echo $room_desc; ?>
                    </div>
                    <div class="hotel-details-room-price">$<?php echo $room['price_per_night']; ?><span class="hotel-details-room-night">/night</span></div>
                    <a href="hotelBookInfo.php?hotel_id=<?php echo $hotel_id; ?>&r_type_id=<?php echo $room['r_type_id']; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>&adult=<?php echo $adult; ?>&child=<?php echo $child; ?>&room=<?php echo $room_count; ?>" class="hotel-details-book-btn" onclick="return handleBookNowClick(event, this.href)">Book now</a>
                    <!-- DEBUG: room_count=<?php echo $room_count; ?> -->
            </div>
                <?php if ($index < count($rooms) - 1): ?>
            <hr class="hotel-details-divider2">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <hr class="hotel-details-divider">
        <h2 class="hotel-details-reviews-title">Reviews</h2>
        
        <!-- Reviews Header -->
        <div class="reviews-header">
            <div class="rating-display">
                <span class="rating-score"><?php echo $avg_rating; ?></span>
                <div class="rating-details">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $avg_rating): ?>
                        <i class="fas fa-star"></i>
                            <?php else: ?>
                        <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-label"><?php echo $review_label; ?></span>
                </div>
            </div>
        </div>
        
        <!-- Reviews List -->
        <div class="reviews-list">
            <?php 
            // Display database reviews
            foreach ($feedbacks as $index => $feedback):
                // Generate star rating HTML
                $starsHtml = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $feedback['rating']) {
                        $starsHtml .= '<i class="fas fa-star"></i>';
                    } else {
                        $starsHtml .= '<i class="far fa-star"></i>';
                    }
                }
                ?>
                <!-- User Review -->
                <div class="review-item">
                    <div class="review-content">
                        <img src="icon/profile.svg" alt="<?php echo htmlspecialchars($feedback['fst_name'] . ' ' . $feedback['lst_name']); ?>" class="user-avatar">
                        <div class="review-text">
                            <div class="review-header">
                                <span class="user-name"><?php echo htmlspecialchars($feedback['fst_name'] . ' ' . $feedback['lst_name']); ?></span>
                                <span class="separator">|</span>
                                <div class="review-rating">
                                    <?php echo $starsHtml; ?>
                                </div>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($feedback['feedback']); ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($index < count($feedbacks) - 1): ?>
                    <div class="review-divider"></div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if (empty($feedbacks)): ?>
                <p>No reviews available for this hotel yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'u_footer_2.php'; ?>
<script>
    window.userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
<script src="js/hotelDetails.js"></script>
<script src="js/loginReminder.js"></script>
</body>
</html>
