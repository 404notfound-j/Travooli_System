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
        
        <div class="dashboard-charts sales-details-section">
            <div class="sales-chart-container">
                <h2 class="sub-title">Sales Details</h2>
                <canvas id="salesBarChart" width="600" height="260" class="bar-chart-canvas"></canvas>
            </div>
            <div class="summary-box">
                <div class="summary-item">
                    <span>Total Ticket Sold</span> : <span class="summary-value" id="totalTickets">RM 103,950</span>
                </div>
                <div class="summary-item">
                    <span>Total Revenue</span> : <span class="summary-value" id="totalRevenue">RM 103,950</span>
                </div>
            </div>
        </div>

        <!-- Second Sales Details Section (Reversed) -->
        <div class="dashboard-charts sales-details-section sales-details-section--reverse">
            <div class="summary-box">
                <div class="summary-item">
                    <span>Total Ticket Sold</span> : <span class="summary-value" id="totalTickets2">RM 88,000</span>
                </div>
                <div class="summary-item">
                    <span>Total Revenue</span> : <span class="summary-value" id="totalRevenue2">RM 88,000</span>
                </div>
            </div>
            <div class="sales-chart-container">
                <h2 class="sub-title">Sales Details (Right Chart)</h2>
                <canvas id="salesBarChart2" width="600" height="260" class="bar-chart-canvas"></canvas>
            </div>
        </div>
    </div>
</div>


<?php
// Example sales data for the chart (replace with real data as needed)
$salesData = [
    ['name' => 'January', 'revenue' => 'RM60000'],
    ['name' => 'February', 'revenue' => 'RM35000'],
    ['name' => 'March', 'revenue' => 'RM50000'],
    ['name' => 'April', 'revenue' => 'RM42000'],
    ['name' => 'May', 'revenue' => 'RM48000'],
    ['name' => 'June', 'revenue' => 'RM30000'],
    ['name' => 'July', 'revenue' => 'RM70000'],
    ['name' => 'August', 'revenue' => 'RM55000'],
    ['name' => 'September', 'revenue' => 'RM40000'],
    ['name' => 'October', 'revenue' => 'RM80000'],
    ['name' => 'November', 'revenue' => 'RM60000'],
    ['name' => 'December', 'revenue' => 'RM65000'],
];
?>
<script>
window.salesData = <?php echo json_encode($salesData); ?>;
</script>

<?php
// Example sales data for the second chart
$salesData2 = [
    ['name' => 'Jan', 'revenue' => 'RM20000'],
    ['name' => 'Feb', 'revenue' => 'RM25000'],
    ['name' => 'Mar', 'revenue' => 'RM30000'],
    ['name' => 'Apr', 'revenue' => 'RM35000'],
    ['name' => 'May', 'revenue' => 'RM40000'],
    ['name' => 'Jun', 'revenue' => 'RM45000'],
    ['name' => 'Jul', 'revenue' => 'RM50000'],
    ['name' => 'Aug', 'revenue' => 'RM55000'],
    ['name' => 'Sep', 'revenue' => 'RM60000'],
    ['name' => 'Oct', 'revenue' => 'RM65000'],
    ['name' => 'Nov', 'revenue' => 'RM70000'],
    ['name' => 'Dec', 'revenue' => 'RM75000'],
];
?>
<script>
window.salesData2 = <?php echo json_encode($salesData2); ?>;
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