<?php
session_start();
require_once 'connection.php'; // Ensure connection.php is included

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null; // Get user ID if logged in

// --- DEBUG LINE: Check Session User ID in PHP before passing to JS ---
error_log("DEBUG in payment.php: Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . ", userId variable: " . ($userId ?? 'NULL in PHP'));
// --- END DEBUG ---

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli - Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Nunito+Sans:wght@400;600;700&family=Montserrat:wght@500;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/payment.css">
</head>
<body>
    <header>        
        <?php include 'userHeader.php';?>
    </header>
    <main class="main-content">
        <div class="container">
            <div class="content-grid">
                <div class="left-column">
                    <h2 class="section-title">Payment method</h2>
                    
                    <p class="section-description">
                        Select a payment method below. Tripma processes your payment
                        securely with end-to-end encryption.
                    </p>

                    <div class="payment-methods-card">
                        <div class="payment-methods">
                            <div class="payment-method selected">
                                <div class="method-content">
                                    <img src="icon/card.svg" alt="Credit Card" class="method-icon">
                                    <div class="method-name">Debit/Credit Card</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="icon/google.svg" alt="Google Pay" class="method-icon">
                                    <div class="method-name">Google Pay</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="icon/apple.svg" alt="Apple Pay" class="method-icon">
                                    <div class="method-name">Apple Pay</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="icon/paypal.svg" alt="PayPal" class="method-icon">
                                    <div class="method-name">Paypal</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="icon/amazonpay.svg" alt="Amazon Pay" class="method-icon">
                                    <div class="method-name">Amazon Pay</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-details-header">
                        <h3 class="card-details-title">Card details</h3>
                        <div class="save-card-toggle">
                            <div class="switch">
                                <input type="checkbox" id="save-card" class="switch-input">
                                <label for="save-card" class="switch-label"></label>
                            </div>
                            <label for="save-card" class="save-card-text">save card info</label>
                        </div>
                    </div>

                    <div class="card-form">
                        <input type="text" class="form-input card-input" placeholder="Name">
                        <input type="text" class="form-input card-input" placeholder="Card Number">

                        <div class="form-row">
                            <div class="expiry-field">
                                <input type="text" class="form-input card-input" placeholder="Expiration Date">
                                <div class="field-hint">MM/YY</div>
                            </div>
                            <div class="cvv-field">
                                <div class="cvv-input-wrapper">
                                    <input type="text" class="form-input card-input" placeholder="CVV">
                                    <div class="cvv-icon"></div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class ="cancel-flight">
                        <h1>Cancellation Policy</h1>
                        <p>
                        This flight has a flexible cancellation policy. If you cancel or change your flight up to 24 hours before the departure date, you may be eligible for a refund or minimal fees, depending on your ticket type. Refundable ticket holders are entitled to a full or partial refund.
                        </p>
                        <p>
                        All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the airline and ticket type. For full details, please review the cancellation policy for this flight during the booking process.
                        </p> 
                        <div class="back-button-container">
                            <button class="back-button">Back to Seat Selection</button>
                        </div>
                    </div>
                </div>
                <div class="right-column">
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
                    <span data-tax-display>RM 121</span>
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
            </div>
        </div>
    </main>
    <script>
        window.currentUserId = <?php echo json_encode($userId); ?>;
    </script>
    <script src="js/payment.js"></script>
</body>
</html>