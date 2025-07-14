<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    if ($isRedirect) {
        // Redirect to noFlightBooking.php if no booking ID and this is a redirect request
        header("Location: noFlightBooking.php");
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'No booking ID found',
            'debug' => $debug
        ]);
        exit;
    }
}

$debug['booking_id_found'] = $bookingId;

// Process as a single booking ID
$f_book_id = $bookingId;

try {
    // First, check if the booking exists
    $checkBookingQuery = "SELECT * FROM flight_booking_t WHERE f_book_id = ?";
    $stmt = mysqli_prepare($connection, $checkBookingQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed for check booking query: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $f_book_id);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Execute failed for check booking query: " . mysqli_stmt_error($stmt));
    }
    
    $bookingResult = mysqli_stmt_get_result($stmt);
    $bookingData = mysqli_fetch_assoc($bookingResult);
    mysqli_stmt_close($stmt);
    
    if (!$bookingData) {
        throw new Exception("Booking ID not found in database: $f_book_id");
    }
    
    // Get the flight ID from the booking
    $flightId = $bookingData['flight_id'];
    
    // Try to update seat information, but continue even if this fails
    $seatUpdateSuccess = true;
    $updateSeatsClsSuccess = false;
    $updateSeatsTSuccess = false;
    
    try {
        // Now retrieve seat information from flight_booking_info_t table
        $seatInfoQuery = "SELECT fb.flight_id, fbi.class_id, fbi.passenger_count 
                        FROM flight_booking_t fb 
                        JOIN flight_booking_info_t fbi ON fb.f_book_id = fbi.f_book_id 
                        WHERE fb.f_book_id = ?";
        
        $stmt = mysqli_prepare($connection, $seatInfoQuery);
        if (!$stmt) {
            throw new Exception("Prepare failed for seat info query: " . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($stmt, "s", $f_book_id);
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            throw new Exception("Execute failed for seat info query: " . mysqli_stmt_error($stmt));
        }
        
        $seatInfoResult = mysqli_stmt_get_result($stmt);
        $seatInfo = mysqli_fetch_assoc($seatInfoResult);
        mysqli_stmt_close($stmt);
    
        // If we couldn't get the info from flight_booking_info_t, try passenger_service_t
        if (!$seatInfo) {
            $passengerServiceQuery = "SELECT ps.class_id, COUNT(*) as passenger_count 
                                    FROM passenger_service_t ps 
                                    WHERE ps.f_book_id = ? 
                                    GROUP BY ps.class_id";
            
            $stmt = mysqli_prepare($connection, $passengerServiceQuery);
            if (!$stmt) {
                throw new Exception("Prepare failed for passenger service query: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, "s", $f_book_id);
            $result = mysqli_stmt_execute($stmt);
            if (!$result) {
                throw new Exception("Execute failed for passenger service query: " . mysqli_stmt_error($stmt));
            }
            
            $passengerResult = mysqli_stmt_get_result($stmt);
            $seatInfo = mysqli_fetch_assoc($passengerResult);
            mysqli_stmt_close($stmt);
            
            if ($seatInfo) {
                $seatInfo['flight_id'] = $flightId; // Add flight_id from booking
            }
        }
    
        // Update seat availability
        if ($seatInfo) {
            $flightId = $seatInfo['flight_id'];
            $seatClass = $seatInfo['class_id']; 
            $seatsToRestore = $seatInfo['passenger_count'];
    
            // 4a. Update seat availability in flight_seats_cls_t
            $updateSeatsCls = "UPDATE flight_seats_cls_t 
                            SET available_seats = available_seats + ? 
                            WHERE flight_id = ? AND class_id = ?";
            $stmt = mysqli_prepare($connection, $updateSeatsCls);
            if (!$stmt) {
                throw new Exception("Prepare failed for update seats class: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, "iss", $seatsToRestore, $flightId, $seatClass);
            $updateSeatsClsSuccess = mysqli_stmt_execute($stmt);
            if (!$updateSeatsClsSuccess) {
                throw new Exception("Execute failed for update seats class: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
    
            // 4b. Free the specific seats in flight_seats_t (if you assign specific seats by booking)
            $updateSeatsT = "UPDATE flight_seats_t 
                            SET is_booked = 0, f_book_id = NULL 
                            WHERE f_book_id = ?";
            $stmt = mysqli_prepare($connection, $updateSeatsT);
            if (!$stmt) {
                throw new Exception("Prepare failed for update seats: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt, "s", $f_book_id);
            $updateSeatsTSuccess = mysqli_stmt_execute($stmt);
            if (!$updateSeatsTSuccess) {
                throw new Exception("Execute failed for update seats: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            // Just free the seats without updating class availability as a fallback
            $updateSeatsT = "UPDATE flight_seats_t 
                            SET is_booked = 0, f_book_id = NULL 
                            WHERE f_book_id = ?";
            $stmt = mysqli_prepare($connection, $updateSeatsT);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $f_book_id);
                $updateSeatsTSuccess = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } catch (Exception $seatException) {
        // Seat update failed but continue with cancellation
        $seatUpdateSuccess = false;
    }

    // 1. Insert refund record
    $lastIdQuery = "SELECT f_refund_id FROM flight_refund_t ORDER BY f_refund_id DESC LIMIT 1";
    $lastResult = mysqli_query($connection, $lastIdQuery);
    if (!$lastResult) {
        throw new Exception("Failed to get last refund ID: " . mysqli_error($connection));
    }
    
    $lastRow = mysqli_fetch_assoc($lastResult);
    $newNum = $lastRow ? ((int)substr($lastRow['f_refund_id'], 2)) + 1 : 1;
    $newRefundId = "FR" . str_pad($newNum, 3, "0", STR_PAD_LEFT);

    // Get original payment amount
    $paymentQuery = "SELECT amount FROM flight_payment_t WHERE f_book_id = ?";
    $stmt = mysqli_prepare($connection, $paymentQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed for payment query: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $f_book_id);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        throw new Exception("Execute failed for payment query: " . mysqli_stmt_error($stmt));
    }
    
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
    if (!$stmt) {
        throw new Exception("Prepare failed for insert refund: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "ssdsss", $newRefundId, $f_book_id, $refundAmt, $refundMethod, $refundDate, $refundStatus);
    $insertSuccess = mysqli_stmt_execute($stmt);
    if (!$insertSuccess) {
        throw new Exception("Execute failed for insert refund: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // 2. Update booking status
    $updateBooking = "UPDATE flight_booking_t SET status = 'cancelled' WHERE f_book_id = ?";
    $stmt = mysqli_prepare($connection, $updateBooking);
    if (!$stmt) {
        throw new Exception("Prepare failed for update booking: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $f_book_id);
    $updateBookingSuccess = mysqli_stmt_execute($stmt);
    if (!$updateBookingSuccess) {
        throw new Exception("Execute failed for update booking: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // 3. Update payment status
    $updatePayment = "UPDATE flight_payment_t SET payment_status = 'refunded' WHERE f_book_id = ?";
    $stmt = mysqli_prepare($connection, $updatePayment);
    if (!$stmt) {
        throw new Exception("Prepare failed for update payment: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $f_book_id);
    $updatePaymentSuccess = mysqli_stmt_execute($stmt);
    if (!$updatePaymentSuccess) {
        throw new Exception("Execute failed for update payment: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // Check if user has any remaining active bookings
    $userId = $_SESSION['user_id'] ?? null;
    $hasMoreBookings = false;
    
    if ($userId) {
        $activeBookingsQuery = "SELECT COUNT(*) as booking_count FROM flight_booking_t 
                               WHERE user_id = ? AND status = 'confirmed'";
        $stmt = mysqli_prepare($connection, $activeBookingsQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $hasMoreBookings = ($row['booking_count'] > 0);
            mysqli_stmt_close($stmt);
        }
    }

    // If this is a direct browser request (not AJAX), redirect to appropriate page
    if ($isRedirect) {
        if ($hasMoreBookings) {
            header("Location: payment_complete.php");
        } else {
            header("Location: noFlightBooking.php");
        }
        exit();
    }

    // Return success message with debug info for AJAX requests
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => "Your booking has been successfully cancelled and refund processed.",
        'booking_id' => $f_book_id,
        'has_more_bookings' => $hasMoreBookings,
        'redirect_url' => $hasMoreBookings ? 'payment_complete.php' : 'noFlightBooking.php',
        'debug' => $debug,
        'operations' => [
            'insert_refund' => $insertSuccess,
            'update_booking' => $updateBookingSuccess,
            'update_payment' => $updatePaymentSuccess,
            'update_seat_class' => $updateSeatsClsSuccess,
            'update_individual_seats' => $updateSeatsTSuccess,
            'seat_update_attempted' => $seatUpdateSuccess
        ]
    ]);

} catch (Exception $e) {
    // If this is a direct browser request (not AJAX), redirect to error page
    if ($isRedirect) {
        $_SESSION['error_message'] = "Error processing cancellation: " . $e->getMessage();
        header("Location: error.php");
        exit();
    }

    // Return error message for AJAX requests
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => "Error processing cancellation: " . $e->getMessage(),
        'booking_id' => $f_book_id ?? null,
        'debug' => $debug
    ]);
}
?>