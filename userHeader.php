<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli</title>
    <link rel="stylesheet" href="css/userHeader.css">
</head>
<body>

<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Check if user is logged in and get user data
$isLoggedIn = false;
$userData = null;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Fetch user data from database
    $query = "SELECT user_id, fst_name, lst_name, email_address, phone_no, country FROM user_detail_t WHERE user_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $userData = mysqli_fetch_assoc($result);
            $isLoggedIn = true;
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<?php if (!$isLoggedIn): ?>
<!-- Header for logged-out users (original) -->
<header class="header">
    <div class="header-container">
        <div class="logo-container">
            <img src="icon/Travooli logo.svg" alt="Travooli" class="logo">
        </div>
        
        <nav class="nav">
            <ul class="nav-list" id="nav-menu">
                <li class="nav-item active"><a href="U_dashboard.php">Flights</a></li>
                <li class="nav-item"><a href="#hotels">Hotels</a></li>
                <li class="nav-item"><a href="#notifications">Notifications</a></li>
                <li class="nav-item"><a href="#tickets">My Tickets</a></li>
                <li class="nav-item"><a href="#reservations">My Reservations</a></li>
                <li class="nav-item"><a href="signIn.php">Sign in</a></li>
                <li class="nav-item"><a href="signUp.php" class="btn btn-primary">Sign up</a></li>
            </ul>
        </nav>
        
        <!-- Hamburger Menu for Mobile -->
        <div class="hamburger" id="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</header>

<?php else: ?>
<!-- Header for logged-in users (with profile) -->
<header class="header">
    <div class="header-container">
        <div class="logo-container">
            <img src="icon/Travooli logo.svg" alt="Travooli" class="logo">
        </div>
        
        <nav class="nav">
            <ul class="nav-list" id="nav-menu">
                <li class="nav-item active"><a href="U_dashboard.php">Flights</a></li>
                <li class="nav-item"><a href="#hotels">Hotels</a></li>
                <li class="nav-item"><a href="#notifications">Notifications</a></li>
                <li class="nav-item"><a href="#tickets">My Tickets</a></li>
                <li class="nav-item"><a href="#reservations">My Reservations</a></li>
                
                <!-- Profile Section -->
                <li class="nav-item profile-item">
                    <div class="profile-container" id="profile-container">
                        <img src="https://i.pinimg.com/736x/fe/ca/35/feca353a62bc5974bc699ec41b9cebcc.jpg" alt="Profile" class="profile-picture">
                        <span class="profile-name"><?= htmlspecialchars($userData['fst_name'] . ' ' . $userData['lst_name']) ?></span>
                        <svg class="dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M6 8.5L2.5 5H9.5L6 8.5Z"/>
                        </svg>
                    </div>
                    
                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown" id="profile-dropdown">
                        <div class="profile-info">
                            <img src="https://i.pinimg.com/736x/fe/ca/35/feca353a62bc5974bc699ec41b9cebcc.jpg" alt="Profile" class="profile-dropdown-pic">
                            <div class="profile-details">
                                <span class="profile-dropdown-name"><?= htmlspecialchars($userData['fst_name'] . ' ' . $userData['lst_name']) ?></span>
                                <span class="profile-email"><?= htmlspecialchars($userData['email_address']) ?></span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">My Profile</a></li>
                            <li class="logout-item"><a href="logout.php">Sign Out</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
        
        <!-- Hamburger Menu for Mobile -->
        <div class="hamburger" id="hamburger">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>
</header>
<?php endif; ?>

<script src="js/header.js"></script>
</body>
</html>


