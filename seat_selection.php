<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Seat Selection</title>
  <link rel="stylesheet" href="css/seat_selection.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet">
</head>
<body>
  <header>
    <?php include 'userHeader.php';?>
  </header>
  <main class="container">
    <h2 class="title">Seat Selection</h2>
    <p class="description">
        We kindly invite you to select your preferred seat to ensure a comfortable and seamless journey. 
        Please proceed to the seat selection option during your booking process
    </p>

    <h3 class="subtitle">Business class</h3>

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
                <!-- Row 1 -->
                <div class="seat available"></div>
                <div class="seat unavailable"></div>
                <div class="seat available"></div>
                <div class="row-number">1</div>
                <div class="seat available"></div>
                <div class="seat available"></div>
                <div class="seat available"></div>

                <!-- Row 2 -->
                <div class="seat available"></div>
                <div class="seat available"></div>
                <div class="seat available"></div>
                <div class="row-number">2</div>
                <div class="seat available"></div>
                <div class="seat unavailable"></div>
                <div class="seat available"></div>

                <!-- Row 3 -->
                <div class="seat unavailable"></div>
                <div class="seat available"></div>
                <div id="seat_selected" class="seat selected"></div>
                <div class="row-number">3</div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat available"></div>

                <!-- Row 4 -->
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="row-number">4</div>
                <div class="seat available"></div>
                <div class="seat unavailable"></div>
                <div class="seat available"></div>

                <!-- Row 5 -->
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="row-number">5</div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>

                <!-- Row 6 -->
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="row-number">6</div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>

                <!-- Row 7 -->
                <div class="seat unavailable"></div>
                <div class="seat available"></div>
                <div class="seat available"></div>
                <div class="row-number">7</div>
                <div class="seat unavailable"></div>
                <div class="seat available"></div>
                <div class="seat available"></div>

                <!-- Row 8 -->
                <div class="seat available"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
                <div class="row-number">8</div>
                <div class="seat available"></div>
                <div class="seat unavailable"></div>
                <div class="seat unavailable"></div>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="selected-icon"></div>
                    <span>SELECTED</span>
                </div>
                <div class="legend-item">
                    <div class="available-icon"></div>
                    <span>AVAILABLE</span>
                </div>
                <div class="legend-item">
                    <div class="unavailable-icon"></div>
                    <span>NOT AVAILABLE</span>
                </div>
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
        </div>
    </main>
    <script src="js/seat_selection.js"></script> 
</body>
</html>
