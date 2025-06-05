<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fight";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to database: ' . mysqli_connect_error()]);
    exit;
}
?>
