<?php
// Start the session
session_start();

// Database connection
include 'connection.php';

// Set response header to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid request data'
];

// Check if we have valid data
if (isset($data['booking_id']) && !empty($data['booking_id'])) {
    $bookingId = $data['booking_id'];
    $passengers = $data['passengers'] ?? [];
    $newAmount = isset($data['new_amount']) ? floatval($data['new_amount']) : 0;
    
    // Start transaction
    mysqli_begin_transaction($connection);
    
    try {
        // Update passenger details
        foreach ($passengers as $index => $passenger) {
            // Extract passenger name
            $nameParts = explode(' ', $passenger['name'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            // Get passenger ID for this booking and index
            $sql = "SELECT p.pass_id 
                    FROM passenger_t p 
                    JOIN passenger_service_t ps ON p.pass_id = ps.pass_id 
                    WHERE ps.f_book_id = ? 
                    LIMIT ?, 1";
            
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "si", $bookingId, $index);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                $passId = $row['pass_id'];
                
                // Update passenger name and category
                if (!empty($firstName)) {
                    // Get the age group (Adult/Child)
                    $passCategory = !empty($passenger['age_group']) ? $passenger['age_group'] : 'Adult';
                    
                    $updateSql = "UPDATE passenger_t SET fst_name = ?, lst_name = ?, pass_category = ? WHERE pass_id = ?";
                    $updateStmt = mysqli_prepare($connection, $updateSql);
                    mysqli_stmt_bind_param($updateStmt, "ssss", $firstName, $lastName, $passCategory, $passId);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                }
                
                // Check if class or meal has changed
                $classChanged = $passenger['class'] !== $passenger['original_class'];
                $mealChanged = $passenger['meal_type'] !== $passenger['original_meal'];
                
                // Update passenger service (class and meal)
                $updateServiceSql = "UPDATE passenger_service_t ps 
                                     LEFT JOIN seat_class_t sc ON sc.class_name = ? 
                                     LEFT JOIN meal_option_t mo ON mo.opt_name = ? 
                                     SET ps.class_id = sc.class_id, ps.meal_id = mo.meal_id 
                                     WHERE ps.pass_id = ? AND ps.f_book_id = ?";
                
                $updateServiceStmt = mysqli_prepare($connection, $updateServiceSql);
                mysqli_stmt_bind_param($updateServiceStmt, "ssss", $passenger['class'], $passenger['meal_type'], $passId, $bookingId);
                mysqli_stmt_execute($updateServiceStmt);
                mysqli_stmt_close($updateServiceStmt);
            }
            
            mysqli_stmt_close($stmt);
        }
        
        // Update payment amount if it changed
        if ($newAmount > 0) {
            // Check if payment record exists
            $checkPaymentSql = "SELECT * FROM flight_payment_t WHERE f_book_id = ?";
            $checkPaymentStmt = mysqli_prepare($connection, $checkPaymentSql);
            mysqli_stmt_bind_param($checkPaymentStmt, "s", $bookingId);
            mysqli_stmt_execute($checkPaymentStmt);
            $paymentResult = mysqli_stmt_get_result($checkPaymentStmt);
            
            if (mysqli_num_rows($paymentResult) > 0) {
                // Update existing payment record
                $updatePaymentSql = "UPDATE flight_payment_t SET amount = ? WHERE f_book_id = ?";
                $updatePaymentStmt = mysqli_prepare($connection, $updatePaymentSql);
                mysqli_stmt_bind_param($updatePaymentStmt, "ds", $newAmount, $bookingId);
                mysqli_stmt_execute($updatePaymentStmt);
                mysqli_stmt_close($updatePaymentStmt);
            } else {
                // Create new payment record
                $paymentId = 'PY' . time();
                $paymentMethod = 'Card'; // Default payment method
                $paymentStatus = 'Paid'; // Default status
                
                $insertPaymentSql = "INSERT INTO flight_payment_t (payment_id, f_book_id, amount, payment_method, payment_status) 
                                     VALUES (?, ?, ?, ?, ?)";
                $insertPaymentStmt = mysqli_prepare($connection, $insertPaymentSql);
                mysqli_stmt_bind_param($insertPaymentStmt, "ssdss", $paymentId, $bookingId, $newAmount, $paymentMethod, $paymentStatus);
                mysqli_stmt_execute($insertPaymentStmt);
                mysqli_stmt_close($insertPaymentStmt);
            }
            
            mysqli_stmt_close($checkPaymentStmt);
        }
        
        // Commit transaction
        mysqli_commit($connection);
        
        $response = [
            'success' => true,
            'message' => 'Flight booking updated successfully'
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connection);
        
        $response = [
            'success' => false,
            'message' => 'Error updating flight booking: ' . $e->getMessage()
        ];
    }
}

// Return response
echo json_encode($response);
?> 