<?php
include 'connection.php';

error_log(json_encode($_POST));


// Get POST data
$origin = $_POST['origin'] ?? null;
$destination = $_POST['destination'] ?? null;
$seatClass = $_POST['seatClass'] ?? null;
$timeFrom = ($_POST['timeFrom'] ?? '00:00:00') ; // ensures 'HH:MM:SS'
$timeTo = ($_POST['timeTo'] ?? '23:59:00');     // ensures end of minute
$sortBy = $_POST['sortBy'] ?? null;
$airlinesInput = $_POST['airlines'] ?? null;


// Start base query
$query = "
SELECT 
    f.flight_id, 
    f.airline_id, 
    f.orig_airport_id, 
    f.dest_airport_id,
    f.departure_time, 
    f.arrival_time,
    s.class_id, 
    s.price, 
    s.available_seats,
    ROUND(AVG(ff.rating), 1) AS avg_rating,
    COUNT(ff.f_feedback_id) AS review_count
FROM 
    flight_info_t f
JOIN 
    flight_seat_cls_t s ON f.flight_id = s.flight_id
LEFT JOIN 
    flight_feedback_t ff ON f.airline_id = ff.airline_id
WHERE 1=1
";

$params = [];
$types = "";

// Filter conditions
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
if ($seatClass) {
    $query .= " AND s.class_id = ?";
    $types .= "s";
    $params[] = $seatClass;
}
if (!empty($airlinesInput)) {
    $airlines = explode(',', $airlinesInput);
    $placeholders = implode(',', array_fill(0, count($airlines), '?'));
    $query .= " AND f.airline_id IN ($placeholders)";
    $types .= str_repeat('s', count($airlines));
    $params = array_merge($params, $airlines);
}
if (!empty($timeFrom) && !empty($timeTo)) {
    $query .= " AND TIME(f.departure_time) BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $timeFrom;
    $params[] = $timeTo;
}

// Group and sort
$query .= " GROUP BY f.flight_id, s.class_id ";

if ($sortBy === "time") {
    $query .= " ORDER BY f.departure_time ASC";
} elseif ($sortBy === "price") {
    $query .= " ORDER BY s.price ASC";
}

$stmt = mysqli_prepare($connection, $query);
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

header('Content-Type: application/json');
echo json_encode($flights);

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
