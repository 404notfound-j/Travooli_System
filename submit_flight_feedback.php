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
    
    // Debug - Log the received values
    error_log("Received feedback data - f_book_id: $f_book_id, airline_id: $airline_id, rating: $rating");
    
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
        // Check if the feedback already exists for this booking
        $check_existing_query = "SELECT f_feedback_id FROM flight_feedback_t WHERE f_book_id = ? AND user_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_existing_query);
        if (!$stmt_check) {
            throw new Exception("Prepare failed for check existing: " . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($stmt_check, "ss", $f_book_id, $user_id);
        mysqli_stmt_execute($stmt_check);
        $check_result = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Feedback already exists for this booking and user
            $existing_row = mysqli_fetch_assoc($check_result);
            $feedback_id = $existing_row['f_feedback_id'];
            
            // User can only submit one feedback per booking
            mysqli_stmt_close($stmt_check);
            mysqli_rollback($connection);
            $response = ['success' => false, 'message' => 'You have already submitted feedback for this flight'];
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        mysqli_stmt_close($stmt_check);
        
        // Generate a new feedback ID with retry mechanism to handle duplicates
        $max_attempts = 5;
        $attempt = 0;
        $feedback_id = null;
        
        while ($attempt < $max_attempts && $feedback_id === null) {
            // Get the maximum ID and increment it
            $max_id_query = "SELECT MAX(CAST(SUBSTRING(f_feedback_id, 3) AS UNSIGNED)) AS max_id FROM flight_feedback_t";
            $max_id_result = mysqli_query($connection, $max_id_query);
            
            if ($max_id_result && $max_id_row = mysqli_fetch_assoc($max_id_result)) {
                $max_id = $max_id_row['max_id'] ? (int)$max_id_row['max_id'] : 0;
                $new_id = $max_id + 1;
                $candidate_id = 'FB' . str_pad($new_id, 4, '0', STR_PAD_LEFT);
                
                // Check if this ID already exists
                $check_id_query = "SELECT 1 FROM flight_feedback_t WHERE f_feedback_id = ?";
                $stmt_check_id = mysqli_prepare($connection, $check_id_query);
                mysqli_stmt_bind_param($stmt_check_id, "s", $candidate_id);
                mysqli_stmt_execute($stmt_check_id);
                $check_id_result = mysqli_stmt_get_result($stmt_check_id);
                
                if (mysqli_num_rows($check_id_result) == 0) {
                    // ID is available
                    $feedback_id = $candidate_id;
                } else {
                    // ID exists, try next one
                    $attempt++;
                }
                
                mysqli_stmt_close($stmt_check_id);
            } else {
                // If query fails, use a fallback approach
                $attempt++;
                $feedback_id = 'FB' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }
        }
        
        if ($feedback_id === null) {
            throw new Exception("Failed to generate a unique feedback ID after $max_attempts attempts");
        }
        
        // Debug - Log the generated ID
        error_log("Generated feedback ID: $feedback_id for booking: $f_book_id, airline: $airline_id");
        
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
        error_log("Error in submit_flight_feedback.php: " . $e->getMessage());
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