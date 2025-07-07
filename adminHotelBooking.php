<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Management System</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminHotelBooking.css">
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
            <a href="#" class="nav-item active">Booking</a>
            <a href="salesReport.php" class="nav-item">Report</a>
            <a href="recordTable.php?section=payment&type=flight" class="nav-item">Payment</a>
            <a href="recordTable.php?section=refund&type=flight" class="nav-item">Refund</a>
            <a href="U_Manage.php" class="nav-item">User Management</a>
            
            <div class="pages-section">
                <div class="section-title">PAGES</div>
                <a href="#" class="nav-item">Calendar</a>
                <a href="#" class="nav-item">To-Do</a>
                <a href="#" class="nav-item">Contact</a>
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
                <div class="admin-avatar">AD</div>
                <div class="admin-info">
                    <h4>Admin User</h4>
                    <span>Admin</span>
                </div>
                <svg class="admin-dropdown-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                    <path d="M6 8.5L2.5 5H9.5L6 8.5Z"/>
                </svg>
                
                <!-- Admin Profile Dropdown -->
                <div class="admin-profile-dropdown" id="admin-profile-dropdown">
                    <div class="admin-profile-info">
                        <div class="admin-dropdown-avatar">AD</div>
                        <div class="admin-profile-details">
                            <span class="admin-dropdown-name">Admin User</span>
                            <span class="admin-email">admin@travooli.com</span>
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
            <div class="booking-container">
                <h1>Booking</h1>
                
                <div class="booking-tabs">
                    <button class="tab-button" data-tab="flight" onclick="window.location.href='adminFlightBooking.php'">Flight Booking</button>
                    <button class="tab-button active" data-tab="hotel">Hotel Booking</button>
                </div>
                
                <div class="booking-table-container">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Booking Reference</th>
                                <th>Hotel</th>
                                <th>Room Type</th>
                                <th>Amount</th>
                                <th>Booking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example row 1 -->
                            <tr class="booking-row" data-booking-id="BK001234896" id="booking-BK001234896">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234896')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234896</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234896'">Modify</button></td>
                            </tr>
                            <!-- Details panel for row 1 -->
                            <tr class="details-row">
                                <td colspan="6">
                                    <div class="details-panel" style="display: none;">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th>Guest Name</th>
                                                    <th>Nationality</th>
                                                    <th>Email</th>
                                                    <th>Phone Number</th>
                                                    <th>Check-In Date</th>
                                                    <th>Check-Out Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>John Doe</td>
                                                    <td>Malaysia</td>
                                                    <td>abc123@gmail.com</td>
                                                    <td>011-11001100</td>
                                                    <td>22-05-2025</td>
                                                    <td>23-05-2025</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Example row 2 -->
                            <tr class="booking-row" data-booking-id="BK001234897" id="booking-BK001234897">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234897')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234897</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234897'">Modify</button></td>
                            </tr>
                            <!-- Details panel for row 2 -->
                            <tr class="details-row">
                                <td colspan="6">
                                    <div class="details-panel" style="display: none;">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th>Guest Name</th>
                                                    <th>Nationality</th>
                                                    <th>Email</th>
                                                    <th>Phone Number</th>
                                                    <th>Check-In Date</th>
                                                    <th>Check-Out Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sarah Williams</td>
                                                    <td>Singapore</td>
                                                    <td>sarah@example.com</td>
                                                    <td>012-34567890</td>
                                                    <td>15-06-2025</td>
                                                    <td>18-06-2025</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Example row 3 -->
                            <tr class="booking-row" data-booking-id="BK001234898" id="booking-BK001234898">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234898')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234898</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234898'">Modify</button></td>
                            </tr>
                            
                            <!-- Example row 4 -->
                            <tr class="booking-row" data-booking-id="BK001234899" id="booking-BK001234899">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234899')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234899</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234899'">Modify</button></td>
                            </tr>
                            
                            <!-- Example row 5 -->
                            <tr class="booking-row" data-booking-id="BK001234900" id="booking-BK001234900">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234900')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234900</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234900'">Modify</button></td>
                            </tr>
                            
                            <!-- Example row 6 -->
                            <tr class="booking-row" data-booking-id="BK001234901" id="booking-BK001234901">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234901')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234901</span>
                                    </div>
                                </td>
                                <td>Emperor Hotel @ KLCC</td>
                                <td>Deluxe Room</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=BK001234901'">Modify</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <span class="pagination-info">Showing 1-06 of 36</span>
                    <div class="pagination-controls">
                        <button class="pagination-btn prev"><i class="fa fa-chevron-left"></i></button>
                        <button class="pagination-btn next"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/adminSidebar.js"></script>
    <script src="js/adminHotelBooking.js"></script>
</body>
</html> 