<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Get booking ID from URL
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : '';

// Initialize variables with default values
$flightId = '';
$flightRoute = 'KUL → SIN, SIN → KUL';
$flightOption = 'Round-trip';
$totalAmount = '13,500';
$paymentStatus = 'Paid';
$bookingStatus = 'Confirmed';

// Default passenger data
$passengerDetails = [
    [
        'passenger_name' => 'John Doe',
        'age_group' => 'Adult (30)',
        'seat_no' => '1A (A2301), 2A (A2302)',
        'class' => 'Business',
        'meal_type' => 'Multi-meal',
        'amount' => '3,500'
    ],
    [
        'passenger_name' => 'Jane Doe',
        'age_group' => 'Child (8)',
        'seat_no' => '1B (A2301), 2B (A2302)',
        'class' => 'Business',
        'meal_type' => 'N/A',
        'amount' => '2,100'
    ],
    [
        'passenger_name' => 'Emily Davis',
        'age_group' => 'Youth (16)',
        'seat_no' => '3F (A2301), 5D (A2302)',
        'class' => 'Premium Economy',
        'meal_type' => 'Single meal',
        'amount' => '2,700'
    ],
    [
        'passenger_name' => 'Michael Johnson',
        'age_group' => 'Senior (76)',
        'seat_no' => '9B (A2301), 7A (A2302)',
        'class' => 'First',
        'meal_type' => 'Multi-meal',
        'amount' => '5,200'
    ]
];

// If booking ID is provided, fetch data from database
if (!empty($bookingId)) {
    // Get flight booking details
    $sql = "SELECT fb.f_book_id, fb.flight_id, fb.status, fp.amount, fp.payment_status,
                   fi.orig_airport_id, fi.dest_airport_id, a1.city_full as origin_city, a2.city_full as destination_city,
                   al.airline_name
            FROM flight_booking_t fb
            LEFT JOIN flight_payment_t fp ON fb.f_book_id = fp.f_book_id
            LEFT JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
            LEFT JOIN airport_t a1 ON fi.orig_airport_id = a1.airport_id
            LEFT JOIN airport_t a2 ON fi.dest_airport_id = a2.airport_id
            LEFT JOIN airline_t al ON fi.airline_id = al.airline_id
            WHERE fb.f_book_id = ?";
            
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $bookingId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $flightId = $row['flight_id'] ?: 'TSI-MH-A2301';
            $bookingStatus = $row['status'] ?: 'Confirmed';
            $totalAmount = number_format($row['amount'] ?: 13500, 2);
            $paymentStatus = $row['payment_status'] ?: 'Paid';
            
            // Format flight route
            if (!empty($row['origin_city']) && !empty($row['destination_city'])) {
                $flightRoute = $row['origin_city'] . ' → ' . $row['destination_city'];
            }
        }
        
        mysqli_stmt_close($stmt);
    }
    
    // Note: We're not querying passenger_details_t as it doesn't exist
    // Using default passenger data instead
}

// Set active nav item for the sidebar
$activeNav = 'Booking';

// Define page content
ob_start();
?>
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
        const bookingId = "<?php echo $bookingId; ?>";
    </script>
    <style>
        /* Ensure scrolling works properly */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        body {
            display: flex;
        }
        
        .main-content {
            flex: 1;
            height: 100vh;
            overflow-y: auto !important;
            padding-bottom: 50px;
        }
        
        .admin-main-content {
            padding-bottom: 100px;
        }
    </style>
</head>
<body>
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
                    <div class="summary-value">: <?php echo htmlspecialchars($bookingId); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Flight(s) ID</div>
                    <div class="summary-value">: <?php echo htmlspecialchars($flightId); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Flight Route</div>
                    <div class="summary-value">: <?php echo htmlspecialchars($flightRoute); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Flight Option</div>
                    <div class="summary-value">: <?php echo htmlspecialchars($flightOption); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total Amount</div>
                    <div class="summary-value">: RM<?php echo htmlspecialchars($totalAmount); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Payment Status</div>
                    <div class="summary-value">: <?php echo htmlspecialchars($paymentStatus); ?></div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Booking Status</div>
                    <div class="summary-value">: <?php echo htmlspecialchars($bookingStatus); ?></div>
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
                        <?php foreach ($passengerDetails as $passenger): ?>
                        <tr>
                            <td>
                                <div class="editable-field">
                                    <span><?php echo htmlspecialchars($passenger['passenger_name']); ?></span>
                                    <button class="edit-btn">
                                        <img src="icon/edit.svg" alt="Edit">
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="editable-field">
                                    <span><?php echo htmlspecialchars($passenger['age_group']); ?></span>
                                    <button class="edit-btn">
                                        <img src="icon/edit.svg" alt="Edit">
                                    </button>
                                </div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($passenger['seat_no']); ?>
                            </td>
                            <td>
                                <div class="dropdown-select">
                                    <select>
                                        <option <?php if($passenger['class'] == 'Business') echo 'selected'; ?>>Business</option>
                                        <option <?php if($passenger['class'] == 'Premium Economy') echo 'selected'; ?>>Premium Economy</option>
                                        <option <?php if($passenger['class'] == 'First') echo 'selected'; ?>>First</option>
                                        <option <?php if($passenger['class'] == 'Economy') echo 'selected'; ?>>Economy</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown-select">
                                    <select>
                                        <option <?php if($passenger['meal_type'] == 'Multi-meal') echo 'selected'; ?>>Multi-meal</option>
                                        <option <?php if($passenger['meal_type'] == 'Single meal') echo 'selected'; ?>>Single meal</option>
                                        <option <?php if($passenger['meal_type'] == 'N/A') echo 'selected'; ?>>N/A</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </td>
                            <td>RM <?php echo htmlspecialchars($passenger['amount']); ?></td>
                        </tr>
                        <?php endforeach; ?>
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
                    <div class="fare-value">: RM<?php echo htmlspecialchars($totalAmount); ?></div>
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

    <!-- Cancel Flight Popup Modal -->
    <div class="modal-overlay" id="cancelFlightModal" style="display:none;">
        <div class="modal-dialog">
            <div class="title-row">
                <h2 class="modal-title">Are you sure you want to cancel this flight?</h2>
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
<?php
$pageContent = ob_get_clean();

// Include the admin sidebar layout
include 'adminSidebar.php';
?>

    <script src="js/adminSidebar.js"></script>
    <script src="js/adminModifyFlight.js"></script>
</body>
</html> 