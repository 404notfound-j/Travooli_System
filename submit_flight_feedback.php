<?php
session_start();
include 'connection.php';

// Check if request is POST and action is submitFeedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submitFeedback') {
    $response = ['success' => false, 'message' => 'Unknown error'];
    
    // Get user_id from session
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if (!$user_id) {
        $response = ['success' => false, 'message' => 'User not logged in'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Get parameters from POST
    $f_book_id = isset($_POST['f_book_id']) ? $_POST['f_book_id'] : '';
    $airline_id = isset($_POST['airline_id']) ? $_POST['airline_id'] : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $feedback = isset($_POST['feedback']) ? $_POST['feedback'] : '';
    
    // Validate inputs
    if (empty($f_book_id)) {
        $response = ['success' => false, 'message' => 'Missing booking ID'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    if (empty($airline_id)) {
        $response = ['success' => false, 'message' => 'Missing airline ID'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    if ($rating < 1 || $rating > 5) {
        $response = ['success' => false, 'message' => 'Rating must be between 1 and 5'];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Begin transaction
    mysqli_begin_transaction($connection);
    
    try {
        // Check if user has already submitted feedback for this booking
        $check_existing_query = "SELECT COUNT(*) as count FROM flight_feedback_t WHERE user_id = ? AND f_book_id = ?";
        $check_stmt = mysqli_prepare($connection, $check_existing_query);
        if (!$check_stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($check_stmt, "ss", $user_id, $f_book_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $check_row = mysqli_fetch_assoc($check_result);
        mysqli_stmt_close($check_stmt);
        
        if ($check_row['count'] > 0) {
            throw new Exception("You have already submitted feedback for this booking");
        }
        
        // Completely rewritten ID generation logic to avoid duplicates
        // Find the highest numeric part in existing IDs
        $max_id_query = "SELECT MAX(CAST(SUBSTRING(f_feedback_id, 3) AS UNSIGNED)) AS max_id FROM flight_feedback_t";
        $max_id_result = mysqli_query($connection, $max_id_query);
        $max_id_row = mysqli_fetch_assoc($max_id_result);
        
        // If we found a maximum ID, use it + 1, otherwise start at 1
        $next_id = 1;
        if ($max_id_row && $max_id_row['max_id']) {
            $next_id = intval($max_id_row['max_id']) + 1;
        }
        
        // Format the new ID with FB prefix and padded zeros
        $feedback_id = 'FB' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
        
        // Double-check that this ID doesn't already exist (just to be safe)
        $check_query = "SELECT 1 FROM flight_feedback_t WHERE f_feedback_id = ? LIMIT 1";
        $check_stmt = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $feedback_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        // If by some chance this ID exists, keep incrementing until we find an unused one
        while (mysqli_stmt_num_rows($check_stmt) > 0) {
            $next_id++;
            $feedback_id = 'FB' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            mysqli_stmt_bind_param($check_stmt, "s", $feedback_id);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
        }
        
        mysqli_stmt_close($check_stmt);
        
        // Prepare the feedback insert query with sanitized inputs
        $sanitized_feedback = mysqli_real_escape_string($connection, $feedback);
        
        $insert_query = "INSERT INTO flight_feedback_t (f_feedback_id, user_id, airline_id, f_book_id, rating, feedback) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($connection, $insert_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($stmt, "ssssss", $feedback_id, $user_id, $airline_id, $f_book_id, $rating, $sanitized_feedback);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        
        // If we got here, commit the transaction
        mysqli_commit($connection);
        $response = ['success' => true, 'message' => 'Feedback submitted successfully'];
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($connection);
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// If not a POST request or not the right action, return error
$response = ['success' => false, 'message' => 'Invalid request'];
header('Content-Type: application/json');
echo json_encode($response);
exit();
?> 