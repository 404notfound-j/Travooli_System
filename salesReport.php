<?php
ob_start();
?>

<?php
$type = isset($_GET['type']) ? $_GET['type'] : 'flight';

if ($type === 'hotel') {
    $pageTitle = "Hotel Sales";
    $tabActive = "Hotel Sales";
    $tabInactive = "Flight Sales";
    $tabActiveLink = "sales.php?type=hotel";
    $tabInactiveLink = "sales.php?type=flight";
    $items = [
        [
            'name' => 'AAA Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM40,000'
        ],
        [
            'name' => 'BBB Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM63,000'
        ],
        [
            'name' => 'CCC Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM58,000'
        ],
        [
            'name' => 'DDD Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM90,000'
        ],
        [
            'name' => 'EEE Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM74,000'
        ],
        [
            'name' => 'FFF Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM58,000'
        ],
        [
            'name' => 'GGG Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM95,000'
        ],
        [
            'name' => 'HHH Hotel',
            'image' => 'images/grand-hotel-logo.png',
            'tickets_sold' => 120,
            'revenue' => 'RM43,000'
        ]


    ];
} else {
    $pageTitle = "Flight Sales";
    $tabActive = "Flight Sales";
    $tabInactive = "Hotel Sales";
    $tabActiveLink = "sales.php?type=flight";
    $tabInactiveLink = "sales.php?type=hotel";
    $items = [
        [ 
            'name' => 'AAA Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM63,500'
        ],
        [ 
            'name' => 'BBB Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM54,500'
        ],
        [ 
            'name' => 'CCC Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM66,500'
        ],
        [ 
            'name' => 'DDD Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM78,500'
        ],
        [ 
            'name' => 'EEE Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM84,500'
        ],
        [ 
            'name' => 'FFF Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM45,500'
        ],
        [ 
            'name' => 'GGG Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM58,500'
        ],
        [ 
            'name' => 'HHH Airlines',
            'image' => 'images/AK.png',
            'tickets_sold' => 205,
            'revenue' => 'RM74,500'
        ]
    ];
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
    <div class="filters">
        <select id="filter-type" class="filter-select">
            <option value="yearly">Yearly</option>
            <option value="monthly">Monthly</option>
            <option value="weekly">Weekly</option>
        </select>
        <select id="filter-value" class="filter-select">
            <option>Jan 2025</option>
        </select>
    </div>
</div>

<h2 class="sub-title">Detailed Report</h2>
<div class="report-chart-summary">
    <canvas id="salesBarChart" width="600" height="260"></canvas>
    <div class="summary-box">
        <div class="summary-item">
            <span>Total Ticket Sold</span> : <span class="summary-value" id="totalTickets">RM 103,950</span>
        </div>
        <div class="summary-item">
            <span>Total Revenue</span> : <span class="summary-value" id="totalRevenue">RM 103,950</span>
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
