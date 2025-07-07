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
    <link rel="stylesheet" href="css/adminFlightBooking.css">
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
                    <button class="tab-button active" data-tab="flight">Flight Booking</button>
                    <button class="tab-button" data-tab="hotel" onclick="window.location.href='adminHotelBooking.php'">Hotel Booking</button>
                </div>
                
                <div class="booking-table-container">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Booking Reference</th>
                                <th>Flight ID(s)</th>
                                <th>Username</th>
                                <th>Flight Option</th>
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
                                <td>TSI-MH-A2301<br>TSI-MH-A2302</td>
                                <td>KUL → SIN → KUL</td>
                                <td>Round-trip</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=BK001234896'">Modify</button></td>
                            </tr>
                            <!-- Details panel for row 1 -->
                            <tr class="details-row">
                                <td colspan="7">
                                    <div class="details-panel" style="display: none;">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th>Passenger Name</th>
                                                    <th>Age Group</th>
                                                    <th>Seat No.</th>
                                                    <th>Class</th>
                                                    <th>Meal Type</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>John Doe</td>
                                                    <td>Adult (30)</td>
                                                    <td>1A (A2301)<br>2A (A2302)</td>
                                                    <td>Business</td>
                                                    <td>Multi-meal</td>
                                                    <td>RM 3,500</td>
                                                </tr>
                                                <tr>
                                                    <td>Jane Doe</td>
                                                    <td>Child (8)</td>
                                                    <td>1B (A2301)<br>2B (A2302)</td>
                                                    <td>Business</td>
                                                    <td>N/A</td>
                                                    <td>RM 2,100</td>
                                                </tr>
                                                <tr>
                                                    <td>Emily Davis</td>
                                                    <td>Youth (16)</td>
                                                    <td>3F (A2301)<br>5D (A2302)</td>
                                                    <td>Premium Economy</td>
                                                    <td>Single meal</td>
                                                    <td>RM 2,700</td>
                                                </tr>
                                                <tr>
                                                    <td>Michael Johnson</td>
                                                    <td>Senior (76)</td>
                                                    <td>9B (A2301)<br>7A (A2302)</td>
                                                    <td>First</td>
                                                    <td>Multi-meal</td>
                                                    <td>RM 5,200</td>
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
                                <td>TSI-MH-A2301<br>TSI-MH-A2302</td>
                                <td>KUL → SIN → KUL</td>
                                <td>Round-trip</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=BK001234897'">Modify</button></td>
                            </tr>
                            <!-- Details panel for row 2 -->
                            <tr class="details-row">
                                <td colspan="7">
                                    <div class="details-panel" style="display: none;">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th>Passenger Name</th>
                                                    <th>Age Group</th>
                                                    <th>Seat No.</th>
                                                    <th>Class</th>
                                                    <th>Meal Type</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sarah Williams</td>
                                                    <td>Adult (35)</td>
                                                    <td>3A (A2301)<br>4A (A2302)</td>
                                                    <td>Business</td>
                                                    <td>Multi-meal</td>
                                                    <td>RM 3,500</td>
                                                </tr>
                                                <tr>
                                                    <td>Robert Brown</td>
                                                    <td>Adult (42)</td>
                                                    <td>3B (A2301)<br>4B (A2302)</td>
                                                    <td>Business</td>
                                                    <td>Single meal</td>
                                                    <td>RM 3,500</td>
                                                </tr>
                                                <tr>
                                                    <td>Emma Jones</td>
                                                    <td>Child (10)</td>
                                                    <td>3C (A2301)<br>4C (A2302)</td>
                                                    <td>Business</td>
                                                    <td>N/A</td>
                                                    <td>RM 2,100</td>
                                                </tr>
                                                <tr>
                                                    <td>Thomas Wilson</td>
                                                    <td>Senior (68)</td>
                                                    <td>3D (A2301)<br>4D (A2302)</td>
                                                    <td>Business</td>
                                                    <td>Multi-meal</td>
                                                    <td>RM 4,400</td>
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
                                <td>TSI-MH-A2301<br>TSI-MH-A2302</td>
                                <td>KUL → SIN → KUL</td>
                                <td>Round-trip</td>
                                <td>RM 13,500</td>
                                <td><span class="status-cancelled">Cancelled</span></td>
                                <td><div class="empty-action-cell"></div></td>
                            </tr>
                            <!-- Details panel for row 3 -->
                            <tr class="details-row">
                                <td colspan="7">
                                    <div class="details-panel" style="display: none;">
                                        <table class="details-table">
                                            <thead>
                                                <tr>
                                                    <th>Passenger Name</th>
                                                    <th>Age Group</th>
                                                    <th>Seat No.</th>
                                                    <th>Class</th>
                                                    <th>Meal Type</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>David Miller</td>
                                                    <td>Adult (40)</td>
                                                    <td>5A (A2301)<br>6A (A2302)</td>
                                                    <td>Economy</td>
                                                    <td>Single meal</td>
                                                    <td>RM 2,700</td>
                                                </tr>
                                                <tr>
                                                    <td>Lisa Miller</td>
                                                    <td>Adult (38)</td>
                                                    <td>5B (A2301)<br>6B (A2302)</td>
                                                    <td>Economy</td>
                                                    <td>Single meal</td>
                                                    <td>RM 2,700</td>
                                                </tr>
                                                <tr>
                                                    <td>James Miller</td>
                                                    <td>Child (12)</td>
                                                    <td>5C (A2301)<br>6C (A2302)</td>
                                                    <td>Economy</td>
                                                    <td>N/A</td>
                                                    <td>RM 2,000</td>
                                                </tr>
                                                <tr>
                                                    <td>Anna Miller</td>
                                                    <td>Child (10)</td>
                                                    <td>5D (A2301)<br>6D (A2302)</td>
                                                    <td>Economy</td>
                                                    <td>N/A</td>
                                                    <td>RM 2,000</td>
                                                </tr>
                                                <tr>
                                                    <td>Margaret Miller</td>
                                                    <td>Senior (72)</td>
                                                    <td>5E (A2301)<br>6E (A2302)</td>
                                                    <td>Economy</td>
                                                    <td>Single meal</td>
                                                    <td>RM 4,100</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Example row 4 -->
                            <tr class="booking-row" data-booking-id="BK001234899" id="booking-BK001234899">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234899')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234899</span>
                                    </div>
                                </td>
                                <td>TSI-MH-A2301<br>TSI-MH-A2302</td>
                                <td>KUL → SIN → KUL</td>
                                <td>Round-trip</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=BK001234899'">Modify</button></td>
                            </tr>
                            
                            <!-- Example row 5 -->
                            <tr class="booking-row" data-booking-id="BK001234900" id="booking-BK001234900">
                                <td>
                                    <div class="toggle-details" onclick="toggleDetails('BK001234900')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                                        <span class="booking-ref">BK001234900</span>
                                    </div>
                                </td>
                                <td>TSI-MH-A2301<br>TSI-MH-A2302</td>
                                <td>KUL → SIN → KUL</td>
                                <td>Round-trip</td>
                                <td>RM 13,500</td>
                                <td><span class="status-confirmed">Confirmed</span></td>
                                <td><button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=BK001234900'">Modify</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
                    <span class="pagination-info">Showing 1-09 of 78</span>
                    <div class="pagination-controls">
                        <button class="pagination-btn prev"><i class="fa fa-chevron-left"></i></button>
                        <button class="pagination-btn next"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/adminSidebar.js"></script>
    <script src="js/adminFlightBooking.js"></script>
</body>
</html> 