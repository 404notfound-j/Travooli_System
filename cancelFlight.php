<?php
session_start();
include 'connection.php';

// Get booking ID either from POST data or URL parameter
$data = json_decode(file_get_contents('php://input'), true);
$bookingId = null;

// Debug information
$debug = [];
$debug['post_data'] = $data;
$debug['get_data'] = $_GET;
$debug['session_data'] = $_SESSION;

$seatInfoQuery = "SELECT flight_id, seat_class, total_passengers FROM flight_booking_t WHERE f_book_id = ?";
$stmt = mysqli_prepare($connection, $seatInfoQuery);
mysqli_stmt_bind_param($stmt, "s", $f_book_id);
mysqli_stmt_execute($stmt);
$seatInfoResult = mysqli_stmt_get_result($stmt);
$seatInfo = mysqli_fetch_assoc($seatInfoResult);
mysqli_stmt_close($stmt);

if ($seatInfo) {
    $flightId = $seatInfo['flight_id'];
    $seatClass = $seatInfo['seat_class'];
    $seatsToRestore = $seatInfo['total_passengers'];

    // 4a. Update seat availability in flight_seats_cls_t
    $updateSeatsCls = "UPDATE flight_seats_cls_t 
                       SET available_seats = available_seats + ? 
                       WHERE flight_id = ? AND seat_class = ?";
    $stmt = mysqli_prepare($connection, $updateSeatsCls);
    mysqli_stmt_bind_param($stmt, "iss", $seatsToRestore, $flightId, $seatClass);
    $updateSeatsClsSuccess = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 4b. Free the specific seats in flight_seats_t (if you assign specific seats by booking)
    $updateSeatsT = "UPDATE flight_seats_t 
                     SET is_booked = 0, f_book_id = NULL 
                     WHERE f_book_id = ?";
    $stmt = mysqli_prepare($connection, $updateSeatsT);
    mysqli_stmt_bind_param($stmt, "s", $f_book_id);
    $updateSeatsTSuccess = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    $updateSeatsClsSuccess = false;
    $updateSeatsTSuccess = false;
}

// Check if booking ID is in POST data
if (isset($data['booking_id']) && !empty($data['booking_id'])) {
    $bookingId = $data['booking_id'];
    $debug['source'] = 'POST data';
} 
// Check if booking ID is in URL parameter
elseif (isset($_GET['booking_id']) && !empty($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];
    $debug['source'] = 'GET parameter';
}
// Otherwise, get from session
else {
    if (isset($_SESSION['booking_ids']) && !empty($_SESSION['booking_ids'])) {
        $bookingIds = $_SESSION['booking_ids'];
        $bookingId = $bookingIds[0];
        $debug['source'] = 'Session booking_ids array';
    } elseif (isset($_SESSION['booking_id']) && !empty($_SESSION['booking_id'])) {
        $bookingId = $_SESSION['booking_id'];
        $debug['source'] = 'Session booking_id';
    } elseif (isset($_SESSION['f_book_id']) && !empty($_SESSION['f_book_id'])) {
        $bookingId = $_SESSION['f_book_id'];
        $debug['source'] = 'Session f_book_id';
    }
}

// If still no booking ID, check URL for bookingId parameter
if (!$bookingId && isset($_GET['bookingId']) && !empty($_GET['bookingId'])) {
    $bookingId = $_GET['bookingId'];
    $debug['source'] = 'GET bookingId parameter';
}

// If still no booking ID found, output debug info and exit
if (!$bookingId) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'No booking ID found',
        'debug' => $debug
    ]);
    exit;
}

$debug['booking_id_found'] = $bookingId;

// Process as a single booking ID
$f_book_id = $bookingId;

// 1. Insert refund record
$lastIdQuery = "SELECT f_refund_id FROM flight_refund_t ORDER BY f_refund_id DESC LIMIT 1";
$lastResult = mysqli_query($connection, $lastIdQuery);
$lastRow = mysqli_fetch_assoc($lastResult);
$newNum = $lastRow ? ((int)substr($lastRow['f_refund_id'], 2)) + 1 : 1;
$newRefundId = "FR" . str_pad($newNum, 3, "0", STR_PAD_LEFT);

// Get original payment amount
$paymentQuery = "SELECT amount FROM flight_payment_t WHERE f_book_id = ?";
$stmt = mysqli_prepare($connection, $paymentQuery);
mysqli_stmt_bind_param($stmt, "s", $f_book_id);
mysqli_stmt_execute($stmt);
$paymentResult = mysqli_stmt_get_result($stmt);
$paymentRow = mysqli_fetch_assoc($paymentResult);
$refundAmt = $paymentRow['amount'] ?? 0;
mysqli_stmt_close($stmt);

$refundMethod = "credit"; 
$refundDate = date("Y-m-d");
$refundStatus = "completed";

// Use prepared statement to prevent SQL injection
$insertRefund = "INSERT INTO flight_refund_t (f_refund_id, f_book_id, refund_amt, refund_method, refund_date, status)
                VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($connection, $insertRefund);
mysqli_stmt_bind_param($stmt, "ssdsss", $newRefundId, $f_book_id, $refundAmt, $refundMethod, $refundDate, $refundStatus);
$insertSuccess = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// 2. Update booking status
$updateBooking = "UPDATE flight_booking_t SET status = 'cancelled' WHERE f_book_id = ?";
$stmt = mysqli_prepare($connection, $updateBooking);
mysqli_stmt_bind_param($stmt, "s", $f_book_id);
$updateBookingSuccess = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// 3. Update payment status
$updatePayment = "UPDATE flight_payment_t SET payment_status = 'refunded' WHERE f_book_id = ?";
$stmt = mysqli_prepare($connection, $updatePayment);
mysqli_stmt_bind_param($stmt, "s", $f_book_id);
$updatePaymentSuccess = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Return success message with debug info
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => "Your booking has been successfully cancelled and refund processed.",
    'booking_id' => $f_book_id,
    'debug' => $debug,
    'operations' => [
        'insert_refund' => $insertSuccess,
        'update_booking' => $updateBookingSuccess,
        'update_payment' => $updatePaymentSuccess,
        'update_seat_class' => $updateSeatsClsSuccess,
        'update_individual_seats' => $updateSeatsTSuccess
    ]
]);
?>