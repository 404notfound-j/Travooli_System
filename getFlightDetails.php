<?php
header('Content-Type: application/json');
include 'connection.php';

// Get flightId from URL, sanitize input
$flightId = isset($_GET['flightId']) ? $_GET['flightId'] : '';

if (empty($flightId)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid flight ID"]);
    exit();
}

$stmt = $connenction->prepare("
    SELECT f.flight_id, f.airline_id, 
           f.departure_time, f.arrival_time,
           f.orig_airport_id, f.dest_airport_id, `date`
    FROM flightinfo f
    LEFT JOIN airport orig ON f.orig_airport_id = orig.airport_id
    LEFT JOIN airport dest ON f.dest_airport_id = dest.airport_id
    WHERE f.flight_id = ?
");

// Bind as string (not integer)
$stmt->bind_param("s", $flightId);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Flight not found"]);
    exit();
}

$flight = $result->fetch_assoc();
$stmt->close();

// Fetch origin airport details
$origAirportId = $flight['orig_airport_id'];
$stmt = $conn->prepare("SELECT airport_full, city_full FROM airport WHERE airport_id = ?");
$stmt->bind_param("s", $origAirportId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $originAirport = $result->fetch_assoc();
    $flight['origin_airport_full'] = $originAirport['airport_full'];
    $flight['origin_airport_address'] = $originAirport['city_full'];
} else {
     $flight['origin_airport_full'] = "Unknown Origin Airport";
     $flight['origin_airport_address'] = "";
}

$stmt->close();
$connenction->close();

// Output combined result as JSON
echo json_encode($flight);
