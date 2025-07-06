<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Flight Booking - Travooli</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminSidebar.css">
    <link rel="stylesheet" href="css/adminModifyFlight.css">
    <!-- Popup CSS -->
    <link rel="stylesheet" href="css/dlt_acc_popup.css">
    <link rel="stylesheet" href="css/adminCancelFlight.css">

    <script>
        // Store booking ID from URL for JavaScript use
        const bookingId = "<?php echo isset($_GET['bookingId']) ? htmlspecialchars($_GET['bookingId']) : ''; ?>";
    </script>
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
                <!-- Header with Back Button -->
                <div class="modify-header">
                    <a href="adminFlightBooking.php" class="back-button">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <h1>Modify Booking Details</h1>
                </div>
                

                
                <!-- Booking Summary Section -->
                <div class="section-container">
                    <h2 class="section-title">Booking Summary</h2>
                    <div class="booking-summary">
                        <div class="summary-row">
                            <div class="summary-label">Booking Reference</div>
                            <div class="summary-value">: <?php echo isset($_GET['bookingId']) ? htmlspecialchars($_GET['bookingId']) : 'BK001234896'; ?></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Flight(s) ID</div>
                            <div class="summary-value">: TSI-MH-A2301 (KUL → SIN), TSI-MH-A2302 (SIN → KUL)</div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Flight Option</div>
                            <div class="summary-value">: Round-trip</div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Total Amount</div>
                            <div class="summary-value">: RM13,500</div>
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
                    
                    <button class="cancel-booking-btn" onclick="openCancelFlightModal()">
                        <i class="fas fa-times"></i> Cancel Booking
                    </button>
                </div>
                
                <!-- Traveler Details Section -->
                <div class="section-container">
                    <h2 class="section-title">Traveler(s) Details</h2>
                    <div class="traveler-details">
                        <table class="traveler-table">
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
                                    <td>
                                        <div class="editable-field">
                                            <span>John Doe</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="editable-field">
                                            <span>Adult (30)</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        1A (A2301)<br>
                                        2A (A2302)
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option selected>Business</option>
                                                <option>Premium Economy</option>
                                                <option>First</option>
                                                <option>Economy</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option selected>Multi-meal</option>
                                                <option>Single meal</option>
                                                <option>N/A</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>RM 3,500</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="editable-field">
                                            <span>Jane Doe</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="editable-field">
                                            <span>Child (8)</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        1B (A2301)<br>
                                        2B (A2302)
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option selected>Business</option>
                                                <option>Premium Economy</option>
                                                <option>First</option>
                                                <option>Economy</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option>Multi-meal</option>
                                                <option>Single meal</option>
                                                <option selected>N/A</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>RM 2,100</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="editable-field">
                                            <span>Emily Davis</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="editable-field">
                                            <span>Youth (16)</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        3F (A2301)<br>
                                        5D (A2302)
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option>Business</option>
                                                <option selected>Premium Economy</option>
                                                <option>First</option>
                                                <option>Economy</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option>Multi-meal</option>
                                                <option selected>Single meal</option>
                                                <option>N/A</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>RM 2,700</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="editable-field">
                                            <span>Michael Johnson</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="editable-field">
                                            <span>Senior (76)</span>
                                            <button class="edit-btn">
                                                <img src="icon/edit.svg" alt="Edit">
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        9B (A2301)<br>
                                        7A (A2302)
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option>Business</option>
                                                <option>Premium Economy</option>
                                                <option selected>First</option>
                                                <option>Economy</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown-select">
                                            <select>
                                                <option selected>Multi-meal</option>
                                                <option>Single meal</option>
                                                <option>N/A</option>
                                            </select>
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </td>
                                    <td>RM 5,200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Flight Details Section -->
                <div class="section-container">
                    <h2 class="section-title">Flight(s) Details</h2>
                    <div class="flight-details">
                        <table class="flight-table">
                            <thead>
                                <tr>
                                    <th>Flight ID</th>
                                    <th>Flight Name</th>
                                    <th>Route</th>
                                    <th>Date - Time</th>
                                    <th>Aircraft</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TSI-MH-A2301</td>
                                    <td>MH1217</td>
                                    <td>KUL → SIN</td>
                                    <td>25 Jan 2025<br>08:00</td>
                                    <td>Boeing 777</td>
                                </tr>
                                <tr>
                                    <td>TSI-MH-A2302</td>
                                    <td>MH1374</td>
                                    <td>SIN → KUL</td>
                                    <td>29 Jan 2025<br>14:00</td>
                                    <td>Boeing 747</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Fare Update Section -->
                <div class="section-container">
                    <h2 class="section-title">Fare Update</h2>
                    <div class="fare-update">
                        <div class="fare-row">
                            <div class="fare-label">Current Total Amount</div>
                            <div class="fare-value">: RM13,500</div>
                        </div>
                        <div class="fare-row">
                            <div class="fare-label">New Total Amount</div>
                            <div class="fare-value">: RM16,500</div>
                        </div>
                        <div class="fare-row">
                            <div class="fare-label">Additional Charge(s)</div>
                            <div class="fare-value">: RM4,000</div>
                        </div>
                        <div class="fare-divider"></div>
                        <div class="fare-row total">
                            <div class="fare-label">Total</div>
                            <div class="fare-total-value">: RM20,500</div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="discard-btn">Discard</button>
                        <button class="save-btn">Save Change(s)</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Cancel Flight Popup Modal -->
    <div class="modal-overlay" id="cancelFlightModal" style="display:none;">
        <div class="modal-dialog">
            <div class="title-row">
                <h2 class="modal-title">Are you sure to cancel your room?</h2>
            </div>
            <div class="modal-description">
                Cancelling this flight booking will result in all passenger reservations being removed from the system. Depending on the airline's policy, this may incur cancellation fees. Please confirm that you want to proceed with cancelling this booking.
            </div>
            <div class="modal-actions">
                <div class="button-row">
                    <button class="btn btn-secondary" onclick="closeModal()">Back</button>
                    <button class="btn btn-primary btn-danger" onclick="confirmCancelFlight()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/adminSidebar.js"></script>
    <script src="js/adminModifyFlight.js"></script>
</body>
</html> 