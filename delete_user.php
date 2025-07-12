<?php
header('Content-Type: application/json');
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id']) || empty($_POST['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit();
}

$userId = $_POST['user_id'];

mysqli_begin_transaction($connection);
try {
    // Fetch all hotel bookings for the user 
    $stmt = mysqli_prepare($connection, "SELECT h_book_id, customer_id FROM hotel_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $hotelBookIds = [];
    $customerIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $hotelBookIds[] = $row['h_book_id'];
        if (!empty($row['customer_id'])) {
            $customerIds[] = $row['customer_id'];
        }
    }
    mysqli_stmt_close($stmt);

    if (!empty($hotelBookIds)) {
        $in = implode(',', array_fill(0, count($hotelBookIds), '?'));
        $types = str_repeat('s', count($hotelBookIds));

        // Delete hotel payments
        $stmt = mysqli_prepare($connection, "DELETE FROM hotel_payment_t WHERE h_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$hotelBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete hotel refunds
        $stmt = mysqli_prepare($connection, "DELETE FROM hotel_refund_t WHERE h_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$hotelBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete hotel feedback
        $stmt = mysqli_prepare($connection, "DELETE FROM hotel_feedback_t WHERE h_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$hotelBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Delete customers (if any)
    if (!empty($customerIds)) {
        $in = implode(',', array_fill(0, count($customerIds), '?'));
        $types = str_repeat('s', count($customerIds));
        $stmt = mysqli_prepare($connection, "DELETE FROM customer_t WHERE customer_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$customerIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Delete hotel bookings
    $stmt = mysqli_prepare($connection, "DELETE FROM hotel_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Fetch all flight bookings for the user
    $stmt = mysqli_prepare($connection, "SELECT f_book_id FROM flight_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flightBookIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $flightBookIds[] = $row['f_book_id'];
    }
    mysqli_stmt_close($stmt);

    if (!empty($flightBookIds)) {
        $in = implode(',', array_fill(0, count($flightBookIds), '?'));
        $types = str_repeat('s', count($flightBookIds));

        // Delete from flight_booking_info_t
        $stmt = mysqli_prepare($connection, "DELETE FROM flight_booking_info_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from flight_payment_t
        $stmt = mysqli_prepare($connection, "DELETE FROM flight_payment_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from flight_refund_t
        $stmt = mysqli_prepare($connection, "DELETE FROM flight_refund_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from passenger_service_t and collect pass_id
        $passengerIds = [];
        $stmt = mysqli_prepare($connection, "SELECT pass_id FROM passenger_service_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $passengerIds[] = $row['pass_id'];
        }
        mysqli_stmt_close($stmt);

        // Delete from passenger_service_t
        $stmt = mysqli_prepare($connection, "DELETE FROM passenger_service_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from passenger_t if any
        if (!empty($passengerIds)) {
            $inPass = implode(',', array_fill(0, count($passengerIds), '?'));
            $typesPass = str_repeat('s', count($passengerIds));
            $stmt = mysqli_prepare($connection, "DELETE FROM passenger_t WHERE pass_id IN ($inPass)");
            mysqli_stmt_bind_param($stmt, $typesPass, ...$passengerIds);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Delete from flight_feedback_t (by user_id)
    $stmt = mysqli_prepare($connection, "DELETE FROM flight_feedback_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete from flight_booking_t
    $stmt = mysqli_prepare($connection, "DELETE FROM flight_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete from user_detail_t
    $stmt = mysqli_prepare($connection, "DELETE FROM user_detail_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($connection);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    mysqli_rollback($connection);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 
?>