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
    // Delete from admin_detail_t (if present)
    $stmt = mysqli_prepare($connection, "DELETE FROM admin_detail_t WHERE admin_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete hotel payments (by booking)
    $stmt = mysqli_prepare($connection, "SELECT h_book_id FROM hotel_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $hotelBookIds = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $hotelBookIds[] = $row['h_book_id'];
    }
    mysqli_stmt_close($stmt);
    if (!empty($hotelBookIds)) {
        $in = implode(',', array_fill(0, count($hotelBookIds), '?'));
        $types = str_repeat('s', count($hotelBookIds));
        $stmt = mysqli_prepare($connection, "DELETE FROM hotel_payment_t WHERE h_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$hotelBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // Delete hotel bookings
    $stmt = mysqli_prepare($connection, "DELETE FROM hotel_booking_t WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete flight payments (by booking)
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
        $stmt = mysqli_prepare($connection, "DELETE FROM flight_payment_t WHERE f_book_id IN ($in)");
        mysqli_stmt_bind_param($stmt, $types, ...$flightBookIds);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // Delete flight bookings
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