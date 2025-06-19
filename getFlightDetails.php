<?php
header('Content-Type: application/json');
include 'connection.php';

$flightId = isset($_GET['flightId']) ? $_GET['flightId'] : null;

if (!$flightId) {
    echo json_encode(['error' => 'No flight ID provided']);
    exit;
}

$query = "SELECT f.flight_id, f.airline_id, a.airline_name, 
                 f.orig_airport_id, f.dest_airport_id, f.departure_time, f.arrival_time, f.date, s.class_id, 
                 s.price, s.available_seats, oa.airport_full AS origin_airport_full, oa.city_full AS origin_airport_address, da.airport_full AS dest_airport_full,
                 da.city_full AS dest_airport_address
            FROM flight_info_t f
            JOIN flight_seat_cls_t s ON f.flight_id = s.flight_id
            JOIN airport_t oa ON f.orig_airport_id = oa.airport_short
            JOIN airport_t da ON f.dest_airport_id = da.airport_short
            JOIN airline_t a ON f.airline_id = a.airline_id
            WHERE f.flight_id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $flightId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$flight = mysqli_fetch_assoc($result);

if (!$flight) {
    echo json_encode(['error' => 'Flight not found']);
} else {
    echo json_encode($flight);
}

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
