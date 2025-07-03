<?php
// Start session to check login status
session_start();
include 'connection.php';

// Get parameters from URL
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$hotel_id = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : '';
$r_type_id = isset($_GET['r_type_id']) ? $_GET['r_type_id'] : '';
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : (isset($_SESSION['checkin_date']) ? $_SESSION['checkin_date'] : date('Y-m-d'));
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : (isset($_SESSION['checkout_date']) ? $_SESSION['checkout_date'] : date('Y-m-d', strtotime('+1 day')));
$room_count = isset($_GET['room']) ? (int)$_GET['room'] : 1;
$adult = isset($_GET['adult']) ? (int)$_GET['adult'] : 1;
$child = isset($_GET['child']) ? (int)$_GET['child'] : 0;
$firstname = isset($_GET['firstname']) ? $_GET['firstname'] : '';
$lastname = isset($_GET['lastname']) ? $_GET['lastname'] : '';
$nationality = isset($_GET['nationality']) ? $_GET['nationality'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$phone = isset($_GET['phone']) ? $_GET['phone'] : '';
$room_price = isset($_GET['room_price']) ? $_GET['room_price'] : '';
$tax = isset($_GET['tax']) ? $_GET['tax'] : '';
$total = isset($_GET['total']) ? $_GET['total'] : '';

// Process payment form submission - MOVED BEFORE ANY OUTPUT
$payment_success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Output all POST and GET data
    error_log("POST DATA: " . print_r($_POST, true));
    error_log("GET DATA: " . print_r($_GET, true));
    
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    
    // Always get booking info from GET (URL) to ensure latest user selection
    $customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
    $hotel_id = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : '';
    $r_type_id = isset($_GET['r_type_id']) ? $_GET['r_type_id'] : '';
    $checkin = isset($_GET['checkin']) ? $_GET['checkin'] : (isset($_SESSION['checkin_date']) ? $_SESSION['checkin_date'] : date('Y-m-d'));
    $checkout = isset($_GET['checkout']) ? $_GET['checkout'] : (isset($_SESSION['checkout_date']) ? $_SESSION['checkout_date'] : date('Y-m-d', strtotime('+1 day')));
    $room_count = isset($_GET['room']) ? (int)$_GET['room'] : 1;
    $adult = isset($_GET['adult']) ? (int)$_GET['adult'] : 1;
    $child = isset($_GET['child']) ? (int)$_GET['child'] : 0;
    $room_price = isset($_GET['room_price']) ? $_GET['room_price'] : '';
    $tax = isset($_GET['tax']) ? $_GET['tax'] : '';
    $total = isset($_GET['total']) ? $_GET['total'] : '';
    
    if (empty($payment_method)) {
        $error_message = "Please select a payment method.";
    } else {
        try {
            // Start transaction
            mysqli_begin_transaction($connection, MYSQLI_TRANS_START_READ_WRITE);
            
            // Get the logged-in user's ID from the session
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'U001'; // Default to U001 if not logged in
            
            // --- Generate booking ID safely (lock table row) ---
            $booking_id_query = "SELECT MAX(SUBSTRING(h_book_id, 3)) as max_id FROM hotel_booking_t FOR UPDATE";
            $result = mysqli_query($connection, $booking_id_query);
            $row = mysqli_fetch_assoc($result);
            $max_id = isset($row['max_id']) ? (int)$row['max_id'] : 0;
            $next_booking_id = str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
            $h_book_id = "BK" . $next_booking_id;
            
            // Debug: Output booking info before insert
            error_log("Booking Insert: h_book_id=$h_book_id, user_id=$user_id, customer_id=$customer_id, hotel_id=$hotel_id, r_type_id=$r_type_id, checkin=$checkin, checkout=$checkout, room_count=$room_count, adult=$adult, child=$child");
            
            // --- Use prepared statement for booking insert ---
            $stmt = mysqli_prepare($connection, "INSERT INTO hotel_booking_t (h_book_id, user_id, customer_id, hotel_id, r_type_id, check_in_date, check_out_date, status, room_count, adult_count, child_count) VALUES (?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?, ?, ?)");
            if (!$stmt) throw new Exception('Prepare booking insert failed: ' . mysqli_error($connection));
            mysqli_stmt_bind_param($stmt, 'ssssssssii', $h_book_id, $user_id, $customer_id, $hotel_id, $r_type_id, $checkin, $checkout, $room_count, $adult, $child);
            if (!mysqli_stmt_execute($stmt)) {
                error_log('Booking insert error: ' . mysqli_stmt_error($stmt));
                throw new Exception('Booking insert error: ' . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
            
            // --- Generate payment ID safely (lock table row) ---
            $payment_id_query = "SELECT MAX(SUBSTRING(h_payment_id, 3)) as max_id FROM hotel_payment_t FOR UPDATE";
            $result = mysqli_query($connection, $payment_id_query);
            $row = mysqli_fetch_assoc($result);
            $max_payment_id = isset($row['max_id']) ? (int)$row['max_id'] : 0;
            $next_payment_id = str_pad($max_payment_id + 1, 4, '0', STR_PAD_LEFT);
            $h_payment_id = "HP" . $next_payment_id;
            
            // Get current date for payment date
            $payment_date = date('Y-m-d');
            
            // --- Use prepared statement for payment insert ---
            $stmt2 = mysqli_prepare($connection, "INSERT INTO hotel_payment_t (h_payment_id, h_book_id, payment_date, amount, method, status) VALUES (?, ?, ?, ?, ?, 'Paid')");
            if (!$stmt2) throw new Exception('Prepare payment insert failed: ' . mysqli_error($connection));
            mysqli_stmt_bind_param($stmt2, 'sssss', $h_payment_id, $h_book_id, $payment_date, $total, $payment_method);
            if (!mysqli_stmt_execute($stmt2)) {
                error_log('Payment insert error: ' . mysqli_stmt_error($stmt2));
                throw new Exception('Payment insert error: ' . mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);
            
            // Commit the changes
            mysqli_commit($connection);
            
            // Store booking ID in session for hotelPaymentComplete.php
            $_SESSION['last_booking_id'] = $h_book_id;
            
            $payment_success = true;
            
            if ($payment_success) {
                header("Location: hotelPaymentComplete.php");
                exit();
            }
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($connection);
            $error_message = "Error processing payment: " . $e->getMessage();
        }
    }

    // Return JSON if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        if ($payment_success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $error_message]);
        }
        exit();
    }
}

// NOW it's safe to start outputting HTML
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
            <?php if ($payment_success): ?>
                <div class="payment-success">
                    <h2>Payment Successful!</h2>
                    <p>Your hotel booking has been confirmed. Thank you for choosing Travooli.</p>
                    <a href="index.php" class="home-btn">Return to Home</a>
                </div>
            <?php else: ?>
                <div class="content-grid">
                    <!-- Left Column -->
                    <div class="left-column">
                        <h2 class="section-title">Payment method</h2>
                        
                        <p class="section-description">
                            Select a payment method below. Tripma processes your payment
                            securely with end-to-end encryption.
                        </p>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <div class="payment-methods-card">
                                <div class="payment-methods">
                                    <div class="payment-method">
                                        <div class="method-content">
                                            <img src="icon/card.svg" alt="Credit Card" class="method-icon">
                                            <div class="method-name">Debit/Credit Card</div>
                                        </div>
                                        <div class="radio-button">
                                            <input type="radio" id="credit_card" name="payment_method" value="Credit Card">
                                        </div>
                                    </div>
                                    
                                    <div class="method-divider"></div>
                                    
                                    <div class="payment-method">
                                        <div class="method-content">
                                            <img src="icon/google.svg" alt="Google Pay" class="method-icon">
                                            <div class="method-name">Google Pay</div>
                                        </div>
                                        <div class="radio-button">
                                            <input type="radio" id="google_pay" name="payment_method" value="Google Pay">
                                        </div>
                                    </div>
                                    
                                    <div class="method-divider"></div>
                                    
                                    <div class="payment-method">
                                        <div class="method-content">
                                            <img src="icon/apple.svg" alt="Apple Pay" class="method-icon">
                                            <div class="method-name">Apple Pay</div>
                                        </div>
                                        <div class="radio-button">
                                            <input type="radio" id="apple_pay" name="payment_method" value="Apple Pay">
                                        </div>
                                    </div>
                                    
                                    <div class="method-divider"></div>
                                    
                                    <div class="payment-method">
                                        <div class="method-content">
                                            <img src="icon/paypal.svg" alt="PayPal" class="method-icon">
                                            <div class="method-name">Paypal</div>
                                        </div>
                                        <div class="radio-button">
                                            <input type="radio" id="paypal" name="payment_method" value="Paypal">
                                        </div>
                                    </div>
                                    
                                    <div class="method-divider"></div>
                                    
                                    <div class="payment-method">
                                        <div class="method-content">
                                            <img src="icon/amazonpay.svg" alt="Amazon Pay" class="method-icon">
                                            <div class="method-name">Amazon Pay</div>
                                        </div>
                                        <div class="radio-button">
                                            <input type="radio" id="amazon_pay" name="payment_method" value="Amazon Pay">
                                        </div>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancellation Policy -->
                            <div class ="cancel-flight">
                                <h1>Cancellation Policy</h1>
                                <p>
                                This hotel booking has a flexible cancellation policy. If you cancel or change your booking up to 24 hours before the check-in date, you may be eligible for a refund or minimal fees, depending on your booking type.
                                </p>
                                <p>
                                All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the hotel and booking type. For full details, please review the cancellation policy for this hotel during the booking process.
                                </p> 
                                <div class="back-button-container">
                                    <a href="javascript:history.back()" class="back-button">Back</a>
                                </div>
                            </div>
                    </div>
                    <!-- Right Column -->
                    <div class="right-column">
                        <div class="price-card">
                            <?php
                            // Get room type name
                            $room_type_query = "SELECT type_name FROM room_type_t WHERE r_type_id = '$r_type_id'";
                            $room_type_result = mysqli_query($connection, $room_type_query);
                            $room_type = mysqli_fetch_assoc($room_type_result);
                            $room_type_name = $room_type ? $room_type['type_name'] : 'Room';
                            ?>
                            <h2>Price Details (<?php echo htmlspecialchars($room_type_name); ?>)</h2>
                            
                            <div class="price-item">
                                <span>Subtotal</span>
                                <span>RM<?php echo number_format($room_price, 2); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Taxes & Fees</span>
                                <span>RM<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="price-item">
                                <span>Discount</span>
                                <span>RM0.00</span>
                            </div>
                            <hr>
                            <div class="total">
                                <span>Total</span>
                                <span>RM<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                        <button type="submit" class="proceed-btn">Proceed to Payment</button>
                    </div>
                </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/hotelPayment.js"></script>
</body>
</html>