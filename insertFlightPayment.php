<?php
session_start();
include 'connection.php';

// Step 1: Get user session
$username = $_SESSION['username'] ?? null;
if (!$username) {
    echo "User not logged in.";
    exit;
}

// Step 2: Get user_id by matching full name
$userQuery = "SELECT user_id FROM user_detail_t WHERE CONCAT(fst_name, ' ', lst_name) = ?";
$stmt = mysqli_prepare($connection, $userQuery);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$userResult = mysqli_stmt_get_result($stmt);
$userRow = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($stmt);

if (!$userRow) {
    echo "User ID not found.";
    exit;
}

$userId = $userRow['user_id'];

// Step 3: Get flight ID(s) from session
$departId = $_SESSION['selected_depart_flight_id'] ?? null;
$returnId = $_SESSION['selected_return_flight_id'] ?? null;
$oneWayFlightId = $_SESSION['selected_flight_id'] ?? null;

// Store seat class in session if provided (optional, used for logic only)
$data = json_decode(file_get_contents("php://input"), true);
$_SESSION['selectedClass'] = $data['seat_class'] ?? 'PE';

// Validate at least one flight is selected
if (!$departId && !$returnId && !$oneWayFlightId) {
    echo "No flight selected.";
    exit;
}

// Step 4: Prepare for booking insertion
$lastBookingQuery = "SELECT f_book_id FROM flight_booking_t ORDER BY f_book_id DESC LIMIT 1";
$bookingResult = mysqli_query($connection, $lastBookingQuery);
$bookingRow = mysqli_fetch_assoc($bookingResult);
$lastBookingIdNum = $bookingRow ? (int)substr($bookingRow['f_book_id'], 2) : 0;

$bookDate = date('Y-m-d');
$status = "confirmed";
$bookingIds = [];

// Step 5: Generate Booking ID helper
function createBookingId(&$counter) {
    $counter++;
    return "FB" . str_pad($counter, 3, "0", STR_PAD_LEFT);
}

// Step 6: Insert bookings
if ($oneWayFlightId) {
    $bookingId = createBookingId($lastBookingIdNum);
    $insert = "INSERT INTO flight_booking_t (f_book_id, user_id, flight_id, book_date, status)
               VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert);
    mysqli_stmt_bind_param($stmt, "sssss", $bookingId, $userId, $oneWayFlightId, $bookDate, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $bookingIds[] = $bookingId;
}

if ($departId && $returnId) {
    // Depart booking
    $departBookingId = createBookingId($lastBookingIdNum);
    $insertDepart = "INSERT INTO flight_booking_t (f_book_id, user_id, flight_id, book_date, status)
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertDepart);
    mysqli_stmt_bind_param($stmt, "sssss", $departBookingId, $userId, $departId, $bookDate, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $bookingIds[] = $departBookingId;

    // Return booking
    $returnBookingId = createBookingId($lastBookingIdNum);
    $insertReturn = "INSERT INTO flight_booking_t (f_book_id, user_id, flight_id, book_date, status)
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insertReturn);
    mysqli_stmt_bind_param($stmt, "sssss", $returnBookingId, $userId, $returnId, $bookDate, $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $bookingIds[] = $returnBookingId;
}

// Step 7: Payment Handling
$amount = $data['amount'] ?? null;
$payment_method = $data['payment_method'] ?? null;
$payment_date = $data['payment_date'] ?? null;

if (!$amount || !$payment_method || !$payment_date) {
    echo "Missing payment information.";
    exit;
}

// Step 8: Insert payment(s)
$lastIdQuery = "SELECT f_payment_id FROM flight_payment_t ORDER BY f_payment_id DESC LIMIT 1";
$result = mysqli_query($connection, $lastIdQuery);
$row = mysqli_fetch_assoc($result);
$lastId = $row ? (int)substr($row['f_payment_id'], 2) : 0;

$splitAmount = number_format($amount / count($bookingIds), 2, '.', '');

foreach ($bookingIds as $bookingId) {
    $lastId++;
    $newPaymentId = "FP" . str_pad($lastId, 3, "0", STR_PAD_LEFT);
    $insertPayment = "INSERT INTO flight_payment_t (f_payment_id, f_book_id, payment_date, amount, payment_method, payment_status)
                      VALUES (?, ?, ?, ?, ?, 'paid')";
    $stmt = mysqli_prepare($connection, $insertPayment);
    mysqli_stmt_bind_param($stmt, "sssss", $newPaymentId, $bookingId, $payment_date, $splitAmount, $payment_method);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Step 9: Save to session
$_SESSION['booking_ids'] = $bookingIds;

echo "Booking and payment recorded successfully.";
?>
