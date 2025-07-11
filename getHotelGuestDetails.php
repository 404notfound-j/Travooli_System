<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo '<tr><td colspan="6">Unauthorized access</td></tr>';
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['bookingId']) || empty($_GET['bookingId'])) {
    echo '<tr><td colspan="6">No booking ID provided</td></tr>';
    exit();
}

$bookingId = $_GET['bookingId'];

// Get guest details from the database
$query = "SELECT c.fst_name, c.lst_name, c.nationality, c.email, c.phone_no, 
          hb.check_in_date, hb.check_out_date
          FROM hotel_booking_t hb
          JOIN customer_t c ON hb.customer_id = c.customer_id
          WHERE hb.h_book_id = ?";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $bookingId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Format dates for display
    $checkInDate = date('d-m-Y', strtotime($row['check_in_date']));
    $checkOutDate = date('d-m-Y', strtotime($row['check_out_date']));
    
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['fst_name'] . ' ' . $row['lst_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['nationality']) . '</td>';
    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
    echo '<td>' . htmlspecialchars($row['phone_no']) . '</td>';
    echo '<td>' . $checkInDate . '</td>';
    echo '<td>' . $checkOutDate . '</td>';
    echo '</tr>';
} else {
    echo '<tr><td colspan="6">No guest details found for this booking</td></tr>';
}

// Close the statement
mysqli_stmt_close($stmt);
?> 