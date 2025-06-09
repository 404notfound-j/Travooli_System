<?php include 'userHeader.php'; ?>
<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBookInfo.css">
<?php
// Placeholder hotel data (reuse from hotelDetails.php for now)
$hotels = [
    1 => [
        'name' => 'CVK Park Bosphorus Hotel Istanbul',
        'location' => 'Gümüssuyu Mah. Inönü Cad. No:8, Istanbul 34437',
        'stars' => 5,
        'price' => 240,
        'images' => [
            'background/hotel1_main.jpg',
        ],
    ],
    2 => [
        'name' => 'Eresin Hotels Sultanahmet - Boutique Class',
        'location' => 'Istanbul, Turkey',
        'stars' => 5,
        'price' => 104,
        'images' => [
            'background/hotel2.jpg',
        ],
    ],
    3 => [
        'name' => 'Urban Stay Suites',
        'location' => 'Johor Bahru, Malaysia',
        'stars' => 4,
        'price' => 89,
        'images' => [
            'background/hotel3.jpg',
        ],
    ],
];
$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 1;
$hotel = $hotels[$hotel_id] ?? $hotels[1];
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$adult = $_GET['adult'] ?? 1;
$child = $_GET['child'] ?? 0;
$room = $_GET['room'] ?? 1;
?>
<div class="hotel-book-info-main">
    <div class="hotel-book-info-left-section">
        <h2 class="hotel-book-info-title">Who's Staying ?</h2>
        <div class="hotel-book-info-container">
            <div class="hotel-book-info-left">
                <div class="guest-info-box">
                    <h3 class="guest-info-heading">Guest Info</h3>
                    <ul class="guest-info-notes">
                        <li><b>Names must match ID</b> Please make sure that you enter the name exactly as it appears on the ID that will be used when checking in</li>
                        <li><b>ID validity requirements</b> To ensure your trip goes smoothly, please make sure that the passenger's travel document is valid on the date the trip ends</li>
                    </ul>
                    <form class="hotel-book-info-form" method="post" action="">
                        <div class="form-row form-row-2col">
                            <input type="text" id="firstname" name="firstname" placeholder="First name*" required>
                            <input type="text" id="lastname" name="lastname" placeholder="Last name*" required>
                        </div>
                        <div class="form-row">
                            <select id="nationality" name="nationality" required>
                                <option value="">Nationality</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="United States">United States</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                                <option value="India">India</option>
                                <option value="China">China</option>
                                <option value="Japan">Japan</option>
                                <option value="South Korea">South Korea</option>
                                <option value="France">France</option>
                                <option value="Germany">Germany</option>
                                <option value="Canada">Canada</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-row form-row-2col">
                            <input type="email" id="email" name="email" placeholder="Email*" required>
                            <input type="tel" id="phone" name="phone" placeholder="Phone number*" required>
                        </div>
                        <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                        <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                        <input type="hidden" name="adult" value="<?php echo htmlspecialchars($adult); ?>">
                        <input type="hidden" name="child" value="<?php echo htmlspecialchars($child); ?>">
                        <input type="hidden" name="room" value="<?php echo htmlspecialchars($room); ?>">
                    </form>
                </div>
            </div>
            <div class="hotel-book-info-right">
                <div class="price-details-box">
                    <h3 class="price-details-title">Price Details</h3>
                    <div class="price-details-row"><span><b>Tickets (<?php echo $adult; ?> Adults, <?php echo $room; ?> Room)</b></span></div>
                    <div class="price-details-row"><span>Subtotal</span><span>$340</span></div>
                    <div class="price-details-row"><span>Baggage Fees</span><span>$20</span></div>
                    <div class="price-details-row"><span>Multi-meal</span><span>$30</span></div>
                    <div class="price-details-row"><span>Taxes & Fees</span><span>$121</span></div>
                    <div class="price-details-row"><span>Discount</span><span>$0</span></div>
                    <div class="price-details-total"><span>Total</span><span class="price-details-total-amount">$491</span></div>
                </div>
                <div class="payment-button-container">
                    <button class="btn btn-primary hotel-book-info-submit">Proceed to Payment</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/hotelBookInfo.js"></script>
<?php include 'u_footer_2.php'; ?>
