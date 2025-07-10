<?php
include 'connection.php';

// Enable error reporting and display errors in logs for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Do not display errors to browser

error_log("DEBUG: getflight.php received POST data: " . json_encode($_POST));

$origin = $_POST['origin'] ?? null;
$destination = $_POST['destination'] ?? null;
// $date = $_POST['date'] ?? null; // Date filtering is removed as per your request
$seatClass = $_POST['seatClass'] ?? null;
$timeFrom = ($_POST['timeFrom'] ?? '00:00:00');
$timeTo = ($_POST['timeTo'] ?? '23:59:00');
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

// Date filter is REMOVED as per your request.
// if ($date) {
//     $query .= " AND DATE(f.departure_time) = ?";
//     $types .= "s";
//     $params[] = $date;
// }

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

// --- NEW DEBUG LOGS HERE ---
error_log("DEBUG: Final SQL Query to prepare: " . $query);
error_log("DEBUG: Parameters to bind: " . json_encode($params));
error_log("DEBUG: Parameter types: " . $types);
// --- END NEW DEBUG LOGS ---

$stmt = mysqli_prepare($connection, $query);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare SQL statement: ' . mysqli_error($connection)]); 
    exit;
}

if (!empty($params)) {
    // Check if $types string length matches $params array length
    if (strlen($types) !== count($params)) {
        http_response_code(500);
        echo json_encode(['error' => 'Bind param mismatch: types string length (' . strlen($types) . ') != params count (' . count($params) . ')']);
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
} else {
    error_log("DEBUG: No parameters to bind for the query.");
}

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to execute SQL statement: ' . mysqli_stmt_error($stmt)]);
    mysqli_stmt_close($stmt);
    exit;
}

$result = mysqli_stmt_get_result($stmt);

$flights = [];
// --- NEW DEBUG LOG ---
if ($result) {
    $rowCount = mysqli_num_rows($result);
    error_log("DEBUG: SQL result row count: " . $rowCount);
    while ($row = mysqli_fetch_assoc($result)) {
        $flights[] = $row;
    }
    error_log("DEBUG: Fetched flights array count: " . count($flights));
} else {
    error_log("ERROR: mysqli_stmt_get_result returned false: " . mysqli_error($connection));
}
// --- END NEW DEBUG LOG ---

header('Content-Type: application/json');
echo json_encode($flights);

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>