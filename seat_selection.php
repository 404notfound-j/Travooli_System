<?php
// seat_selection.php
session_start();
include 'connection.php';

$flight_id = $_GET['flight_id'] ?? '';
$booking_id = $_GET['booking_id'] ?? '';
$class_id = $_GET['class_id'] ?? '';
$user_id = $_SESSION['user_id'] ?? 'U0001'; // temporary fallback for testing

// Handle fetch of occupied seats
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $stmt = $conn->prepare("SELECT seat_no FROM flight_seats_t WHERE flight_id = ? AND is_booked = 1");
    $stmt->bind_param("s", $flight_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $occupiedSeats = [];
    while ($row = $result->fetch_assoc()) {
        $occupiedSeats[] = $row['seat_no'];
    }

    echo json_encode($occupiedSeats);
    exit;
}

// Handle saving selected seats and booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'save') {
    $seats = explode(',', $_POST['seats']);
    $total_price = floatval($_POST['total_price'] ?? 0);
    $class_id = $_POST['class_id'] ?? '';

    // Insert into FLIGHT_BOOKING_T
    $stmt = $conn->prepare("INSERT IGNORE INTO FLIGHT_BOOKING_T (flight_booking_id, user_id, flight_id, booking_date, status) VALUES (?, ?, ?, NOW(), 'Pending')");
    $stmt->bind_param("sss", $booking_id, $user_id, $flight_id);
    $stmt->execute();

    // Insert selected seats
    foreach ($seats as $seat) {
        $seat = trim($seat);
        $stmt = $conn->prepare("INSERT INTO SEAT_BOOKING_T (booking_id, flight_id, seat_number) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $booking_id, $flight_id, $seat);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success']);
    exit;
}

// Get class name(s)
$flight_classes = [];
if (!empty($flight_id)) {
    $stmt = $conn->prepare("SELECT sct.class_name FROM flight_seat_cls_t fsc JOIN seat_class_t sct ON fsc.class_id = sct.class_id WHERE fsc.flight_id = ?");
    $stmt->bind_param("s", $flight_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $flight_classes[] = $row['class_name'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seat Selection</title>
    <link rel="stylesheet" href="css/seat_selection.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>

    <main>
    <div class="container">
        <h2 class="title">Seat Selection</h2>
        <p class="description">
            We kindly invite you to select your preferred seat to ensure a comfortable and seamless journey. 
            Please proceed to the seat selection option during your booking process
        </p>

        <h3 class="subtitle"><?php echo implode(', ', array_map('htmlspecialchars', $flight_classes)); ?> </h3>

        <div class="layout">
            <div class="seat-map">
                <div class="seat-columns">
                    <span>A</span>
                    <span>B</span>
                    <span>C</span>
                    <span> </span>
                    <span>D</span>
                    <span>E</span>
                    <span>F</span>
                </div>

                <div class="seat-grid">
                    <?php
                    $rows = 8;
                    $cols = ['A', 'B', 'C', '', 'D', 'E', 'F'];
                    for ($r = 1; $r <= $rows; $r++) {
                        foreach ($cols as $c) {
                            if ($c === '') {
                                echo "<div class='row-number'>$r</div>";
                            } else {
                                $id = $c . $r;
                                echo "<div class='seat available' id='$id'></div>";
                            }
                        }
                    }
                    ?>
                </div>

                <div class="legend">
                    <div class="legend-item"><div class="available-icon"></div>Available</div>
                    <div class="legend-item"><div class="selected-icon"></div>Selected</div>
                    <div class="legend-item"><div class="unavailable-icon"></div>Unavailable</div>
                </div>
            </div>

            <div class="price-details">
            <div class="price-card">
                <h2>Price Details</h2>
                <p id="ticket-count-label">Tickets</p>               
                <div class="price-item">
                    <span>Subtotal</span>
                    <span id="flight-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span>Baggage Fees</span>
                    <span class="baggage-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span>Multi-meal</span>
                    <span class="meal-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span>Taxes & Fees</span>
                    <span>RM 121</span>
                </div>
                <div class="price-item">
                    <span>Discount</span>
                    <span>RM 0</span>
                </div>
                <hr>
                <div class="total">
                    <span>Total</span>
                    <span id="total">RM 0</span>
                </div>
            </div>
            <a href="payment.php" class="proceed-btn">Proceed to Payment</a>

            <button class="proceed-btn">Proceed to Payment</button>
        </div>


    <script>
        const flightId = "<?php echo $flight_id; ?>";
        const bookingId = "<?php echo $booking_id; ?>";
    </script>
    <script src="js/seat_selection.js"></script>
</body>
</html>
