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

// Get flight bookings with pagination and join with related tables
$sql = "SELECT fb.f_book_id, fb.flight_id, c.fst_name, c.lst_name, fp.amount, fb.status,
               fi.orig_airport_id, fi.dest_airport_id, a1.city_full as origin_city, a2.city_full as destination_city,
               al.airline_name
        FROM flight_booking_t fb
        LEFT JOIN customer_t c ON fb.user_id = c.customer_id
        LEFT JOIN flight_payment_t fp ON fb.f_book_id = fp.f_book_id
        LEFT JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
        LEFT JOIN airport_t a1 ON fi.orig_airport_id = a1.airport_id
        LEFT JOIN airport_t a2 ON fi.dest_airport_id = a2.airport_id
        LEFT JOIN airline_t al ON fi.airline_id = al.airline_id
        ORDER BY fb.f_book_id
        LIMIT $offset, $records_per_page";

$result = $connection->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Error executing query: " . $connection->error);
}

// Count the actual number of rows returned
$actual_rows = $result->num_rows;

// Store all rows in an array for easier processing
$all_bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_bookings[] = $row;
    }
}

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
                    foreach ($all_bookings as $row) {
                        // Format the amount with commas for thousands
                        $formatted_amount = "RM " . number_format($row['amount'] ?: 245.50, 2);
                        
                        // Format flight route
                        $flight_route = '';
                        if (!empty($row['origin_city']) && !empty($row['destination_city'])) {
                            $flight_route = $row['origin_city'] . ' → ' . $row['destination_city'];
                        } else {
                            $flight_route = 'KUL → SIN';
                        }
                        
                        // Default to "Round-trip" for flight option
                        $flight_option = 'Round-trip';
                        
                        // Get passenger details for this booking
                        $passenger_sql = "SELECT p.*, ps.class_id, sc.class_name, mo.opt_name as meal_type 
                                          FROM passenger_t p
                                          JOIN passenger_service_t ps ON p.pass_id = ps.pass_id
                                          LEFT JOIN seat_class_t sc ON ps.class_id = sc.class_id
                                          LEFT JOIN meal_option_t mo ON ps.meal_id = mo.meal_id
                                          WHERE ps.f_book_id = '{$row['f_book_id']}'";
                        $passenger_result = $connection->query($passenger_sql);
                        $passengers = [];
                        
                        if ($passenger_result && $passenger_result->num_rows > 0) {
                            while ($passenger = $passenger_result->fetch_assoc()) {
                                $passengers[] = $passenger;
                            }
                        }
                ?>
                <!-- Booking row -->
                <tr class="booking-row" data-booking-id="<?php echo $row['f_book_id']; ?>" id="booking-<?php echo $row['f_book_id']; ?>">
                                <td>
                        <div class="toggle-details" onclick="toggleDetails('<?php echo $row['f_book_id']; ?>')">
                                        <i class="fa fa-chevron-down arrow-icon"></i>
                            <span class="booking-ref"><?php echo $row['f_book_id']; ?></span>
                                    </div>
                                </td>
                    <td><?php echo htmlspecialchars($row['flight_id']); ?></td>
                    <td><?php echo htmlspecialchars($flight_route); ?></td>
                    <td><?php echo htmlspecialchars($flight_option); ?></td>
                    <td><?php echo $formatted_amount; ?></td>
                    <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                    <td>
                        <?php if (strtolower($row['status']) === 'confirmed'): ?>
                            <button class="modify-btn" onclick="window.location.href='adminModifyFlight.php?bookingId=<?php echo $row['f_book_id']; ?>'">Modify</button>
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
                                                    <td><?php echo htmlspecialchars($passenger['pass_category'] ?? 'Adult'); ?></td>
                                                    <td><?php echo htmlspecialchars('Not assigned'); ?></td>
                                                    <td><?php echo htmlspecialchars($passenger['class_name'] ?? 'Economy'); ?></td>
                                                    <td><?php echo htmlspecialchars($passenger['meal_type'] ?? 'Standard'); ?></td>
                                                    <td><?php echo $formatted_amount; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['fst_name'] . ' ' . $row['lst_name']) ?: 'No passenger data'; ?></td>
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