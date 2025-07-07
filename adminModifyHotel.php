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
    <link rel="stylesheet" href="css/adminModifyHotel.css">
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
            <div class="modify-booking-container">
                <div class="page-header">
                    <a href="adminHotelBooking.php" class="back-button">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                    <h1>Modify Booking Details</h1>
                </div>
                
                <div class="booking-sections">
                    <!-- Booking Summary Title -->
                    <h2 class="section-title">BOOKING SUMMARY</h2>
                    
                    <!-- Booking Summary Section -->
                    <section class="booking-summary-section">
                        <div class="summary-content">
                            <div class="summary-row">
                                <div class="summary-label">Booking Reference</div>
                                <div class="summary-value">: BK001234896</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Hotel</div>
                                <div class="summary-value">: Emperor Hotel @ KLCC</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Check-In Date</div>
                                <div class="summary-value">: 22-05-2025</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Check-Out Date</div>
                                <div class="summary-value">: 23-05-2025</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Payment Status</div>
                                <div class="summary-value">: Paid</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Booking Status</div>
                                <div class="summary-value">: Confirmed</div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Cancel Booking Button -->
                    <button class="cancel-booking-btn" onclick="openCancelHotelModal()">
                        <i class="fa fa-times"></i> Cancel Booking
                    </button>
                    
                    <!-- Guest Details Title -->
                    <h2 class="section-title">GUEST DETAILS</h2>
                    
                    <!-- Guest Details Section -->
                    <section class="guest-details-section">
                        <div class="guest-details-table">
                            <div class="guest-details-header">
                                <div class="guest-name-header">Guest Name</div>
                                <div class="nationality-header">Nationality</div>
                                <div class="email-header">Email</div>
                                <div class="phone-header">Phone Number</div>
                            </div>
                            
                            <div class="guest-details-row">
                                <div class="guest-name-cell">
                                    <div class="editable-field">
                                        <span id="guest-name">Michael Johnson</span>
                                        <button class="edit-btn" data-field="guest-name">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="nationality-cell">
                                    <div class="editable-field">
                                        <span id="nationality">Malaysia</span>
                                        <button class="edit-btn" data-field="nationality">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="email-cell">
                                    <div class="editable-field">
                                        <span id="email">aa@gmail.com</span>
                                        <button class="edit-btn" data-field="email">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="phone-cell">
                                    <div class="editable-field">
                                        <span id="phone">011-0111 1110</span>
                                        <button class="edit-btn" data-field="phone">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button class="discard-btn" id="discard-btn">Discard</button>
                        <button class="save-btn" id="save-btn">Save Change(s)</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Cancel Booking Modal -->
    <div class="modal" id="cancel-booking-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Are you sure to cancel your room?</h3>
            </div>
            <div class="modal-body">
                <p>Lorem ipsum odor amet, consectetuer adipiscing elit. Porttitor eget quam dui neque aenean. Facilisis feugiat conubia bibendum lobortis nunc. Mi nibh cubilia habitant dignissim curae.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-back" id="back-btn">Back</button>
                <button class="btn-confirm" id="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>
    
    <script src="js/adminSidebar.js"></script>
    <script src="js/adminModifyHotel.js"></script>
</body>
</html> 