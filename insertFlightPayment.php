<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';

function generateUniquePassengerId($connection) {
    do {
        $digits = '0123456789';
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomId = 'P' .
                    $digits[mt_rand(0, 9)] .
                    $letters[mt_rand(0, 25)] .
                    $digits[mt_rand(0, 9)] .
                    $letters[mt_rand(0, 25)];

        $check = $connection->prepare("SELECT pass_id FROM flight_seats_t WHERE pass_id = ?");
        $check->bind_param("s", $randomId);
        $check->execute();
        $result = $check->get_result();
    } while ($result->num_rows > 0);

    return $randomId;
}

function insertFlightPayment(mysqli $connection, string $f_book_id, float $amount, string $payment_method, string $payment_status): ?string {
    do {
        $randomDigits = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $paymentId = 'PAY' . $randomDigits;

        // Ensure payment ID is unique
        $checkQuery = "SELECT 1 FROM flight_payment_t WHERE f_payment_id = ?";
        $stmtCheck = $connection->prepare($checkQuery);
        $stmtCheck->bind_param("s", $paymentId);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        $exists = $stmtCheck->num_rows > 0;
        $stmtCheck->close();
    } while ($exists);

    // Prepare insert query
    $insertQuery = "INSERT INTO flight_payment_t (f_payment_id, f_book_id, payment_date, amount, payment_method, payment_status)
                    VALUES (?, ?, NOW(), ?, ?, ?)";
    $stmtInsert = $connection->prepare($insertQuery);
    if (!$stmtInsert) {
        error_log("Failed to prepare insertFlightPayment: " . $connection->error);
        return null;
    }

    $stmtInsert->bind_param("ssdss", $paymentId, $f_book_id, $amount, $payment_method, $payment_status);

    if ($stmtInsert->execute()) {
        $stmtInsert->close();
        return $paymentId; // Return the new ID
    } else {
        error_log("Failed to insert payment: " . $stmtInsert->error);
        $stmtInsert->close();
        return null;
    }
}

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !$data || !isset(
            $data['user_id'], $data['flight_id'], $data['booking_date'], $data['status'],
            $data['class_id'], $data['selected_seats'], $data['total_amount'],
            $data['passengers'], $data['ticket'], $data['baggage'],
            $data['meal'], $data['num_passenger']
        )
    ) {
        throw new Exception("Missing required booking data.");
    }

    $f_book_id = 'BK' . time();
    $booking_info_id = 'BI' . time();

    $connection->begin_transaction();

    // ➤ Insert into flight_booking_t
    $stmt1 = $connection->prepare("INSERT INTO flight_booking_t (f_book_id, user_id, flight_id, book_date, status)
                                   VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssss", $f_book_id, $data['user_id'], $data['flight_id'], $data['booking_date'], $data['status']);
    $stmt1->execute();

    // ➤ Insert into flight_booking_info_t
    $stmt2 = $connection->prepare("INSERT INTO flight_booking_info_t 
    (booking_info_id, f_book_id, passenger_count, baggage_total, meal_total, base_fare_total, total_amount, flight_date, trip_type)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Add `flight_date` and `trip_type` (string, string)
    $stmt2->bind_param("ssiddddss",
        $booking_info_id,
        $f_book_id,
        $data['num_passenger'],
        $data['baggage'],
        $data['meal'],
        $data['ticket'],
        $data['total_amount'],
        $data['flight_date'],
        $data['trip_type']
    );

$stmt2->execute();

    // ➤ Insert each passenger
    foreach ($data['passengers'] as $index => $pax) {
        $selectedSeat = $data['selected_seats'][$index] ?? null;

        if (!$selectedSeat) {
            throw new Exception("❌ Missing selected seat for passenger index $index");
        }

        $pass_id = generateUniquePassengerId($connection);

        // ➤ Insert into passenger_t
        $stmt3 = $connection->prepare("INSERT INTO passenger_t (pass_id, fst_name, lst_name, gender, dob, country, pass_category)
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->bind_param("sssssss", $pass_id, $pax['first_name'], $pax['last_name'], $pax['gender'], $pax['dob'], $pax['country'], $pax['type']);
        $stmt3->execute();

        // ➤ Safe baggage and meal fallback
        $validBaggageIds = ['BG01', 'BG02', 'BG03'];
        $baggage_id = in_array($pax['baggage_id'], $validBaggageIds) ? $pax['baggage_id'] : 'BG01';
        $meal_id = $pax['meal_id'] ?? 'M01';

        // ➤ Insert into passenger_service_t
        $stmt4 = $connection->prepare("INSERT INTO passenger_service_t (pass_id, f_book_id, class_id, baggage_id, meal_id)
                                       VALUES (?, ?, ?, ?, ?)");
        $stmt4->bind_param("sssss", $pass_id, $f_book_id, $data['class_id'], $baggage_id, $meal_id);
        $stmt4->execute();

        $class_id = $data['class_id'];
        error_log("➡️ Assigning seat $selectedSeat on flight {$data['flight_id']} for class {$class_id} to passenger $pass_id");
        $stmtSeatUpdate = $connection->prepare("UPDATE flight_seats_t 
                                                SET is_booked = 1, pass_id = ? 
                                                WHERE flight_id = ? AND seat_no = ? AND class_id = ?");
        $stmtSeatUpdate->bind_param("ssss", $pass_id, $data['flight_id'], $selectedSeat, $class_id);
        $stmtSeatUpdate->execute();
        
        if ($stmtSeatUpdate->affected_rows === 0) {
            throw new Exception("Failed to assign seat: $selectedSeat (possibly not matching class or already booked)");
        }
        $stmtSeatUpdate->close();
        
    }

    // ➤ Insert flight payment
    $payment_method = $data['payment_method'] ?? 'unknown';
    $payment_status = 'paid';
    $payment_id = insertFlightPayment($connection, $f_book_id, $data['total_amount'], $payment_method, $payment_status);

    if (!$payment_id) {
        throw new Exception("Failed to insert payment record.");
    }

        // ➤ Reduce available seats in flight_seat_cls_t
    $seatReduction = $data['num_passenger'];
    $stmtReduce = $connection->prepare("UPDATE flight_seat_cls_t 
                                        SET available_seats = available_seats - ? 
                                        WHERE flight_id = ? AND class_id = ?");
    $stmtReduce->bind_param("iss", $seatReduction, $data['flight_id'], $data['class_id']);
    $stmtReduce->execute();

    if ($stmtReduce->affected_rows === 0) {
        throw new Exception("Seat reduction failed — check class_id or availability.");
    }


    $connection->commit();
    
    // Store booking ID in session for payment_complete.php (similar to hotel system)
    $_SESSION['last_flight_booking_id'] = $f_book_id;
    
    echo json_encode(['success' => true, 'bookingId' => $f_book_id, 'paymentId' => $payment_id]);

} catch (Exception $e) {
    if (isset($connection)) {
        $connection->rollback();
    }
    error_log("❌ Booking Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Booking failed: ' . $e->getMessage()]);
}
?>