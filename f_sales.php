<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Sales</title>
    <link rel="stylesheet" href="css/f_sales.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php
// Start output buffering to capture the page content
ob_start();

?>

<h1 class="page-title">Report</h1>
<div class="tabs-and-filters">
    <div class="tabs">
        <a href="f_sales.php" class="tab active">Flight Sales</a>
        <a href="#" class="tab">Hotel Sales</a>
    </div>
    <div class="filters">
        <select id="filter-type" class="filter-select">
            <option value="yearly">Yearly</option>
            <option value="monthly">Monthly</option>
            <option value="weekly">Weekly</option>
            <option value="daily">Daily</option>
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

<script src="js/f_sales.js"></script>

<?php
// Capture the content and store it in a variable
$pageContent = ob_get_clean();

// Include the admin sidebar layout
include 'adminSidebar.php';
?>

</body>
</html>