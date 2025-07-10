<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travooli</title>
    <link rel="stylesheet" href="css/A_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php
// Start output buffering to capture the page content
ob_start();

include 'connection.php';

// Total Users
$userResult = mysqli_query($connection, "SELECT COUNT(*) as total_users FROM user_detail_t");
$userRow = mysqli_fetch_assoc($userResult);
$totalUsers = $userRow['total_users'];

// Total Flight Bookings
$flightResult = mysqli_query($connection, "SELECT COUNT(*) as total_flights FROM flight_booking_t WHERE status = 'Confirmed'");
$flightRow = mysqli_fetch_assoc($flightResult);
$totalFlights = $flightRow['total_flights'];

// Total Hotel Bookings
$hotelResult = mysqli_query($connection, "SELECT COUNT(*) as total_hotels FROM hotel_booking_t");
$hotelRow = mysqli_fetch_assoc($hotelResult);
$totalHotels = $hotelRow['total_hotels'];

// Total Revenue
$revenueResult = mysqli_query($connection, "SELECT (SELECT COALESCE(SUM(amount), 0) FROM flight_payment_t WHERE payment_status = 'Paid') + (SELECT COALESCE(SUM(amount), 0) FROM hotel_payment_t WHERE status = 'Paid') as total_revenue");
$revenueRow = mysqli_fetch_assoc($revenueResult);
$totalRevenue = $revenueRow['total_revenue'];

// Get analytics data for different time periods
$analyticsData = [
    'daily' => []
];

// Get last 30 days of daily data
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dateLabel = date('M d', strtotime("-$i days"));
    
    // Flight revenue for this date (using booking_date)
    $flightQuery = "SELECT COALESCE(SUM(fp.amount), 0) as flight_revenue 
                    FROM flight_payment_t fp 
                    JOIN flight_booking_t fb ON fp.f_book_id = fb.flight_booking_id 
                    WHERE DATE(fb.booking_date) = '$date' AND fp.payment_status = 'Paid'";
    $flightResult = mysqli_query($connection, $flightQuery);
    $flightRevenue = mysqli_fetch_assoc($flightResult)['flight_revenue'];
    
    // Hotel revenue for this date (using payment_date)
    $hotelQuery = "SELECT COALESCE(SUM(hp.amount), 0) as hotel_revenue 
                   FROM hotel_payment_t hp 
                   WHERE DATE(hp.payment_date) = '$date' AND hp.status = 'Paid'";
    $hotelResult = mysqli_query($connection, $hotelQuery);
    $hotelRevenue = mysqli_fetch_assoc($hotelResult)['hotel_revenue'];
    
    $analyticsData['daily'][] = [
        'date' => $date,
        'label' => $dateLabel,
        'flight_revenue' => (float)$flightRevenue,
        'hotel_revenue' => (float)$hotelRevenue
    ];
}

// Calculate totals for different periods
$last7DaysData = array_slice($analyticsData['daily'], -7);
$totalFlightRevenue = array_sum(array_column($last7DaysData, 'flight_revenue'));
$totalHotelRevenue = array_sum(array_column($last7DaysData, 'hotel_revenue'));
$totalWeekRevenue = $totalFlightRevenue + $totalHotelRevenue;
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the Travooli Management System</p>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="card-icon"><img src="icon/aPerson.svg" alt="Total User" width="60" height="60"></div>
                <div class="card-title">Total User</div>
                <div class="card-value"><?php echo $totalUsers; ?></div>
                <div class="card-subtitle">Track user activity</div>
            </div>
            <div class="dashboard-card">
                <div class="card-icon"><img src="icon/aBook.svg" alt="Total Flight Booked" width="60" height="60"></div>
                <div class="card-title">Total Flight Booked</div>
                <div class="card-value"><?php echo $totalFlights; ?></div>
                <div class="card-subtitle">Overview of total flight bookings</div>
            </div>
            <div class="dashboard-card">
                <div class="card-icon"><img src="icon/aSales.svg" alt="Total Hotel Booked" width="60" height="60"></div>
                <div class="card-title">Total Hotel Booked</div>
                <div class="card-value"><?php echo $totalHotels; ?></div>
                <div class="card-subtitle">Monitor total hotel bookings</div>
            </div>
            <div class="dashboard-card">
                <div class="card-icon"><img src="icon/aTime.svg" alt="Total Revenue" width="60" height="60"></div>
                <div class="card-title">Total Revenue</div>
                <div class="card-value">RM <?php echo number_format($totalRevenue, 2); ?></div>
                <div class="card-subtitle">Combined flight & hotel revenue</div>
            </div>
        </div>
        
        <!-- Revenue Analytics Chart -->
        <div class="analytics-section">
            <div class="analytics-header">
                <h2>Your Analytics</h2>
                <div class="time-filter">
                    <span class="filter-btn active">7d</span>
                    <span class="filter-btn">30d</span>
                    <span class="filter-btn-static" id="dateRange"><?php echo date('M d', strtotime('-6 days')) . ' - ' . date('M d'); ?></span>
                </div>
            </div>
            
            <div class="analytics-stats">
                <div class="stat-item">
                    <div class="stat-value">RM <?php echo number_format($totalWeekRevenue, 0); ?></div>
                    <div class="stat-label">Total Revenue (7 days)</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">RM <?php echo number_format($totalFlightRevenue, 0); ?></div>
                    <div class="stat-label">Flight Revenue</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">RM <?php echo number_format($totalHotelRevenue, 0); ?></div>
                    <div class="stat-label">Hotel Revenue</div>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="revenueChart" width="800" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
// Pass analytics data to JavaScript
window.analyticsData = <?php echo json_encode($analyticsData); ?>;
</script>

<?php
// Capture the content and store it in a variable
$pageContent = ob_get_clean();

// Include the admin sidebar layout
include 'adminSidebar.php';
?>

<script src="js/A_dashboard.js"></script>
</body>
</html>
l