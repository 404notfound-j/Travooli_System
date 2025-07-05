<?php
// Start session to check login status
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to sign in if not logged in
    error_log("No user session in noFlightBooking.php - redirecting to signIn.php");
    header("Location: signIn.php");
    exit();
} else {
    error_log("User session found in noFlightBooking.php - User ID: " . $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - My Reservations</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Nunito+Sans:wght@400;600;700&family=Montserrat:wght@500;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/noBooking.css">
</head>
<body>
    <header>        
        <?php include 'userHeader.php';?>
    </header>
    
    <main class="container">
        <div class="no-booking-wrapper">
            <div class="no-booking-content">
                <div class="no-booking-image">
                    <img src="images/noBooking.png" alt="Flight attendants representing travel services" loading="lazy">
                </div>
                <div class="no-booking-text-content">
                    <h1 class="no-booking-title">You don't have any flight bookings or we can't access your flight bookings at this time. Please check back later or contact support if you need assistance with your bookings.</h1>
                </div>
            </div>
            <div class="cta-section">
                <a href="U_dashboard.php" class="cta-button">Search Flights</a>
            </div>
        </div>
    </main>
    
    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html> 