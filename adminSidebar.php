<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signIn.php");
    exit();
}

$adminId = $_SESSION['user_id'];
$adminData = null;

// Fetch admin data from database including profile picture
$query = "SELECT admin_id, fst_name, lst_name, email_address, phone_no, country, gender, profile_pic FROM admin_detail_t WHERE admin_id = ?";
$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $adminId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $adminData = mysqli_fetch_assoc($result);
    } else {
        // Admin data not found, redirect to login
        header("Location: signIn.php");
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    // Database error, redirect to login
    header("Location: signIn.php");
    exit();
}

// Generate admin initials
$initials = strtoupper(substr($adminData['fst_name'], 0, 1) . substr($adminData['lst_name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Management System</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminSidebar.css">

</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="icon/Travooli logo.svg" alt="Travooli" class="logo-img">
            <span>Management System</span>
        </div>
        
        <nav class="nav-menu">
            <a href="A_dashboard.php" class="nav-item">Dashboard</a>
            <a href="#" class="nav-item">Flight</a>
            <a href="#" class="nav-item">Booking</a>
            <a href="salesReport.php" class="nav-item">Report</a>
            <a href="recordTable.php?section=payment&type=flight" class="nav-item">Payment</a>
            <a href="recordTable.php?section=refund&type=flight" class="nav-item">Refund</a>
            <a href="U_Manage.php" class="nav-item">User Management</a>
            
            <div class="pages-section">
                <div class="section-title">PAGES</div>
                <a href="#" class="nav-item">Calendar</a>
                <a href="#" class="nav-item">To-Do</a>
                <a href="contact.php" class="nav-item">Contact</a>
                <a href="#" class="nav-item">Receipt & Ticket</a>
                <a href="#" class="nav-item">Team</a>
            </div>
        </nav>
        
        <div class="footer">
            <a href="logout.php" class="logout-btn">Log Out</a>
            <div class="copyright">
                Travooli Management System © 2025.<br>
                All Rights Reserved.
            </div>
        </div>
    </div>
    <div class="main-content">
        <header class="header">
            <div class="admin-profile-container" id="admin-profile-container">
                <?php if (!empty($adminData['profile_pic'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($adminData['profile_pic']) ?>" alt="Profile" class="admin-profile-picture">
                <?php else: ?>
                    <div class="admin-avatar"><?= $initials ?></div>
                <?php endif; ?>
                <div class="admin-info">
                    <h4><?= htmlspecialchars($adminData['fst_name'] . ' ' . $adminData['lst_name']) ?></h4>
                    <span>Admin</span>
                </div>
                <svg class="admin-dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M6 8.5L2.5 5H9.5L6 8.5Z"/>
                </svg>
                
                <!-- Admin Profile Dropdown -->
                <div class="admin-profile-dropdown" id="admin-profile-dropdown">
                    <div class="admin-profile-info">
                        <?php if (!empty($adminData['profile_pic'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($adminData['profile_pic']) ?>" alt="Profile" class="admin-dropdown-pic">
                        <?php else: ?>
                            <div class="admin-dropdown-avatar"><?= $initials ?></div>
                        <?php endif; ?>
                        <div class="admin-profile-details">
                            <span class="admin-dropdown-name"><?= htmlspecialchars($adminData['fst_name'] . ' ' . $adminData['lst_name']) ?></span>
                            <span class="admin-email"><?= htmlspecialchars($adminData['email_address']) ?></span>
                        </div>
                    </div>
                    <div class="admin-dropdown-divider"></div>
                    <ul class="admin-dropdown-menu">
                        <li><a href="admin_profile.php">My Profile</a></li>
                        <li class="admin-logout-item"><a href="logout.php">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Main Content Area -->
        <main class="admin-main-content">
            <?php
            // Content will be included here by the page that includes this sidebar
            // Pages should define $pageContent or use output buffering
            if (isset($pageContent)) {
                echo $pageContent;
            }
            ?>
        </main>
    </div>
    <script src="js/adminSidebar.js"></script>
    <?php
    // Include page-specific JavaScript based on current page
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage === 'admin_profile.php') {
        echo '<script src="js/admin_profile.js"></script>';
    }
    ?>
</body>
</html>