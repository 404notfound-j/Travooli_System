<?php
// Include database connection
include 'connection.php';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Count total records for pagination
$total_records_query = "SELECT COUNT(*) as total FROM flight_booking_t";
$total_records_result = $connection->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get distinct booking IDs with pagination
$booking_ids_sql = "SELECT DISTINCT f_book_id FROM flight_booking_t ORDER BY f_book_id LIMIT $offset, $records_per_page";
$booking_ids_result = $connection->query($booking_ids_sql);

if (!$booking_ids_result) {
    die("Error executing query: " . $connection->error);
}

// Store all booking IDs
$booking_ids = [];
while ($row = $booking_ids_result->fetch_assoc()) {
    $booking_ids[] = $row['f_book_id'];
}

// Store all bookings data
$all_bookings = [];

// Process each booking ID
foreach ($booking_ids as $booking_id) {
    // Get all flight segments for this booking
    $sql = "SELECT fb.f_book_id, fb.flight_id, fb.user_id, c.fst_name, c.lst_name, fp.amount, fb.status,
                 fi.orig_airport_id, fi.dest_airport_id, 
                 orig.city_full as origin_city, orig.airport_short as origin_code,
                 dest.city_full as destination_city, dest.airport_short as destination_code,
                 al.airline_name, fbi.trip_type
        FROM flight_booking_t fb
        LEFT JOIN customer_t c ON fb.user_id = c.customer_id
        LEFT JOIN flight_payment_t fp ON fb.f_book_id = fp.f_book_id
        LEFT JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
        LEFT JOIN airport_t orig ON fi.orig_airport_id = orig.airport_id
        LEFT JOIN airport_t dest ON fi.dest_airport_id = dest.airport_id
        LEFT JOIN airline_t al ON fi.airline_id = al.airline_id
        LEFT JOIN flight_booking_info_t fbi ON fb.f_book_id = fbi.f_book_id
        WHERE fb.f_book_id = '$booking_id'
        ORDER BY fb.flight_id";

$result = $connection->query($sql);

if (!$result) {
    die("Error executing query: " . $connection->error);
}

    // Get all flight segments for this booking
    $flight_segments = [];
    while ($row = $result->fetch_assoc()) {
        $flight_segments[] = $row;
    }
    
    // If we have flight segments, add this booking to all_bookings
    if (count($flight_segments) > 0) {
        // Use the first segment for basic booking info
        $booking = $flight_segments[0];
        
        // Store all flight IDs for this booking
        $flight_ids = array_column($flight_segments, 'flight_id');
        $booking['flight_ids'] = $flight_ids;
        
        // Check if this is a round-trip based on trip_type
        $is_round_trip = false;
        if (!empty($booking['trip_type'])) {
            $is_round_trip = ($booking['trip_type'] === 'round-trip');
        }
        
        // Group flights by origin-destination pairs to identify round-trips
        $airport_pairs = [];
        foreach ($flight_segments as $segment) {
            $orig = !empty($segment['orig_airport_id']) ? $segment['orig_airport_id'] : 
                   (!empty($segment['origin_code']) ? $segment['origin_code'] : '');
            $dest = !empty($segment['dest_airport_id']) ? $segment['dest_airport_id'] : 
                   (!empty($segment['destination_code']) ? $segment['destination_code'] : '');
            
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
                        $route = $outbound['orig'] . ' → ' . $outbound['dest'] . ' → ' . $outbound['orig'];
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
                
                // If we have a second segment, add its destination
                if (count($airport_pairs) > 1) {
                    $last = $airport_pairs[count($airport_pairs) - 1];
                    $route .= ' → ' . $last['dest'];
                } else {
                    // If only one segment but marked as round-trip, assume return to origin
                    $route .= ' → ' . $first['orig'];
                }
            }
        }
        
        // If we couldn't build a round-trip route, fall back to simple route
        if (empty($route)) {
            $route_parts = [];
            foreach ($airport_pairs as $pair) {
                if (empty($route_parts) || end($route_parts) !== $pair['orig']) {
                    $route_parts[] = $pair['orig'];
                }
                $route_parts[] = $pair['dest'];
            }
            $route = implode(' → ', $route_parts);
        }
        
        // Store the route
        $booking['route'] = !empty($route) ? $route : 'Route information unavailable';
        $booking['is_round_trip'] = $is_round_trip;
        
        // Get passenger details for this booking
        $passenger_sql = "SELECT p.*, ps.class_id, sc.class_name, mo.opt_name as meal_type, 
                                 fs.seat_no, fs.flight_id
                          FROM passenger_t p
                          JOIN passenger_service_t ps ON p.pass_id = ps.pass_id
                          LEFT JOIN seat_class_t sc ON ps.class_id = sc.class_id
                          LEFT JOIN meal_option_t mo ON ps.meal_id = mo.meal_id
                          LEFT JOIN flight_seats_t fs ON ps.pass_id = fs.pass_id
                          WHERE ps.f_book_id = '$booking_id'";
        
        $passenger_result = $connection->query($passenger_sql);
        $passengers = [];
        
        if ($passenger_result && $passenger_result->num_rows > 0) {
            // Group passengers by their ID
            $passenger_map = [];
            
            while ($passenger = $passenger_result->fetch_assoc()) {
                $pass_id = $passenger['pass_id'];
                
                if (!isset($passenger_map[$pass_id])) {
                    // Initialize passenger record
                    $passenger_map[$pass_id] = [
                        'fst_name' => $passenger['fst_name'],
                        'lst_name' => $passenger['lst_name'],
                        'pass_category' => $passenger['pass_category'] ?? 'Adult',
                        'flights' => []
                    ];
                }
                
                // Add flight-specific info
                $passenger_map[$pass_id]['flights'][$passenger['flight_id']] = [
                    'seat_no' => $passenger['seat_no'] ?? 'Not assigned',
                    'class_name' => $passenger['class_name'] ?? 'Economy',
                    'meal_type' => $passenger['meal_type'] ?? 'Standard'
                ];
            }
            
            $passengers = array_values($passenger_map);
        }
        
        $booking['passengers'] = $passengers;
        $all_bookings[] = $booking;
    }
}

// Count the actual number of bookings
$actual_rows = count($all_bookings);

// Set up content for the page
ob_start();
?>
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
    <script src="js/adminFlightBooking.js"></script>
</head>
<body>
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
                                <th>Flight Route</th>
                                <th>Flight Option</th>
                                <th>Amount</th>
                                <th>Booking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                if (count($all_bookings) > 0) {
                    foreach ($all_bookings as $booking) {
                        // Format the amount with commas for thousands
                        $formatted_amount = "RM " . number_format($booking['amount'] ?: 245.50, 2);
                        
                        // Get flight IDs as a string
                        $flight_ids_display = implode("<br>", $booking['flight_ids']);
                        
                        // Flight route is already prepared in the booking array
                        $flight_route = $booking['route'];
                        
                        // Flight option based on round trip status
                        $flight_option = $booking['is_round_trip'] ? 'Round-trip' : 'One-way';
                        
                        // Get passengers from the booking
                        $passengers = $booking['passengers'];
                ?>
                <!-- Booking row -->
                <tr class="booking-row" data-booking-id="<?php echo $booking['f_book_id']; ?>" id="booking-<?php echo $booking['f_book_id']; ?>">
                                <td>
                        <div class="toggle-details" onclick="toggleDetails('<?php echo $booking['f_book_id']; ?>')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                            <span class="booking-ref"><?php echo $booking['f_book_id']; ?></span>
                                    </div>
                                </td>
                    <td><?php echo $flight_ids_display; ?></td>
                    <td><?php echo htmlspecialchars($flight_route); ?></td>
                    <td><?php echo htmlspecialchars($flight_option); ?></td>
                    <td><?php echo $formatted_amount; ?></td>
                    <td><span class="status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                    <td>
                        <?php if (strtolower($booking['status']) === 'confirmed'): ?>
                            <button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=<?php echo $booking['f_book_id']; ?>'">Modify</button>
                        <?php else: ?>
                            <div class="empty-action-cell"></div>
                        <?php endif; ?>
                                </td>
                            </tr>
                <!-- Details panel -->
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
                                            <?php if (count($passengers) > 0): ?>
                                                <?php foreach ($passengers as $passenger): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($passenger['fst_name'] . ' ' . $passenger['lst_name']); ?></td>
                                        <td><?php echo htmlspecialchars($passenger['pass_category']); ?></td>
                                        <td>
                                            <?php 
                                            // Display seat numbers for each flight
                                            $seat_info = [];
                                            foreach ($booking['flight_ids'] as $flight_id) {
                                                if (isset($passenger['flights'][$flight_id])) {
                                                    $seat_no = $passenger['flights'][$flight_id]['seat_no'];
                                                    $seat_info[] = $seat_no . ' (' . $flight_id . ')';
                                                }
                                            }
                                            echo !empty($seat_info) ? implode('<br>', $seat_info) : 'Not assigned';
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // Display class for each flight (usually the same for all flights)
                                            $class_info = [];
                                            foreach ($booking['flight_ids'] as $flight_id) {
                                                if (isset($passenger['flights'][$flight_id])) {
                                                    $class_name = $passenger['flights'][$flight_id]['class_name'];
                                                    if (!in_array($class_name, $class_info)) {
                                                        $class_info[] = $class_name;
                                                    }
                                                }
                                            }
                                            echo !empty($class_info) ? implode('<br>', $class_info) : 'Economy';
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // Display meal types for each flight
                                            $meal_info = [];
                                            foreach ($booking['flight_ids'] as $flight_id) {
                                                if (isset($passenger['flights'][$flight_id])) {
                                                    $meal_type = $passenger['flights'][$flight_id]['meal_type'];
                                                    if (!in_array($meal_type, $meal_info)) {
                                                        $meal_info[] = $meal_type;
                                                    }
                                                }
                                            }
                                            echo !empty($meal_info) ? implode('<br>', $meal_info) : 'Standard';
                                            ?>
                                        </td>
                                                    <td><?php echo $formatted_amount; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                        <td><?php echo htmlspecialchars($booking['fst_name'] . ' ' . $booking['lst_name']) ?: 'No passenger data'; ?></td>
                                                    <td>Adult</td>
                                                    <td>Not assigned</td>
                                                    <td>Economy</td>
                                                    <td>Standard</td>
                                                    <td><?php echo $formatted_amount; ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>No flight bookings found</td></tr>";
                }
                ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination">
        <span class="pagination-info">Showing <?php echo $actual_rows ? ($offset + 1) : 0; ?>-<?php echo ($offset + $actual_rows); ?> of <?php echo $total_records; ?></span>
                    <div class="pagination-controls">
            <button class="pagination-btn prev" <?php if ($page <= 1) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page - 1; ?>'"><i class="fa fa-chevron-left"></i></button>
            <button class="pagination-btn next" <?php if ($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page + 1; ?>'"><i class="fa fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>

<?php
$pageContent = ob_get_clean();

// Add custom scripts needed for this page
$customScripts = '<script src="js/adminFlightBooking.js"></script>';

// Include sidebar with custom scripts
include 'adminSidebar.php';
?>
</body>
</html>