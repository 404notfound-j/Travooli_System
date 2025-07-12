<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Get booking ID from URL
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : '';

// Initialize variables with default values
$flightId = '';
$flightRoute = '';
$flightOption = '';
$totalAmount = '';
$paymentStatus = '';
$bookingStatus = '';

// Default passenger data
$passengerDetails = [];

// If booking ID is provided, fetch data from database
if (!empty($bookingId)) {
    // Get flight booking details with all related flights for this booking
    $sql = "SELECT fb.f_book_id, fb.flight_id, fb.status, fp.amount, fp.payment_status,
                   fi.orig_airport_id, fi.dest_airport_id, 
                   a1.city_full as origin_city, a1.airport_short as origin_code,
                   a2.city_full as destination_city, a2.airport_short as destination_code,
                   al.airline_name, fbi.trip_type
            FROM flight_booking_t fb
            LEFT JOIN flight_payment_t fp ON fb.f_book_id = fp.f_book_id
            LEFT JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
            LEFT JOIN airport_t a1 ON fi.orig_airport_id = a1.airport_id
            LEFT JOIN airport_t a2 ON fi.dest_airport_id = a2.airport_id
            LEFT JOIN airline_t al ON fi.airline_id = al.airline_id
            LEFT JOIN flight_booking_info_t fbi ON fb.f_book_id = fbi.f_book_id
            WHERE fb.f_book_id = ?
            ORDER BY fi.departure_time";
            
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $bookingId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Get all flight segments for this booking
        $flight_segments = [];
        $flightIds = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $flight_segments[] = $row;
            $flightIds[] = $row['flight_id'];
            
            // Set basic booking info from the first segment
            if (empty($flightId)) {
            $bookingStatus = $row['status'] ?: 'Confirmed';
                $totalAmount = $row['amount'] ? number_format($row['amount'], 2) : '0.00';
            $paymentStatus = $row['payment_status'] ?: 'Paid';
                $tripType = $row['trip_type'] ?: '';
            }
        }
        
        // Format flight IDs for display
        $flightId = implode(', ', array_unique($flightIds));
        
        // Format flight route
        if (count($flight_segments) > 0) {
            // Check if this is a round-trip
            $is_round_trip = false;
            if (!empty($tripType)) {
                $is_round_trip = ($tripType === 'round-trip');
            }
            
            // Group flights by origin-destination pairs to identify routes
            $airport_pairs = [];
            foreach ($flight_segments as $segment) {
                $orig = !empty($segment['origin_code']) ? $segment['origin_code'] : 
                       (!empty($segment['orig_airport_id']) ? $segment['orig_airport_id'] : '');
                $dest = !empty($segment['destination_code']) ? $segment['destination_code'] : 
                       (!empty($segment['dest_airport_id']) ? $segment['dest_airport_id'] : '');
                
                if (!empty($orig) && !empty($dest)) {
                    $airport_pairs[] = ['orig' => $orig, 'dest' => $dest];
                }
            }
            
            // Build route for display
            $route = '';
            
            // If we have trip_type = round-trip or we can detect a round-trip pattern
            if ($is_round_trip || count($flight_segments) >= 2) {
                // Try to find a round-trip pattern (A→B and B→A)
                $found_round_trip = false;
                
                if (count($airport_pairs) >= 2) {
                    // Check for the classic round-trip pattern: A→B followed by B→A
                    for ($i = 0; $i < count($airport_pairs) - 1; $i++) {
                        $outbound = $airport_pairs[$i];
                        $inbound = $airport_pairs[$i + 1];
                        
                        if ($outbound['orig'] === $inbound['dest'] && $outbound['dest'] === $inbound['orig']) {
                            // Found a round-trip pattern
                            $route = $outbound['orig'] . ' → ' . $outbound['dest'] . ', ' . $inbound['orig'] . ' → ' . $inbound['dest'];
                            $found_round_trip = true;
                            break;
                        }
                    }
                }
                
                // If we couldn't find a round-trip pattern but trip_type says it's round-trip,
                // try to construct a reasonable route
                if (!$found_round_trip && $is_round_trip && !empty($airport_pairs)) {
                    $first = $airport_pairs[0];
                    $route = $first['orig'] . ' → ' . $first['dest'];
                    
                    // If we have a second segment, add its route
                    if (count($airport_pairs) > 1) {
                        $last = $airport_pairs[count($airport_pairs) - 1];
                        $route .= ', ' . $last['orig'] . ' → ' . $last['dest'];
                    } else {
                        // If only one segment but marked as round-trip, assume return to origin
                        $route .= ', ' . $first['dest'] . ' → ' . $first['orig'];
                    }
                }
            }
            
            // If we couldn't build a round-trip route, fall back to simple route
            if (empty($route)) {
                $route_parts = [];
                foreach ($airport_pairs as $pair) {
                    $route_parts[] = $pair['orig'] . ' → ' . $pair['dest'];
            }
                $route = implode(', ', $route_parts);
            }
            
            $flightRoute = $route;
            $flightOption = $is_round_trip ? 'Round-trip' : 'One-way';
        }
        
        mysqli_stmt_close($stmt);
        
        // Get passenger details for this booking
        $passenger_sql = "SELECT p.*, ps.class_id, sc.class_name, mo.opt_name as meal_type, 
                                fs.seat_no, fs.flight_id, 
                                (fp.amount / fbi.passenger_count) as amount
                         FROM passenger_t p
                         JOIN passenger_service_t ps ON p.pass_id = ps.pass_id
                         LEFT JOIN seat_class_t sc ON ps.class_id = sc.class_id
                         LEFT JOIN meal_option_t mo ON ps.meal_id = mo.meal_id
                         LEFT JOIN flight_seats_t fs ON ps.pass_id = fs.pass_id
                         LEFT JOIN flight_payment_t fp ON ps.f_book_id = fp.f_book_id
                         LEFT JOIN flight_booking_info_t fbi ON ps.f_book_id = fbi.f_book_id
                         WHERE ps.f_book_id = ?";
        
        $passenger_stmt = mysqli_prepare($connection, $passenger_sql);
        
        if ($passenger_stmt) {
            mysqli_stmt_bind_param($passenger_stmt, "s", $bookingId);
            mysqli_stmt_execute($passenger_stmt);
            $passenger_result = mysqli_stmt_get_result($passenger_stmt);
            
            if ($passenger_result && mysqli_num_rows($passenger_result) > 0) {
                // Group passengers by their ID
                $passenger_map = [];
                
                while ($passenger = mysqli_fetch_assoc($passenger_result)) {
                    $pass_id = $passenger['pass_id'];
                    
                    if (!isset($passenger_map[$pass_id])) {
                        // Calculate age if dob is available
                        $age = '';
                        if (!empty($passenger['dob'])) {
                            $dob = new DateTime($passenger['dob']);
                            $now = new DateTime();
                            $age = $dob->diff($now)->y;
                        }
                        
                        // Initialize passenger record
                        $passenger_map[$pass_id] = [
                            'passenger_name' => $passenger['fst_name'] . ' ' . $passenger['lst_name'],
                            'age_group' => $passenger['pass_category'] . (!empty($age) ? ' (' . $age . ')' : ''),
                            'seat_no' => '',
                            'class' => $passenger['class_name'] ?? 'Economy',
                            'meal_type' => $passenger['meal_type'] ?? 'Standard',
                            'amount' => number_format(($passenger['amount'] ?? 0), 2)
                        ];
                    }
                    
                    // Add seat information
                    if (!empty($passenger['seat_no'])) {
                        $seat_info = $passenger['seat_no'] . ' (' . $passenger['flight_id'] . ')';
                        
                        if (empty($passenger_map[$pass_id]['seat_no'])) {
                            $passenger_map[$pass_id]['seat_no'] = $seat_info;
                        } else {
                            $passenger_map[$pass_id]['seat_no'] .= ', ' . $seat_info;
                        }
                    }
                }
                
                $passengerDetails = array_values($passenger_map);
            }
            
            mysqli_stmt_close($passenger_stmt);
        }
    }
}

// If we still have no passenger details (e.g., database query failed), use default data
if (empty($passengerDetails)) {
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
        ]
    ];
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
    <!-- JavaScript -->
    <script src="js/adminModifyFlight.js"></script>
    
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
                                        <i class="fas fa-pen" style="color: #4379EE;"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown-select">
                                    <select class="age-group-select">
                                        <option value="Adult" <?php if(strpos($passenger['age_group'], 'Adult') !== false) echo 'selected'; ?>>Adult</option>
                                        <option value="Child" <?php if(strpos($passenger['age_group'], 'Child') !== false) echo 'selected'; ?>>Child</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($passenger['seat_no']); ?>
                            </td>
                            <td>
                                <div class="dropdown-select">
                                    <select class="class-select" data-original="<?php echo htmlspecialchars($passenger['class']); ?>">
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
                                    <select class="meal-select" data-original="<?php echo htmlspecialchars($passenger['meal_type']); ?>">
                                        <option <?php if($passenger['meal_type'] == 'Multi-meal') echo 'selected'; ?>>Multi-meal</option>
                                        <option <?php if($passenger['meal_type'] == 'Single meal') echo 'selected'; ?>>Single meal</option>
                                        <option <?php if($passenger['meal_type'] == 'N/A') echo 'selected'; ?>>N/A</option>
                                    </select>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </td>
                            <td class="passenger-amount" data-original-amount="<?php echo str_replace(',', '', $passenger['amount']); ?>">RM <?php echo htmlspecialchars($passenger['amount']); ?></td>
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
                        <?php
                        // Get flight details for this booking
                        $flights_found = false;
                        
                        if (!empty($bookingId)) {
                            $flight_sql = "SELECT fb.flight_id, 
                                           a1.city_full as origin_city, a1.airport_short as origin_code,
                                           a2.city_full as destination_city, a2.airport_short as destination_code,
                                           fi.departure_time, al.airline_name
                                    FROM flight_booking_t fb
                                    JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
                                    LEFT JOIN airport_t a1 ON fi.orig_airport_id = a1.airport_id
                                    LEFT JOIN airport_t a2 ON fi.dest_airport_id = a2.airport_id
                                    LEFT JOIN airline_t al ON fi.airline_id = al.airline_id
                                    WHERE fb.f_book_id = ?
                                    ORDER BY fi.departure_time";
                                    
                            $flight_stmt = mysqli_prepare($connection, $flight_sql);
                            
                            if ($flight_stmt) {
                                mysqli_stmt_bind_param($flight_stmt, "s", $bookingId);
                                mysqli_stmt_execute($flight_stmt);
                                $flight_result = mysqli_stmt_get_result($flight_stmt);
                                
                                if ($flight_result && mysqli_num_rows($flight_result) > 0) {
                                    $flights_found = true;
                                    
                                    // If we have a global flight route from booking summary, use it to extract individual routes
                                    $routes = [];
                                    if (!empty($flightRoute)) {
                                        // Split by comma if there are multiple segments
                                        $routeSegments = explode(',', $flightRoute);
                                        foreach ($routeSegments as $segment) {
                                            $routes[] = trim($segment);
                                        }
                                    }
                                    
                                    $i = 0; // Counter for routes
                                    while ($flight = mysqli_fetch_assoc($flight_result)) {
                                        // Use route from the booking summary if available
                                        $route = '';
                                        if (!empty($routes) && isset($routes[$i])) {
                                            $route = $routes[$i];
                                            $i++;
                                        } else {
                                            // Fallback to generating route from flight data
                                            if (!empty($flight['origin_code']) && !empty($flight['destination_code'])) {
                                                $route = $flight['origin_code'] . ' → ' . $flight['destination_code'];
                                            } elseif (!empty($flight['origin_city']) && !empty($flight['destination_city'])) {
                                                $route = $flight['origin_city'] . ' → ' . $flight['destination_city'];
                                            }
                                        }
                                        
                                        // Format date and time
                                        $date_time = '';
                                        if (!empty($flight['departure_time'])) {
                                            $departure = new DateTime($flight['departure_time']);
                                            $date = $departure->format('d M Y');
                                            $time = $departure->format('H:i');
                                            $date_time = $date . '<br>' . $time;
                                        }
                                        
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($flight['flight_id']) . '</td>';
                                        echo '<td>' . htmlspecialchars($flight['airline_name'] ?? 'N/A') . '</td>';
                                        echo '<td>' . htmlspecialchars($route) . '</td>';
                                        echo '<td>' . $date_time . '</td>';
                                        echo '<td>Airbus A330-300</td>';
                                        echo '</tr>';
                                    }
                                }
                                
                                mysqli_stmt_close($flight_stmt);
                            }
                        }
                        
                        // Display default data if no flights were found
                        if (!$flights_found) {
                        ?>
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
                        <?php } ?>
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
                    <div class="fare-value">: RM<span id="current-total"><?php echo htmlspecialchars($totalAmount); ?></span></div>
                </div>
                <div class="fare-row">
                    <div class="fare-label">New Total Amount</div>
                    <div class="fare-value">: RM<span id="new-total">0.00</span></div>
                </div>
                <div class="fare-row">
                    <div class="fare-label">Additional Charge(s)</div>
                    <div class="fare-value">: RM<span id="additional-charges">0.00</span></div>
                </div>
                <div class="fare-divider"></div>
                <div class="fare-row total">
                    <div class="fare-label">Final Total</div>
                    <div class="fare-total-value">: RM<span id="final-total"><?php echo htmlspecialchars($totalAmount); ?></span></div>
                </div>
                <!-- Hidden inputs for price information -->
                <input type="hidden" id="modification-fee" value="50.00">
                <input type="hidden" id="original-total" value="<?php echo str_replace(',', '', $totalAmount); ?>">
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