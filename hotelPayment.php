<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Payment | Travooli</title>
    <link rel="stylesheet" href="css/hotelPayment.css">
</head>
<body>
<div class="container">
  <div class="payment-section">
    <h2>Payment method</h2>
    <p class="payment-desc">Select a payment method below. Tripma processes your payment securely with end-to-end encryption.</p>
    <div class="payment-methods">
      <label class="payment-method selected">
        <input type="radio" name="payment_method" value="card" checked>
        <img src="icon/card.svg" alt="Card"><br>
        Debit/Credit Card
      </label>
      <div class="payment-divider"></div>
      <label class="payment-method">
        <input type="radio" name="payment_method" value="google">
        <img src="icon/google.svg" alt="Google Pay"><br>
        Google Pay
      </label>
      <div class="payment-divider"></div>
      <label class="payment-method">
        <input type="radio" name="payment_method" value="apple">
        <img src="icon/apple.svg" alt="Apple Pay"><br>
        Apple Pay
      </label>
      <div class="payment-divider"></div>
      <label class="payment-method">
        <input type="radio" name="payment_method" value="paypal">
        <img src="icon/paypal.svg" alt="Paypal"><br>
        Paypal
      </label>
      <div class="payment-divider"></div>
      <label class="payment-method">
        <input type="radio" name="payment_method" value="amazon">
        <img src="icon/amazonpay.svg" alt="Amazon Pay"><br>
        Amazon Pay
      </label>
    </div>
    <div class="white-card credit-card-details-box">
      <h3>Credit card details</h3>
      <div class="save-card-toggle">
        <div class="toggle-switch active"><div class="slider"></div></div>
        <span>save card info</span>
        <input type="hidden" id="saveCardInput" value="1">
      </div>
      <form method="post" action="processPayment.php">
        <div class="form-group"><input type="text" id="cardName" name="cardName" placeholder="Name"></div>
        <div class="form-group"><input type="text" id="cardNumber" name="cardNumber" placeholder="Card Number"></div>
        <div class="flex-row">
          <input type="text" id="cardExp" name="cardExp" placeholder="Expiration Date">
          <input type="text" id="cardCVV" name="cardCVV" placeholder="CVV">
        </div>
      </form>
    </div>
    <div class="white-card cancellation-policy">
      <h4>Cancellation policy</h4>
      <p>This flight has a flexible cancellation policy. If you cancel or change your flight up to 24 hours before the departure date, you may be eligible for a refund or minimal fees, depending on your ticket type. Refundable ticket holders are entitled to a full or partial refund.<br><br>All bookings made through <a href="#">Travooli</a> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the airline and ticket type. For full details, please review the cancellation policy for this flight during the booking process.</p>
    </div>
  </div>
  <div class="right-column">
    <div class="price-details-box">
      <h3 class="price-details-title">Price Details</h3>
      <div class="price-details-row"><span><b>Tickets (2 Adults, 1 Room)</b></span></div>
      <div class="price-details-row"><span>Subtotal</span><span>$340</span></div>
      <div class="price-details-row"><span>Baggage Fees</span><span>$20</span></div>
      <div class="price-details-row"><span>Multi-meal</span><span>$30</span></div>
      <div class="price-details-row"><span>Taxes & Fees</span><span>$121</span></div>
      <div class="price-details-row"><span>Discount</span><span>$0</span></div>
      <div class="price-details-total"><span>Total</span><span class="price-details-total-amount">$491</span></div>
    </div>
    <div class="price-details-btn-row">
      <button class="confirm-btn" type="submit">Proceed to Payment</button>
    </div>
  </div>
</div>
<script src="js/hotelPayment.js"></script>
</body>
</html>
