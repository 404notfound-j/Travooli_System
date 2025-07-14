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
        // Get the last feedback ID and increment it
        $last_id_query = "SELECT f_feedback_id FROM flight_feedback_t ORDER BY f_feedback_id DESC LIMIT 1";
        $last_id_result = mysqli_query($connection, $last_id_query);
        
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
            
            // Update the existing feedback instead of inserting a new one
            $update_query = "UPDATE flight_feedback_t 
                            SET rating = ?, feedback = ? 
                            WHERE f_feedback_id = ?";
            
            $stmt_update = mysqli_prepare($connection, $update_query);
            if (!$stmt_update) {
                throw new Exception("Prepare failed for update: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($stmt_update, "sss", $rating, $sanitized_feedback, $feedback_id);
            
            if (!mysqli_stmt_execute($stmt_update)) {
                throw new Exception("Execute failed for update: " . mysqli_stmt_error($stmt_update));
            }
            
            mysqli_stmt_close($stmt_update);
            mysqli_commit($connection);
            $response = ['success' => true, 'message' => 'Feedback updated successfully'];
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        mysqli_stmt_close($stmt_check);
        
        // Generate a new feedback ID
        $feedback_id = 'FB0001'; // Default first ID with FB prefix
        
        if (mysqli_num_rows($last_id_result) > 0) {
            $last_id_row = mysqli_fetch_assoc($last_id_result);
            $last_id = $last_id_row['f_feedback_id'];
            
            // Extract the numeric part and increment
            $numeric_part = intval(substr($last_id, 2));
            $new_numeric_part = $numeric_part + 1;
            $feedback_id = 'FB' . str_pad($new_numeric_part, 4, '0', STR_PAD_LEFT);
        } else {
            // If there are no existing records, check if FB0001 already exists
            $check_first_id = "SELECT 1 FROM flight_feedback_t WHERE f_feedback_id = 'FB0001'";
            $first_id_result = mysqli_query($connection, $check_first_id);
            
            if (mysqli_num_rows($first_id_result) > 0) {
                // FB0001 exists, find the next available ID
                $find_gap_query = "SELECT MIN(t1.f_feedback_id_num + 1) AS next_id
                                  FROM (
                                      SELECT CAST(SUBSTRING(f_feedback_id, 3) AS UNSIGNED) AS f_feedback_id_num
                                      FROM flight_feedback_t
                                  ) t1
                                  LEFT JOIN (
                                      SELECT CAST(SUBSTRING(f_feedback_id, 3) AS UNSIGNED) AS f_feedback_id_num
                                      FROM flight_feedback_t
                                  ) t2 ON t1.f_feedback_id_num + 1 = t2.f_feedback_id_num
                                  WHERE t2.f_feedback_id_num IS NULL";
                
                $gap_result = mysqli_query($connection, $find_gap_query);
                
                if ($gap_result && $gap_row = mysqli_fetch_assoc($gap_result)) {
                    $next_id = $gap_row['next_id'];
                    if ($next_id) {
                        $feedback_id = 'FB' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
                    } else {
                        // If no gap found, get the max ID and add 1
                        $max_id_query = "SELECT MAX(CAST(SUBSTRING(f_feedback_id, 3) AS UNSIGNED)) + 1 AS next_id 
                                        FROM flight_feedback_t";
                        $max_id_result = mysqli_query($connection, $max_id_query);
                        
                        if ($max_id_result && $max_row = mysqli_fetch_assoc($max_id_result)) {
                            $next_id = $max_row['next_id'] ?: 1;
                            $feedback_id = 'FB' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
        }
        
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