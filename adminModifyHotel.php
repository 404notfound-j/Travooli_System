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
    <link rel="stylesheet" href="css/dlt_acc_popup.css">
    <link rel="stylesheet" href="css/adminCancelHotel.css">

    <style>
        /* Section Titles */
        .section-title-modify {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 20px;
            color: #4379EE;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            opacity: 0.7;
            padding-left: 0;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Get booking ID from URL parameter
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : '';

// If no booking ID provided, redirect back to hotel booking list
if (empty($bookingId)) {
    header("Location: adminHotelBooking.php");
    exit();
}

// Query to get booking details with related information
$sql = "SELECT hb.h_book_id, h.name as hotel_name, rt.type_name, 
               hb.check_in_date, hb.check_out_date, hb.status, 
               c.fst_name, c.lst_name, c.nationality, c.email, c.phone_no,
               hp.amount, hp.status as payment_status
        FROM hotel_booking_t hb
        LEFT JOIN hotel_t h ON hb.hotel_id = h.hotel_id
        LEFT JOIN room_type_t rt ON hb.r_type_id = rt.r_type_id
        LEFT JOIN customer_t c ON hb.customer_id = c.customer_id
        LEFT JOIN hotel_payment_t hp ON hb.h_book_id = hp.h_book_id
        WHERE hb.h_book_id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

// Check if booking exists
if ($result->num_rows === 0) {
    // Booking not found, redirect back
    header("Location: adminHotelBooking.php");
    exit();
}

// Fetch booking data
$booking = $result->fetch_assoc();

// Format dates for display
$checkInDate = date('d-m-Y', strtotime($booking['check_in_date']));
$checkOutDate = date('d-m-Y', strtotime($booking['check_out_date']));

// Set active nav item for the sidebar
$activeNav = 'Booking';

// Define page content
ob_start();
?>
            <div class="modify-booking-container">
                <div class="page-header">
                    <a href="adminHotelBooking.php" class="back-button">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                    <h1>Modify Booking Details</h1>
                </div>
                
                <div class="booking-sections">
                    <!-- Booking Summary Title -->
                    <h2 class="section-title-modify">BOOKING SUMMARY</h2>
                    
                    <!-- Booking Summary Section -->
                    <section class="booking-summary-section">
                        <div class="summary-content">
                            <div class="summary-row">
                                <div class="summary-label">Booking Reference</div>
                            <div class="summary-value" id="booking-id">: <?php echo htmlspecialchars($booking['h_book_id']); ?></div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Hotel</div>
                            <div class="summary-value">: <?php echo htmlspecialchars($booking['hotel_name']); ?></div>
                        </div>
                        <div class="summary-row">
                            <div class="summary-label">Room Type</div>
                            <div class="summary-value">: <?php echo htmlspecialchars($booking['type_name']); ?></div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Check-In Date</div>
                            <div class="summary-value">: <?php echo $checkInDate; ?></div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Check-Out Date</div>
                            <div class="summary-value">: <?php echo $checkOutDate; ?></div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Payment Status</div>
                            <div class="summary-value">: <?php echo $booking['payment_status'] ?? 'Pending'; ?></div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Booking Status</div>
                            <div class="summary-value">: <?php echo $booking['status']; ?></div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Cancel Booking Button -->
                    <button class="cancel-booking-btn" onclick="openCancelHotelModal()">
                        <i class="fa fa-times"></i> Cancel Booking
                    </button>
                    
                    <!-- Guest Details Title -->
                    <h2 class="section-title-modify">GUEST DETAILS</h2>
                    
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
                                    <span id="guest-name"><?php echo htmlspecialchars($booking['fst_name'] . ' ' . $booking['lst_name']); ?></span>
                                        <button class="edit-btn" data-field="guest-name">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="nationality-cell">
                                    <div class="editable-field">
                                    <span id="nationality"><?php echo htmlspecialchars($booking['nationality']); ?></span>
                                        <button class="edit-btn" data-field="nationality">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="email-cell">
                                    <div class="editable-field">
                                    <span id="email"><?php echo htmlspecialchars($booking['email']); ?></span>
                                        <button class="edit-btn" data-field="email">
                                            <img src="icon/edit.svg" alt="Edit">
                                        </button>
                                    </div>
                                </div>
                                <div class="phone-cell">
                                    <div class="editable-field">
                                    <span id="phone"><?php echo htmlspecialchars($booking['phone_no']); ?></span>
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
                    <button class="save-btn" id="save-btn" data-booking-id="<?php echo htmlspecialchars($booking['h_book_id']); ?>">Save Change(s)</button>
                </div>
            </div>
    </div>
    
    <!-- Cancel Booking Modal -->
    <div class="modal-overlay" id="cancelHotelModal" style="display:none;">
        <!-- Modal Dialog -->
        <div class="modal-dialog">
            <!-- Title Row -->
            <div class="title-row">
                <h2 class="modal-title">Are you sure to cancel your room?</h2>
            </div>
            
            <!-- Description -->
            <div class="modal-description">
                <p>Cancelling this hotel booking will result in the reservation being removed from the system. A full refund will be processed to the customer's original payment method, and the booking status will be updated to Cancelled. Please confirm that you want to proceed with cancelling this booking.</p>
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <div class="button-row">
                    <button class="btn btn-secondary" id="back-btn">Back</button>
                    <button class="btn btn-primary btn-danger" id="confirm-btn" data-booking-id="<?php echo htmlspecialchars($booking['h_book_id']); ?>">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();

// Include the admin sidebar layout
include 'adminSidebar.php';
?>

    <script src="js/adminModifyHotel.js"></script>
</body>
</html> 