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
                <!-- Left Column -->
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
                                    <img src="/duotone-creditcard.svg" alt="Credit Card" class="method-icon">
                                    <div class="method-name">Debit/Credit Card</div>
                                </div>
                                <div class="radio-button selected"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="/18---google.svg" alt="Google Pay" class="method-icon">
                                    <div class="method-name">Google Pay</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="/18---apple-mac.svg" alt="Apple Pay" class="method-icon">
                                    <div class="method-name">Apple Pay</div>
                                </div>
                                <div class="radio-button"></div>
                            </div>
                            
                            <div class="method-divider"></div>
                            
                            <div class="payment-method">
                                <div class="method-content">
                                    <img src="/image-9.png" alt="PayPal" class="method-icon">
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

                    <!-- Credit Card Details -->
                    <div class="card-details-header">
                        <h3 class="card-details-title">Credit card details</h3>
                        <div class="save-card-toggle">
                            <div class="switch">
                                <input type="checkbox" id="save-card" class="switch-input">
                                <label for="save-card" class="switch-label"></label>
                            </div>
                            <label for="save-card" class="save-card-text">save card info</label>
                        </div>
                    </div>

                    <div class="card-form">
                        <input type="text" class="form-input" placeholder="Name">
                        <input type="text" class="form-input" placeholder="Card Number">
                        
                        <div class="form-row">
                            <div class="expiry-field">
                                <input type="text" class="form-input" placeholder="Expiration Date">
                                <div class="field-hint">MM/YY</div>
                            </div>
                            <div class="cvv-field">
                                <div class="cvv-input-wrapper">
                                    <input type="text" class="form-input" placeholder="CVV">
                                    <div class="cvv-icon">
                                        <div class="info-circle">
                                            <img src="/union.svg" alt="Info" class="info-icon">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <h4 class="policy-title">Cancellation policy</h4>
                    
                    <div class="policy-content">
                        <p class="policy-text">
                            This flight has a flexible cancellation policy. If you cancel or
                            change your flight up to 24 hours before the departure date, you
                            may be eligible for a refund or minimal fees, depending on your
                            ticket type. Refundable ticket holders are entitled to a full or
                            partial refund.
                        </p>
                        <p class="policy-text">
                            All bookings made through 
                            <span class="highlight">Travel Safe International</span> 
                            are backed by our satisfaction guarantee. However, cancellation
                            policies may vary based on the airline and ticket type. For full
                            details, please review the cancellation policy for this flight
                            during the booking process.
                        </p>
                    </div>

                    <div class="back-button-container">
                        <button class="back-button">Back to seat select</button>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    <div class="price-card">
                        <h2>Price Details</h2>
                        <p>Tickets (2 Adults, 1 Child)</p>
                        
                        <div class="price-item">
                            <span>Subtotal</span>
                            <span>$340</span>
                        </div>
                        <div class="price-item">
                            <span>Baggage Fees</span>
                            <span>$20</span>
                        </div>
                        <div class="price-item">
                            <span>Multi-meal</span>
                            <span>$30</span>
                        </div>
                        <div class="price-item">
                            <span>Taxes & Fees</span>
                            <span>$121</span>
                        </div>
                        <div class="price-item">
                            <span>Discount</span>
                            <span>$0</span>
                        </div>
                        <hr>
                        <div class="total">
                            <span>Total</span>
                            <span>$491</span>
                        </div>
                    </div>
                    <button class="proceed-btn">Proceed to Payment</button>
                </div>
            </div>
        </div>
    </main>
    <script src="js/payment.js"></script>
</body>
</html>