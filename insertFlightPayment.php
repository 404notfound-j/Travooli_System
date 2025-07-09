<?php
// insertFlightPayment.php
session_start();
require_once 'connection.php'; // Include your database connection file

header('Content-Type: application/json'); // Set header to return JSON

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Do not display errors to the user, but log them

// Ensure request method is POST and content type is JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true); // Decode JSON as associative array

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON Decode Error: " . json_last_error_msg() . " Input: " . $input);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON received: ' . json_last_error_msg()]);
    exit();
}

// Extract data from the received JSON
$userId = $data['user_id'] ?? null;
$flightId = $data['flight_id'] ?? null;
$bookingDate = $data['booking_date'] ?? null;
$status = $data['status'] ?? 'Confirmed';

// --- Extract additional data for flight_booking_info_t ---
$classId = $data['class_id'] ?? null;
$selectedSeats = $data['selected_seats'] ?? [];
$totalAmountPaid = $data['total_amount'] ?? 0;

$ticketBasePrice = $data['ticket'] ?? 0;
$baggageFees = $data['baggage'] ?? 0;
$mealFees = $data['meal'] ?? 0;
$taxAmount = $data['taxPrice'] ?? 0;

$numPassenger = $data['num_passenger'] ?? 0;

$flightDate = $data['flight_date'] ?? null;

$selectedSeatNumbersString = implode(',', $selectedSeats);

// --- DEBUG LINE: Check received data in PHP ---
error_log("DEBUG in insertFlightPayment.php: Received data: " . print_r($data, true));
error_log("DEBUG: Data for info table: TotalPaid=" . $totalAmountPaid . ", Ticket=" . $ticketBasePrice . ", NumPass=" . $numPassenger . ", Seats=" . $selectedSeatNumbersString . ", FlightDate=" . $flightDate);
// --- END DEBUG ---


// Basic validation for core booking data
if (empty($userId) || empty($flightId) || empty($bookingDate) || empty($totalAmountPaid) || empty($classId) || empty($selectedSeats) || empty($numPassenger) || empty($flightDate)) {
    error_log("ERROR in insertFlightPayment.php: Missing required booking data. Received: " . print_r($data, true));
    echo json_encode(['success' => false, 'error' => 'Missing required booking data.']);
    exit();
}

// Start transaction for atomicity
mysqli_autocommit($connection, false);

try {
    // 1. Generate a unique flight_booking_id in the format BKmmddNNNNNN
    $datePart = date('md');
    $randomNumber = mt_rand(0, 999999);
    $randomPart = str_pad($randomNumber, 6, '0', STR_PAD_LEFT);
    $flightBookingId = 'BK' . $datePart . $randomPart;


    // 2. Insert into flight_booking_t
    $bookingQuery = "INSERT INTO flight_booking_t (flight_booking_id, user_id, flight_id, booking_date, status) VALUES (?, ?, ?, ?, ?)";
    $stmtBooking = mysqli_prepare($connection, $bookingQuery);
    if (!$stmtBooking) {
        throw new Exception('Booking query prepare failed: ' . mysqli_error($connection));
    }
    mysqli_stmt_bind_param($stmtBooking, "sssss", $flightBookingId, $userId, $flightId, $bookingDate, $status);
    if (!mysqli_stmt_execute($stmtBooking)) {
        throw new Exception('Booking insert failed: ' . mysqli_stmt_error($stmtBooking));
    }
    mysqli_stmt_close($stmtBooking);


    // 3. Insert into flight_booking_info_t
    $bookingInfoQuery = "INSERT INTO flight_booking_info_t (
        flight_booking_id, total_amount_paid, ticket_base_price, baggage_fees, 
        meal_fees, tax_amount, num_passenger, selected_seat_numbers, class_id, flight_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmtInfo = mysqli_prepare($connection, $bookingInfoQuery);
    if (!$stmtInfo) {
        throw new Exception('Booking info query prepare failed: ' . mysqli_error($connection));
    }
    // This line is now corrected to have 10 types to match the 10 variables
    mysqli_stmt_bind_param(
        $stmtInfo, "sdddddisss", // CORRECTED TYPE STRING
        $flightBookingId, $totalAmountPaid, $ticketBasePrice, $baggageFees,
        $mealFees, $taxAmount, $numPassenger, $selectedSeatNumbersString, $classId, $flightDate
    );
    if (!mysqli_stmt_execute($stmtInfo)) {
        throw new Exception('Booking info insert failed: ' . mysqli_stmt_error($stmtInfo));
    }
    mysqli_stmt_close($stmtInfo);


    // 4. Update flight_seats_t for each selected seat
    $seatUpdateQuery = "UPDATE flight_seats_t SET is_booked = 1, pass_id = ? WHERE flight_id = ? AND class_id = ? AND seat_no = ?";
    $stmtSeats = mysqli_prepare($connection, $seatUpdateQuery);
    if (!$stmtSeats) {
        throw new Exception('Seat update query prepare failed: ' . mysqli_error($connection));
    }

    foreach ($selectedSeats as $seatNo) {
        mysqli_stmt_bind_param($stmtSeats, "ssss", $userId, $flightId, $classId, $seatNo);
        if (!mysqli_stmt_execute($stmtSeats)) {
            throw new Exception('Failed to update seat ' . $seatNo . ': ' . mysqli_stmt_error($stmtSeats));
        }
        if (mysqli_stmt_affected_rows($stmtSeats) === 0) {
            throw new Exception('Seat ' . $seatNo . ' was not updated (possibly already booked or does not exist for this flight/class).');
        }
    }
    mysqli_stmt_close($stmtSeats);

    // If all operations succeeded, commit the transaction
    mysqli_commit($connection);
    echo json_encode(['success' => true, 'message' => 'Booking confirmed and seats updated. Booking ID: ' . $flightBookingId, 'bookingId' => $flightBookingId]);

} catch (Exception $e) {
    // If any operation failed, rollback the transaction
    mysqli_rollback($connection);
    error_log("Booking Transaction Failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Booking failed: ' . $e->getMessage()]);
} finally {
    mysqli_autocommit($connection, true); // Restore autocommit mode
    mysqli_close($connection); // Close the database connection
}