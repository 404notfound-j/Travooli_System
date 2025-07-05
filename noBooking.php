<?php
// Start session to check login status
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to sign in if not logged in
    error_log("No user session in noBooking.php - redirecting to signIn.php");
    header("Location: signIn.php");
    exit();
} else {
    error_log("User session found in noBooking.php - User ID: " . $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - My Reservations</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Nunito+Sans:wght@400;600;700&family=Montserrat:wght@500;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple-blue: #605dec;
            --blackish-green: #112211;
            --grey-100: #fafafa;
            --grey-400: #7c8db0;
        }

        body {
            font-family: 'Nunito Sans', Helvetica, sans-serif;
            background-color: #031e2f;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            text-align: center;
        }

        .no-booking-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 4rem 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .no-booking-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--purple-blue), #8b7ef7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .no-booking-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--purple-blue);
            margin-bottom: 1rem;
        }

        .no-booking-subtitle {
            font-size: 1.2rem;
            color: #e6e8e9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .booking-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .booking-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            color: var(--blackish-green);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(96, 93, 236, 0.3);
            text-decoration: none;
            color: var(--blackish-green);
        }

        .booking-card-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--purple-blue), #8b7ef7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .booking-card-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--purple-blue);
        }

        .booking-card-description {
            font-size: 1rem;
            color: var(--grey-400);
            line-height: 1.5;
        }

        .cta-section {
            margin-top: 3rem;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--purple-blue), #8b7ef7);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(96, 93, 236, 0.4);
            text-decoration: none;
            color: white;
        }

        .cta-secondary {
            background: transparent;
            border: 2px solid var(--purple-blue);
            color: var(--purple-blue);
        }

        .cta-secondary:hover {
            background: var(--purple-blue);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 2rem 1rem;
            }
            
            .no-booking-content {
                padding: 2rem 1rem;
            }
            
            .no-booking-title {
                font-size: 2rem;
            }
            
            .booking-options {
                grid-template-columns: 1fr;
            }
            
            .cta-button {
                display: block;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <header>        
        <?php include 'userHeader.php';?>
    </header>
    
    <main class="container">
        <div class="no-booking-content">
            <div class="no-booking-icon">
                ✈️
            </div>
            
            <h1 class="no-booking-title">No Reservations Yet</h1>
            <p class="no-booking-subtitle">
                You haven't made any bookings yet. Start your journey with Travooli!<br>
                Discover amazing destinations and book your perfect trip.
            </p>
            
            <div class="booking-options">
                <a href="U_dashboard.php" class="booking-card">
                    <div class="booking-card-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="booking-card-title">Book Flights</h3>
                    <p class="booking-card-description">
                        Find and book domestic and international flights at the best prices. 
                        Compare airlines and choose your perfect journey.
                    </p>
                </a>
                
                <a href="hotelBook.php" class="booking-card">
                    <div class="booking-card-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V6H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>
                        </svg>
                    </div>
                    <h3 class="booking-card-title">Book Hotels</h3>
                    <p class="booking-card-description">
                        Discover comfortable accommodations worldwide. From luxury resorts 
                        to budget-friendly stays, find your perfect home away from home.
                    </p>
                </a>
            </div>
            
            <div class="cta-section">
                <a href="U_dashboard.php" class="cta-button">Start Booking Flights</a>
                <a href="hotelBook.php" class="cta-button cta-secondary">Browse Hotels</a>
            </div>
        </div>
    </main>
    
    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html> 