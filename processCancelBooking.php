<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Get booking ID and type from URL
$bookingId = isset($_GET['bookingId']) ? $_GET['bookingId'] : '';
$bookingType = isset($_GET['type']) ? $_GET['type'] : '';

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid request parameters'
];

// Process cancellation if booking ID and type are provided
if (!empty($bookingId)) {
    // Different handling based on booking type
    if ($bookingType === 'flight') {
        // Update flight booking status to 'Cancelled'
        $sql = "UPDATE flight_booking_t SET status = 'Cancelled' WHERE f_book_id = ?";
        
        $stmt = mysqli_prepare($connection, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $bookingId);
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Flight booking successfully cancelled'
                ];
                
                // Set success message in session for display
                $_SESSION['success_message'] = 'Flight booking #' . $bookingId . ' has been cancelled.';
            } else {
                $response['message'] = 'Error cancelling flight booking: ' . mysqli_error($connection);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Error preparing statement: ' . mysqli_error($connection);
        }
    } elseif ($bookingType === 'hotel') {
        // Update hotel booking status to 'Cancelled'
        $sql = "UPDATE hotel_booking_t SET status = 'Cancelled' WHERE h_book_id = ?";
        
        $stmt = mysqli_prepare($connection, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $bookingId);
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $response = [
                    'success' => true,
                    'message' => 'Hotel booking successfully cancelled'
                ];
                
                // Set success message in session for display
                $_SESSION['success_message'] = 'Hotel booking #' . $bookingId . ' has been cancelled.';
            } else {
                $response['message'] = 'Error cancelling hotel booking: ' . mysqli_error($connection);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Error preparing statement: ' . mysqli_error($connection);
        }
    } else {
        $response['message'] = 'Invalid booking type specified';
    }
}

// For AJAX requests, return JSON response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For regular requests, redirect back to appropriate page
if ($bookingType === 'flight') {
    header('Location: adminFlightBooking.php');
} elseif ($bookingType === 'hotel') {
    header('Location: adminHotelBooking.php');
} else {
    header('Location: adminDashboard.php');
}
exit;
?> 