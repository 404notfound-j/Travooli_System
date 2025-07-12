<?php
// This file handles saving reviews submitted from hotelPaymentComplete.php
session_start();
include 'connection.php';

// Get POST data
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$review = isset($_POST['review']) ? $_POST['review'] : '';
$booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
$hotel_id = isset($_POST['hotel_id']) ? $_POST['hotel_id'] : '';
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';

// Validate data
if ($rating < 1 || $rating > 5 || empty($review) || empty($booking_id) || empty($hotel_id) || empty($customer_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit;
}

try {
    // Start transaction
    mysqli_begin_transaction($connection);
    
    // Check if customer has already submitted a review for this hotel
    $check_query = "SELECT COUNT(*) AS review_count FROM hotel_feedback_t 
                    WHERE customer_id = '$customer_id' AND hotel_id = '$hotel_id'";
    $check_result = mysqli_query($connection, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);
    
    if ($check_row['review_count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted a review for this hotel']);
        mysqli_rollback($connection);
        exit;
    }
    
    // Generate feedback ID
    $feedback_id_query = "SELECT MAX(SUBSTRING(h_feedback_id, 3)) as max_id FROM hotel_feedback_t";
    $result = mysqli_query($connection, $feedback_id_query);
    $row = mysqli_fetch_assoc($result);
    $next_id = str_pad((int)$row['max_id'] + 1, 4, '0', STR_PAD_LEFT);
    $h_feedback_id = "HF" . $next_id;
    
    // Prepare data for insertion
    $h_feedback_id = mysqli_real_escape_string($connection, $h_feedback_id);
    $customer_id = mysqli_real_escape_string($connection, $customer_id);
    $hotel_id = mysqli_real_escape_string($connection, $hotel_id);
    $booking_id = mysqli_real_escape_string($connection, $booking_id);
    $rating = (int)$rating;
    $review = mysqli_real_escape_string($connection, $review);
    
    // Insert feedback
    $insert_query = "INSERT INTO hotel_feedback_t (h_feedback_id, customer_id, hotel_id, h_book_id, rating, feedback) 
                    VALUES ('$h_feedback_id', '$customer_id', '$hotel_id', '$booking_id', $rating, '$review')";
    
    if (!mysqli_query($connection, $insert_query)) {
        throw new Exception("Error inserting feedback: " . mysqli_error($connection));
    }
    
    // Commit the transaction
    mysqli_commit($connection);
    
    echo json_encode(['success' => true, 'message' => 'Feedback saved successfully']);
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($connection);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Close connection
mysqli_close($connection);
?> 