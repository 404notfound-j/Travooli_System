<?php
// Include database connection
include 'connection.php';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Count total records for pagination
$total_records_query = "SELECT COUNT(*) as total FROM hotel_booking_t";
$total_records_result = $connection->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get hotel bookings with pagination and join with hotel_t, room_type_t, and hotel_payment_t tables
$sql = "SELECT hb.h_book_id, h.name as hotel_name, rt.type_name, hr.price_per_night, hb.status, 
               hb.check_in_date, hb.check_out_date, c.fst_name, c.lst_name, c.nationality, 
               c.email, c.phone_no, hb.room_count, hp.amount as payment_amount,
               DATEDIFF(hb.check_out_date, hb.check_in_date) as nights
        FROM hotel_booking_t hb
        LEFT JOIN hotel_t h ON hb.hotel_id = h.hotel_id
        LEFT JOIN room_type_t rt ON hb.r_type_id = rt.r_type_id
        LEFT JOIN hotel_room_t hr ON (hb.hotel_id = hr.hotel_id AND hb.r_type_id = hr.r_type_id)
        LEFT JOIN customer_t c ON hb.customer_id = c.customer_id
        LEFT JOIN hotel_payment_t hp ON hb.h_book_id = hp.h_book_id
        ORDER BY hb.h_book_id
        LIMIT $offset, $records_per_page";

$result = $connection->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Error executing query: " . $connection->error);
}

// Set page specific CSS
$pageSpecificCSS = '<link rel="stylesheet" href="css/adminHotelBooking.css">';
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
    <link rel="stylesheet" href="css/adminHotelBooking.css">
</head>
<body>
<?php 
// Buffer the main content
ob_start(); 
?>

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
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Use payment_amount if available, otherwise calculate
                        if (!empty($row['payment_amount'])) {
                            $total_amount = $row['payment_amount'];
                        } else {
                            // Calculate the total amount if payment record doesn't exist
                            $nights = $row['nights'] ?: 1; // Default to 1 if nights is null or 0
                            $room_count = $row['room_count'] ?: 1; // Default to 1 if room_count is null or 0
                            $total_amount = $row['price_per_night'] * $nights * $room_count;
                        }
                        
                        // Format the amount with commas for thousands
                        $formatted_amount = "RM " . number_format($total_amount, 2);
                        
                        // If hotel_name is missing, use placeholder
                        $hotel_name = $row['hotel_name'] ?: "Emperor Hotel @ KLCC";
                        
                        // If room type is missing, use placeholder
                        $room_type = $row['type_name'] ?: "Deluxe Room";
                ?>
                <!-- Booking row -->
                <tr class="booking-row" data-booking-id="<?php echo $row['h_book_id']; ?>" id="booking-<?php echo $row['h_book_id']; ?>">
                    <td>
                        <div class="toggle-details" onclick="toggleDetails('<?php echo $row['h_book_id']; ?>')">
                            <i class="fa fa-chevron-down arrow-icon"></i>
                            <span class="booking-ref"><?php echo $row['h_book_id']; ?></span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($hotel_name); ?></td>
                    <td><?php echo htmlspecialchars($room_type); ?></td>
                    <td><?php echo $formatted_amount; ?></td>
                    <td><span class="status-<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                    <?php if (strtolower($row['status']) === 'confirmed'): ?>
                        <td><button class="modify-btn" onclick="window.location.href='adminModifyHotel.php?bookingId=<?php echo $row['h_book_id']; ?>'">Modify</button></td>
                    <?php else: ?>
                        <td><div class="empty-action-cell"></div></td>
                    <?php endif; ?>
                </tr>
                <!-- Details panel -->
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
                                        <td><?php echo htmlspecialchars($row['fst_name'] . ' ' . $row['lst_name']) ?: 'Guest Name'; ?></td>
                                        <td><?php echo htmlspecialchars($row['nationality']) ?: 'Malaysia'; ?></td>
                                        <td><?php echo htmlspecialchars($row['email']) ?: 'guest@example.com'; ?></td>
                                        <td><?php echo htmlspecialchars($row['phone_no']) ?: '011-11001100'; ?></td>
                                        <td><?php echo $row['check_in_date'] ? date('d-m-Y', strtotime($row['check_in_date'])) : '22-05-2025'; ?></td>
                                        <td><?php echo $row['check_out_date'] ? date('d-m-Y', strtotime($row['check_out_date'])) : '23-05-2025'; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No hotel bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="pagination">
        <span class="pagination-info">Showing <?php echo ($offset + 1); ?>-<?php echo min(($offset + $records_per_page), $total_records); ?> of <?php echo $total_records; ?></span>
        <div class="pagination-controls">
            <button class="pagination-btn prev" <?php if ($page <= 1) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page - 1; ?>'"><i class="fa fa-chevron-left"></i></button>
            <button class="pagination-btn next" <?php if ($page >= $total_pages) echo 'disabled'; ?> onclick="window.location.href='?page=<?php echo $page + 1; ?>'"><i class="fa fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
include 'adminSidebar.php';
?>

<script src="js/adminHotelBooking.js"></script>
</body>
</html>
<?php
$connection->close();
?>