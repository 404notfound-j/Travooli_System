<?php
// getSeatAvailability.php
require_once 'connection.php'; // Include your database connection file

header('Content-Type: application/json'); // Set header to return JSON

// Get the flight_id and classId from the GET request
$flightId = $_GET['flightId'] ?? '';
$classId = $_GET['classId'] ?? '';

// --- DEBUG LOGS ---
error_log("getSeatAvailability.php called. flightId: '" . $flightId . "', classId: '" . $classId . "'");
// --- END DEBUG LOGS ---

if (empty($flightId)) {
    $errorMsg = ['error' => 'Flight ID is required.'];
    error_log("Error: " . $errorMsg['error']);
    echo json_encode($errorMsg);
    exit();
}

// Prepare the SQL query to get seat status for the given flight and class
$query = "SELECT seat_no, is_booked FROM flight_seats_t WHERE flight_id = ? AND class_id = ?";

$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $flightId, $classId);
    
    error_log("Prepared statement. Attempting to execute query: " . $query . " with params '" . $flightId . "', '" . $classId . "'");

    if (mysqli_stmt_execute($stmt)) { // Check if execution was successful
        $result = mysqli_stmt_get_result($stmt);

        $seats = [];
        if ($result) { // Check if get_result was successful
            $rowCount = mysqli_num_rows($result);
            error_log("Query executed. Rows found: " . $rowCount);

            while ($row = mysqli_fetch_assoc($result)) {
                $status = ($row['is_booked'] == 1) ? 'booked' : 'available';
                $seats[] = [
                    'seat_number' => $row['seat_no'],
                    'status' => $status
                ];
            }
            error_log("Rows processed: " . count($seats));
        } else {
            $errorMsg = ['error' => 'mysqli_stmt_get_result failed: ' . mysqli_error($connection)];
            error_log($errorMsg['error']);
            echo json_encode($errorMsg);
            exit();
        }

        mysqli_stmt_close($stmt);
        mysqli_close($connection);

        $jsonOutput = json_encode($seats);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON encoding error: " . json_last_error_msg() . " (Attempted to encode: " . print_r($seats, true) . ")");
            echo json_encode(['error' => 'JSON encoding failed: ' . json_last_error_msg()]);
        } else {
            error_log("JSON output successfully encoded: " . $jsonOutput);
            echo $jsonOutput; // Return the fetched seats as JSON
        }
    } else {
        $errorMsg = ['error' => 'Database query execution failed: ' . mysqli_stmt_error($stmt)];
        error_log($errorMsg['error']);
        echo json_encode($errorMsg);
    }
} else {
    $errorMsg = ['error' => 'Database query preparation failed: ' . mysqli_error($connection)];
    error_log($errorMsg['error']);
    echo json_encode($errorMsg);
}
?>