<?php
include 'db_connection.php';

$class_id = $_GET['class_id'] ?? '';

if ($class_id) {
    $stmt = $conn->prepare("SELECT price FROM flight_seat_cls_t WHERE class_id = ? LIMIT 1");
    $stmt->bind_param("s", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["price" => (float)$row['price']]);
    } else {
        echo json_encode(["price" => 0]);
    }
} else {
    echo json_encode(["price" => 0]);
}
?>
