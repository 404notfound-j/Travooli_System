<?php
include 'connection.php';
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

        <h3 class="subtitle">
            <span id="class-name">Class</span>
        </h3>
        
        <div id="seat-selection-status">
                    <p><strong><span id="seat-count">Loading...</span></strong></p>
                    <p>Selected Seats: <span id="seat-list">-</span></p>
        </div>


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
                                echo "<div class='seat available' id='$id' data-seat-number='$id'></div>";
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
                    <span id="baggage-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span>Meal Add-on</span>
                    <span id="meal-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span>Taxes & Fees</span>
                    <span id="tax-amount">RM 0</span>
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
            <button class="proceed-btn" id="proceed-btn" disabled>Proceed to Payment</button>
            </div>

    <script src="js/seat_selection.js"></script>
</body>
</html>
