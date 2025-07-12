<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the JSON data from the request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Validate input data
if (!$data || !isset($data['booking_id']) || !isset($data['passengers'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Get booking ID
$booking_id = $data['booking_id'];

// Initialize response
$response = ['success' => true, 'message' => 'Changes saved successfully'];

// Begin transaction
mysqli_begin_transaction($connection);

try {
    // Update payment amount if changed
    if (isset($data['final_total']) && $data['final_total'] > 0) {
        // Get the final total amount (new total + additional charges)
        $final_total = $data['final_total'];
        
        $update_payment_sql = "UPDATE flight_payment_t 
                              SET amount = ? 
                              WHERE f_book_id = ?";
        
        $payment_stmt = mysqli_prepare($connection, $update_payment_sql);
        if (!$payment_stmt) {
            throw new Exception("Error preparing payment update statement: " . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($payment_stmt, "ds", $final_total, $booking_id);
        
        if (!mysqli_stmt_execute($payment_stmt)) {
            throw new Exception("Error updating payment: " . mysqli_stmt_error($payment_stmt));
        }
        
        mysqli_stmt_close($payment_stmt);
    }
    
    // Process passenger updates
    foreach ($data['passengers'] as $index => $passenger) {
        // Skip if no changes to class or meal
        if ($passenger['class_id'] == $passenger['original_class_id'] && 
            $passenger['meal_type'] == $passenger['original_meal']) {
            continue;
        }
        
        // Get passenger ID - In a real implementation, you would need to get this more reliably
        // For now, we'll get the passenger ID based on the booking ID and row index
        $passenger_sql = "SELECT p.pass_id 
                         FROM passenger_t p 
                         JOIN passenger_service_t ps ON p.pass_id = ps.pass_id 
                         WHERE ps.f_book_id = ? 
                         ORDER BY p.pass_id 
                         LIMIT ?, 1";
        
        $pass_stmt = mysqli_prepare($connection, $passenger_sql);
        if (!$pass_stmt) {
            throw new Exception("Error preparing passenger query: " . mysqli_error($connection));
        }
        
        $offset = $index;
        mysqli_stmt_bind_param($pass_stmt, "si", $booking_id, $offset);
        
        if (!mysqli_stmt_execute($pass_stmt)) {
            throw new Exception("Error fetching passenger ID: " . mysqli_stmt_error($pass_stmt));
        }
        
        $pass_result = mysqli_stmt_get_result($pass_stmt);
        $pass_row = mysqli_fetch_assoc($pass_result);
        
        if (!$pass_row) {
            throw new Exception("Passenger not found at index $index");
        }
        
        $pass_id = $pass_row['pass_id'];
        mysqli_stmt_close($pass_stmt);
        
        // Get seat class ID directly from the request
        $class_id = null;
        if ($passenger['class_id'] != $passenger['original_class_id']) {
            $class_id = $passenger['class_id'];
            
            // Validate that the class ID exists
            $class_sql = "SELECT class_id FROM seat_class_t WHERE class_id = ?";
            $class_stmt = mysqli_prepare($connection, $class_sql);
            
            if (!$class_stmt) {
                throw new Exception("Error preparing class query: " . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($class_stmt, "s", $class_id);
            
            if (!mysqli_stmt_execute($class_stmt)) {
                throw new Exception("Error fetching class ID: " . mysqli_stmt_error($class_stmt));
            }
            
            $class_result = mysqli_stmt_get_result($class_stmt);
            if (!mysqli_fetch_assoc($class_result)) {
                throw new Exception("Invalid seat class ID: " . $class_id);
            }
            
            mysqli_stmt_close($class_stmt);
        }
        
        // Get meal ID from the meal type
        $meal_id = null;
        if ($passenger['meal_type'] != $passenger['original_meal']) {
            // Map meal types directly to meal IDs based on the image
            switch ($passenger['meal_type']) {
                case 'Multi-meal':
                    $meal_id = 'M001';
                    break;
                case 'Single meal':
                    $meal_id = 'M002';
                    break;
                case 'N/A':
                    $meal_id = 'M003';
                    break;
                default:
                    // If not a standard meal, try to get from database
                    $meal_sql = "SELECT meal_id FROM meal_option_t WHERE opt_name = ?";
                    $meal_stmt = mysqli_prepare($connection, $meal_sql);
                    
                    if (!$meal_stmt) {
                        throw new Exception("Error preparing meal query: " . mysqli_error($connection));
                    }
                    
                    mysqli_stmt_bind_param($meal_stmt, "s", $passenger['meal_type']);
                    
                    if (!mysqli_stmt_execute($meal_stmt)) {
                        throw new Exception("Error fetching meal ID: " . mysqli_stmt_error($meal_stmt));
                    }
                    
                    $meal_result = mysqli_stmt_get_result($meal_stmt);
                    $meal_row = mysqli_fetch_assoc($meal_result);
                    
                    if (!$meal_row) {
                        throw new Exception("Meal option not found: " . $passenger['meal_type']);
                    } else {
                        $meal_id = $meal_row['meal_id'];
                    }
                    
                    mysqli_stmt_close($meal_stmt);
            }
        }
        
        // Update passenger_service_t table with new class and/or meal
        $updates = [];
        $params = [];
        $types = "";
        
        if ($class_id !== null) {
            $updates[] = "class_id = ?";
            $params[] = $class_id;
            $types .= "s";
        }
        
        if ($meal_id !== null) {
            $updates[] = "meal_id = ?";
            $params[] = $meal_id;
            $types .= "s";  // Changed from 'i' to 's' since meal_id is a string like 'M001'
        }
        
        if (!empty($updates)) {
            $update_service_sql = "UPDATE passenger_service_t 
                                  SET " . implode(", ", $updates) . " 
                                  WHERE pass_id = ? AND f_book_id = ?";
            
            $service_stmt = mysqli_prepare($connection, $update_service_sql);
            if (!$service_stmt) {
                throw new Exception("Error preparing service update: " . mysqli_error($connection));
            }
            
            $params[] = $pass_id;
            $params[] = $booking_id;
            $types .= "is";
            
            mysqli_stmt_bind_param($service_stmt, $types, ...$params);
            
            if (!mysqli_stmt_execute($service_stmt)) {
                throw new Exception("Error updating passenger service: " . mysqli_stmt_error($service_stmt));
            }
            
            mysqli_stmt_close($service_stmt);
        }
    }
    
    // Commit the transaction
    mysqli_commit($connection);
    echo json_encode($response);
    
} catch (Exception $e) {
    // Roll back the transaction on error
    mysqli_rollback($connection);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 