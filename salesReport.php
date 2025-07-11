<?php

ob_start();
?>

<?php
include 'connection.php';
$type = isset($_GET['type']) ? $_GET['type'] : 'flight';

$flight_image=[
    "AirAsia" => "images/AK.png",
    "FireFly Airlines" => "images/FY.png",
    "Malaysia Airlines" => "images/MH.png"
];

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

if ($type === 'hotel') {
    $pageTitle = "Hotel Sales";
    $tabActive = "Hotel Sales";
    $tabInactive = "Flight Sales";
    $tabActiveLink = "sales.php?type=hotel";
    $tabInactiveLink = "sales.php?type=flight";

    $items = [];
    $sql = "
        SELECT 
            h.hotel_id, 
            h.name, 
            COUNT(DISTINCT b.h_book_id) AS tickets_sold, 
            COALESCE(SUM(p.amount), 0) AS revenue
        FROM hotel_t h
        LEFT JOIN hotel_booking_t b ON h.hotel_id = b.hotel_id
        LEFT JOIN hotel_payment_t p ON b.h_book_id = p.h_book_id AND p.status = 'Paid'
        GROUP BY h.hotel_id, h.name
    ";
    $result = mysqli_query($connection, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'name' => $row['name'],
            'image' => isset($hotel_images[$row['name']]) ? $hotel_images[$row['name']] : 'images/grand-hotel-logo.png',
            'tickets_sold' => (int)$row['tickets_sold'],
            'revenue' => 'RM' . number_format($row['revenue'], 2)
        ];
    }

    $totalTickets = 0;
    $totalRevenue = 0;
    foreach ($items as $item) {
        $totalTickets += $item['tickets_sold'];
        $totalRevenue += floatval(str_replace(['RM', ','], '', $item['revenue']));
    }
} else {
    $pageTitle = "Flight Sales";
    $tabActive = "Flight Sales";
    $tabInactive = "Hotel Sales";
    $tabActiveLink = "sales.php?type=flight";
    $tabInactiveLink = "sales.php?type=hotel";

    $items = [];
    $sql = "
        SELECT 
            a.airline_name AS name,
            'images/AK.png' AS image,
            COUNT(DISTINCT b.f_book_id) AS tickets_sold,
            COALESCE(SUM(p.amount), 0) AS revenue
        FROM airline_t a
        LEFT JOIN flight_info_t f ON a.airline_id = f.airline_id
        LEFT JOIN flight_booking_t b ON f.flight_id = b.flight_id AND b.status = 'confirmed'
        LEFT JOIN flight_payment_t p ON b.f_book_id = p.f_book_id
        GROUP BY a.airline_id, a.airline_name
    ";
    $result = mysqli_query($connection, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'name' => $row['name'],
            'image' => isset($flight_image[$row['name']]) ? $flight_image[$row['name']] : 'images/AK.png',
            'tickets_sold' => (int)$row['tickets_sold'],
            'revenue' => 'RM' . number_format($row['revenue'], 2)
        ];
    }

    $totalTickets = 0;
    $totalRevenue = 0;
    foreach ($items as $item) {
        $totalTickets += $item['tickets_sold'];
        $totalRevenue += floatval(str_replace(['RM', ','], '', $item['revenue']));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Sales</title>
    <link rel="stylesheet" href="css/salesReport.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <h1 class="page-title">Report</h1>
    <div class="tabs-and-filters">
        <div class="tabs">
            <a href="salesReport.php?type=flight" class="tab<?php echo ($type === 'flight') ? ' active' : ''; ?>">Flight Sales</a>
            <a href="salesReport.php?type=hotel" class="tab<?php echo ($type === 'hotel') ? ' active' : ''; ?>">Hotel Sales</a>
        </div>
    </div>

    <h2 class="sub-title">Detailed Report</h2>
    <div class="report-chart-summary">
        <div class="chart-scroll-xy">
            <canvas id="salesBarChart" width="650" height="350"></canvas>
        </div>
        <div class="summary-box">
            <div class="summary-item">
                <span>Total Ticket Sold</span> : <span class="summary-value" id="totalTickets"><?php echo $totalTickets; ?></span>
            </div>
            <div class="summary-item">
                <span>Total Revenue</span> : <span class="summary-value" id="totalRevenue">RM <?php echo number_format($totalRevenue, 2); ?></span>
            </div>
        </div>
    </div>
    <script>
        //pass data to js
        const salesData = <?php echo json_encode($items); ?>;
        const salesType = <?php echo json_encode($type); ?>; //flight or hotel
    </script>

    <script src="js/salesReport.js"></script>

    <?php
    include 'salesBoxes.php';
    ?>

    <?php
    // Capture the content and store it in a variable
    $pageContent = ob_get_clean();

    // Include the admin sidebar layout
    include 'adminSidebar.php';
    ?>
</body>
</html>
