<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if class_id and flight_id parameters are provided
if (!isset($_GET['class_id']) || !isset($_GET['flight_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$class_id = $_GET['class_id'];
$flight_id = $_GET['flight_id'];

// Prepare response
$response = ['success' => false, 'price' => 0];

try {
    // Query to get the class price for the given flight and class ID
    $sql = "SELECT price FROM flight_seat_cls_t 
            WHERE class_id = ? AND flight_id = ?";
    
    $stmt = mysqli_prepare($connection, $sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $class_id, $flight_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing query: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $response['success'] = true;
        $response['price'] = $row['price'];
    } else {
        // If no price found for this class and flight, try to get a default price for this class
        $default_sql = "SELECT AVG(price) as avg_price FROM flight_seat_cls_t WHERE class_id = ?";
        $default_stmt = mysqli_prepare($connection, $default_sql);
        
        if ($default_stmt) {
            mysqli_stmt_bind_param($default_stmt, "s", $class_id);
            mysqli_stmt_execute($default_stmt);
            $default_result = mysqli_stmt_get_result($default_stmt);
            
            if ($default_row = mysqli_fetch_assoc($default_result)) {
                $response['success'] = true;
                $response['price'] = $default_row['avg_price'] ?: 0;
                $response['note'] = 'Using average price for this class';
            }
            
            mysqli_stmt_close($default_stmt);
        }
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return response
echo json_encode($response); 