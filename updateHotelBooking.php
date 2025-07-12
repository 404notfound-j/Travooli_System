<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$bookingId = $_POST['bookingId'] ?? '';
$action = $_POST['action'] ?? '';

// Validate required fields
if (empty($bookingId)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit();
}

// Handle different actions
if ($action === 'cancel') {
    // Begin transaction
    $connection->begin_transaction();
    
    try {
        // 1. Get payment details from hotel_payment_t
        $paymentSql = "SELECT amount, method FROM hotel_payment_t WHERE h_book_id = ?";
        $paymentStmt = $connection->prepare($paymentSql);
        $paymentStmt->bind_param("s", $bookingId);
        $paymentStmt->execute();
        $paymentResult = $paymentStmt->get_result();
        
        if ($paymentResult->num_rows === 0) {
            throw new Exception("Payment record not found for booking ID: $bookingId");
        }
        
        $paymentData = $paymentResult->fetch_assoc();
        $refundAmount = $paymentData['amount'];
        $refundMethod = $paymentData['method'];
        
        // 2. Generate a new refund ID
        $lastRefundQuery = "SELECT h_refund_id FROM hotel_refund_t ORDER BY h_refund_id DESC LIMIT 1";
        $lastRefundResult = $connection->query($lastRefundQuery);
        
        if ($lastRefundResult && $lastRefundResult->num_rows > 0) {
            $lastRefundId = $lastRefundResult->fetch_assoc()['h_refund_id'];
            $numericPart = (int)substr($lastRefundId, 2); // Extract numeric part after 'HF'
            $newNumericPart = $numericPart + 1;
            $newRefundId = 'HF' . str_pad($newNumericPart, 4, '0', STR_PAD_LEFT);
        } else {
            $newRefundId = 'HF0001';
        }
        
        // 3. Get current date for refund date
        $refundDate = date('Y-m-d H:i:s');
        
        // 4. Insert record into hotel_refund_t
        $refundSql = "INSERT INTO hotel_refund_t (h_refund_id, h_book_id, refund_amt, refund_date, status, refund_method) 
                      VALUES (?, ?, ?, ?, 'Completed', ?)";
        $refundStmt = $connection->prepare($refundSql);
        $refundStmt->bind_param("ssdss", $newRefundId, $bookingId, $refundAmount, $refundDate, $refundMethod);
        
        if (!$refundStmt->execute()) {
            throw new Exception("Failed to create refund record: " . $refundStmt->error);
        }
        
        // 5. Update booking status to cancelled in hotel_booking_t
        $updateBookingSql = "UPDATE hotel_booking_t SET status = 'Cancelled' WHERE h_book_id = ?";
        $updateBookingStmt = $connection->prepare($updateBookingSql);
        $updateBookingStmt->bind_param("s", $bookingId);
        
        if (!$updateBookingStmt->execute()) {
            throw new Exception("Failed to update booking status: " . $updateBookingStmt->error);
        }
        
        // 6. Update payment status to refunded in hotel_payment_t
        $updatePaymentSql = "UPDATE hotel_payment_t SET status = 'Refunded' WHERE h_book_id = ?";
        $updatePaymentStmt = $connection->prepare($updatePaymentSql);
        $updatePaymentStmt->bind_param("s", $bookingId);
        
        if (!$updatePaymentStmt->execute()) {
            throw new Exception("Failed to update payment status: " . $updatePaymentStmt->error);
        }
        
        // If we got here, commit the transaction
        $connection->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Booking cancelled and refund processed successfully',
            'data' => [
                'bookingId' => $bookingId,
                'refundId' => $newRefundId
            ]
        ]);
        
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $connection->rollback();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$connection->close();
?> 