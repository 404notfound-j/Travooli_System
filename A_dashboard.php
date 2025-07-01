<?php
// Start output buffering to capture the page content
ob_start();
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome to the Travooli Management System</p>
    </div>
    
    <div class="dashboard-content">
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Total Users</h3>
                <div class="card-value">1,234</div>
            </div>
            
            <div class="dashboard-card">
                <h3>Total Bookings</h3>
                <div class="card-value">567</div>
            </div>
            
            <div class="dashboard-card">
                <h3>Revenue</h3>
                <div class="card-value">$45,678</div>
            </div>
            
            <div class="dashboard-card">
                <h3>Active Flights</h3>
                <div class="card-value">89</div>
            </div>
        </div>
        
        <div class="dashboard-charts">
            <div class="chart-container">
                <h3>Recent Activity</h3>
                <p>Dashboard charts and analytics would go here...</p>
            </div>
        </div>
    </div>
</div>

<?php
// Capture the content and store it in a variable
$pageContent = ob_get_clean();

// Include the admin sidebar layout
include 'adminSidebar.php';
?>