<?php
session_start();
include 'connection.php';

$bookingIds = $_SESSION['booking_ids'] ?? [];

if (empty($bookingIds)) {
    echo "No booking found.";
    exit;
}

foreach ($bookingIds as $f_book_id) {
    // 1. Insert refund record
    $lastIdQuery = "SELECT f_refund_id FROM flight_refund_t ORDER BY f_refund_id DESC LIMIT 1";
    $lastResult = mysqli_query($connection, $lastIdQuery);
    $lastRow = mysqli_fetch_assoc($lastResult);
    $newNum = $lastRow ? ((int)substr($lastRow['f_refund_id'], 2)) + 1 : 1;
    $newRefundId = "FR" . str_pad($newNum, 3, "0", STR_PAD_LEFT);

    // Get original payment amount
    $paymentQuery = "SELECT amount FROM flight_payment_t WHERE f_book_id = '$f_book_id' LIMIT 1";
    $paymentResult = mysqli_query($connection, $paymentQuery);
    $paymentRow = mysqli_fetch_assoc($paymentResult);
    $refundAmt = $paymentRow['amount'] ?? 0;

    $refundMethod = "credit"; 
    $refundDate = date("Y-m-d");
    $refundStatus = "complete";

    $insertRefund = "INSERT INTO flight_refund_t (f_refund_id, f_book_id, refund_amt, refund_method, refund_date, status)
                     VALUES ('$newRefundId', '$f_book_id', '$refundAmt', '$refundMethod', '$refundDate', '$refundStatus')";
    mysqli_query($connection, $insertRefund);

    // 2. Update booking status
    $updateBooking = "UPDATE flight_booking_t SET status = 'cancelled' WHERE f_book_id = '$f_book_id'";
    mysqli_query($connection, $updateBooking);

    // 3. Update payment status
    $updatePayment = "UPDATE flight_payment_t SET payment_status = 'refunded' WHERE f_book_id = '$f_book_id'";
    mysqli_query($connection, $updatePayment);
}

echo "Your booking has been successfully cancelled and refund processed.";
?>
