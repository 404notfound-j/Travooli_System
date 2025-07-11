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
$guestName = $_POST['guestName'] ?? '';
$nationality = $_POST['nationality'] ?? '';
$phone = $_POST['phone'] ?? '';

// Validate required fields
if (empty($bookingId)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit();
}

// Parse guest name into first and last name
$nameParts = explode(' ', $guestName, 2);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';

// Get customer ID from booking
$getCustomerIdSql = "SELECT customer_id FROM hotel_booking_t WHERE h_book_id = ?";
$stmt = $connection->prepare($getCustomerIdSql);
$stmt->bind_param("s", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit();
}

$customerId = $result->fetch_assoc()['customer_id'];

// Update customer details - email field removed from update
$updateSql = "UPDATE customer_t SET 
              fst_name = ?, 
              lst_name = ?, 
              nationality = ?,
              phone_no = ? 
              WHERE customer_id = ?";

$stmt = $connection->prepare($updateSql);
$stmt->bind_param("sssss", $firstName, $lastName, $nationality, $phone, $customerId);

try {
    $success = $stmt->execute();
    
    if ($success) {
        // Get the current email from database since we're not updating it
        $getEmailSql = "SELECT email FROM customer_t WHERE customer_id = ?";
        $emailStmt = $connection->prepare($getEmailSql);
        $emailStmt->bind_param("s", $customerId);
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();
        $email = $emailResult->fetch_assoc()['email'];
        $emailStmt->close();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Guest details updated successfully',
            'data' => [
                'bookingId' => $bookingId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'fullName' => $guestName,
                'nationality' => $nationality,
                'email' => $email, // Return the unchanged email
                'phone' => $phone
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update guest details: ' . $stmt->error]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Close statement
$stmt->close();
$connection->close();
?> 