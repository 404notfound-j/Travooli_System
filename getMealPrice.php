<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if meal_type parameter is provided
if (!isset($_GET['meal_type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing meal_type parameter']);
    exit;
}

$meal_type = $_GET['meal_type'];

// Prepare response
$response = ['success' => false, 'price' => 0];

try {
    // Query to get the meal price for the given meal type
    $sql = "SELECT mo.meal_id, mo.price 
            FROM meal_option_t mo 
            WHERE mo.opt_name = ?";
    
    $stmt = mysqli_prepare($connection, $sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $meal_type);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error executing query: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $response['success'] = true;
        $response['price'] = $row['price'];
        $response['meal_id'] = $row['meal_id'];
    } else {
        // If meal type not found, return default prices
        switch ($meal_type) {
            case 'Multi-meal':
                $response['price'] = 50;
                break;
            case 'Single meal':
                $response['price'] = 20;
                break;
            case 'N/A':
            default:
                $response['price'] = 0;
                break;
        }
        $response['success'] = true;
        $response['note'] = 'Using default price for meal type';
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Return response
echo json_encode($response); 