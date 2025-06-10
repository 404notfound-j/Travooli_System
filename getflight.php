<?php
include 'connection.php';

$origin = $_POST['origin'] ?? null;
$destination = $_POST['destination'] ?? null;
$seatClass = $_POST['seatClass'] ?? null;  // EC, PE, BC, FC
$date = $_POST['date'] ?? null;
$timeFrom = $_POST['timeFrom'] ?? null;    // e.g., "08:00:00"
$timeTo = $_POST['timeTo'] ?? null;        // e.g., "12:00:00"

$query = "SELECT f.flight_id, f.airline_id, f.orig_airport_id, f.dest_airport_id, 
                 f.departure_time, f.arrival_time, f.date,
                 s.class_id, s.price, s.available_seats
          FROM flightinfo f
          JOIN flight_seats s ON f.flight_id = s.flight_id
          WHERE 1=1";

$params = [];
$types = "";

if ($origin) {
    $query .= " AND f.orig_airport_id = ?";
    $types .= "s";
    $params[] = $origin;
}

if ($destination) {
    $query .= " AND f.dest_airport_id = ?";
    $types .= "s";
    $params[] = $destination;
}

if ($date) {
    $query .= " AND f.date = ?";
    $types .= "s";
    $params[] = $date;
}

if ($seatClass) {
    $query .= " AND s.class_id = ?";
    $types .= "s";
    $params[] = $seatClass;
}

if ($timeFrom && $timeTo) {
    $query .= " AND f.departure_time BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $timeFrom;
    $params[] = $timeTo;
}

$stmt = mysqli_prepare($conn, $query);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit;
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$flights = [];
while ($row = mysqli_fetch_assoc($result)) {
    $flights[] = $row;
}

echo json_encode($flights);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
