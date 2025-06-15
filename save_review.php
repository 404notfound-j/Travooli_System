<?php
// This file handles saving reviews submitted from hotelPaymentComplete.php and payment_complete.php
session_start();

// Get the review data from POST
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';
$user = isset($_POST['user']) ? trim($_POST['user']) : 'Anonymous';
$type = isset($_POST['type']) ? trim($_POST['type']) : 'hotel'; // 'hotel' or 'flight'

// Validate the data
$response = array('success' => false, 'message' => '');

if ($rating < 1 || $rating > 5) {
    $response['message'] = 'Invalid rating. Please provide a rating between 1 and 5.';
    echo json_encode($response);
    exit;
}

if (empty($review)) {
    $response['message'] = 'Please provide a review text.';
    echo json_encode($response);
    exit;
}

// In a real application, you would save this to a database
// For this demo, we'll store it in a session variable based on type
$sessionKey = ($type == 'flight') ? 'flight_reviews' : 'hotel_reviews';

if (!isset($_SESSION[$sessionKey])) {
    $_SESSION[$sessionKey] = array();
}

// Create a new review entry
$newReview = array(
    'user' => $user,
    'rating' => $rating,
    'review' => $review,
    'date' => date('Y-m-d H:i:s')
);

// Add the review to the session
array_unshift($_SESSION[$sessionKey], $newReview); // Add to beginning of array

// Limit to 10 reviews
if (count($_SESSION[$sessionKey]) > 10) {
    $_SESSION[$sessionKey] = array_slice($_SESSION[$sessionKey], 0, 10);
}

// Return success response
$response['success'] = true;
$response['message'] = 'Review saved successfully.';
echo json_encode($response);
?> 