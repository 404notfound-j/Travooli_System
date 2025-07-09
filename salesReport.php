<?php

ob_start();
?>

<?php
include 'connection.php';
$type = isset($_GET['type']) ? $_GET['type'] : 'flight';

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
            'image' => 'images/grand-hotel-logo.png', 
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
            'image' => $row['image'],
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
