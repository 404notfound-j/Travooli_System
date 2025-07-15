<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

$response = [
    'avg_rating' => 0,
    'total_reviews' => 0,
    'rating_text' => 'No reviews yet',
    'feedbacks' => [],
    'can_leave_feedback' => false,
    'booking_id' => ''
];

// Check if airline_id is provided
if (!isset($_GET['airline_id']) || empty($_GET['airline_id'])) {
    echo json_encode($response);
    exit;
}

$airline_id = mysqli_real_escape_string($connection, $_GET['airline_id']);

// Get average rating and total reviews
$sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total 
        FROM flight_feedback_t 
        WHERE airline_id = '$airline_id'";
$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row['total'] > 0) {
        $response['avg_rating'] = round($row['avg_rating'], 1);
        $response['total_reviews'] = $row['total'];
        
        // Determine rating text
        if ($response['avg_rating'] >= 4.5) {
            $response['rating_text'] = "Excellent";
        } else if ($response['avg_rating'] >= 4.0) {
            $response['rating_text'] = "Excellent";
        } else if ($response['avg_rating'] >= 3.0) {
            $response['rating_text'] = "Good";
        } else if ($response['avg_rating'] >= 2.0) {
            $response['rating_text'] = "Fair";
        } else {
            $response['rating_text'] = "Poor";
        }
    }
}

// Get feedbacks for this airline
$sql = "SELECT f.*, u.fst_name, u.lst_name 
        FROM flight_feedback_t f 
        JOIN user_detail_t u ON f.user_id = u.user_id 
        WHERE f.airline_id = '$airline_id' 
        ORDER BY f_feedback_id DESC";
$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response['feedbacks'][] = $row;
    }
}

// Check if user is logged in and can leave feedback
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    
    // Get flights for this airline that the user has booked
    $sql = "SELECT fb.f_book_id, fb.flight_id 
            FROM flight_booking_t fb 
            JOIN flight_info_t fi ON fb.flight_id = fi.flight_id 
            WHERE fb.user_id = '$user_id' 
            AND fi.airline_id = '$airline_id' 
            AND fb.status = 'confirmed'";
    $result = mysqli_query($connection, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $booking_id = $row['f_book_id'];
            
            // Check if user has already left feedback for this booking
            $feedback_sql = "SELECT f_feedback_id FROM flight_feedback_t 
                            WHERE user_id = '$user_id' 
                            AND f_book_id = '$booking_id'";
            $feedback_result = mysqli_query($connection, $feedback_sql);
            
            if ($feedback_result && mysqli_num_rows($feedback_result) == 0) {
                // User can leave feedback
                $response['can_leave_feedback'] = true;
                $response['booking_id'] = $booking_id;
                break;
            }
        }
    }
}

echo json_encode($response);
?> 