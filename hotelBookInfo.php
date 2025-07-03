<?php
// Start session to check login status
session_start();

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);

// If not logged in, redirect to the details page with the same parameters
if (!$user_logged_in) {
    $redirect_url = "hotelDetails.php";
    if (!empty($_SERVER['QUERY_STRING'])) {
        $redirect_url .= "?" . $_SERVER['QUERY_STRING'];
    }
    header("Location: " . $redirect_url);
    exit();
}

// Database connection
include 'connection.php';

// Get parameters from URL
$hotel_id = isset($_GET['hotel_id']) ? $_GET['hotel_id'] : 'H0001'; 
$r_type_id = isset($_GET['r_type_id']) ? $_GET['r_type_id'] : 'RT001';
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : (isset($_SESSION['checkin_date']) ? $_SESSION['checkin_date'] : date('Y-m-d'));
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : (isset($_SESSION['checkout_date']) ? $_SESSION['checkout_date'] : date('Y-m-d', strtotime('+1 day')));
$adult = isset($_GET['adult']) ? (int)$_GET['adult'] : 1;
$child = isset($_GET['child']) ? (int)$_GET['child'] : 0;
$room_count = isset($_GET['room']) ? (int)$_GET['room'] : 1;

// Get hotel information
$hotel_query = "SELECT h.* FROM hotel_t h WHERE h.hotel_id = '$hotel_id'";
$hotel_result = mysqli_query($connection, $hotel_query);
$hotel = mysqli_fetch_assoc($hotel_result);

// Get room information
$room_query = "SELECT hr.*, rt.type_name FROM hotel_room_t hr 
               JOIN room_type_t rt ON hr.r_type_id = rt.r_type_id 
               WHERE hr.hotel_id = '$hotel_id' AND hr.r_type_id = '$r_type_id'";
$room_result = mysqli_query($connection, $room_query);
$room = mysqli_fetch_assoc($room_result);

// Calculate number of nights
$checkin_date = new DateTime($checkin);
$checkout_date = new DateTime($checkout);
$interval = $checkin_date->diff($checkout_date);
$nights = $interval->days > 0 ? $interval->days : 1;

// Calculate prices
$room_price = $room['price_per_night'] * $nights * $room_count;
$tax = round($room_price * 0.06, 2); // 6% tax
$total = $room_price + $tax;

// Handle form submission - MOVED BEFORE ANY OUTPUT
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = mysqli_real_escape_string($connection, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($connection, $_POST['lastname']);
    $nationality = mysqli_real_escape_string($connection, $_POST['nationality']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    
    // Validate the data
    if (empty($firstname) || empty($lastname) || empty($nationality) || empty($email) || empty($phone)) {
        $error_message = "All fields are required.";
    } else {
        try {
            mysqli_begin_transaction($connection);
            
            // Check if customer already exists by email
            $existing_customer_query = "SELECT customer_id FROM customer_t WHERE email = '$email'";
            $existing_customer_result = mysqli_query($connection, $existing_customer_query);
            if ($existing_customer = mysqli_fetch_assoc($existing_customer_result)) {
                $customer_id = $existing_customer['customer_id'];
            } else {
                // Generate customer ID
                $customer_id_query = "SELECT MAX(SUBSTRING(customer_id, 2)) as max_id FROM customer_t";
                $result = mysqli_query($connection, $customer_id_query);
                $row = mysqli_fetch_assoc($result);
                $next_id = str_pad((int)$row['max_id'] + 1, 4, '0', STR_PAD_LEFT);
                $customer_id = "C" . $next_id;
                // Insert customer data
                $insert_customer = "INSERT INTO customer_t (customer_id, fst_name, lst_name, email, phone_no, nationality) 
                                VALUES ('$customer_id', '$firstname', '$lastname', '$email', '$phone', '$nationality')";
                mysqli_query($connection, $insert_customer);
            }
            
            // Commit the changes
            mysqli_commit($connection);
            
            // Form is valid, redirect to payment page with all parameters
            $redirect_url = "hotelPayment.php?";
            $redirect_url .= "customer_id=" . urlencode($customer_id);
            $redirect_url .= "&hotel_id=" . urlencode($hotel_id);
            $redirect_url .= "&r_type_id=" . urlencode($r_type_id);
            $redirect_url .= "&checkin=" . urlencode($checkin);
            $redirect_url .= "&checkout=" . urlencode($checkout);
            $redirect_url .= "&adult=" . urlencode($adult);
            $redirect_url .= "&child=" . urlencode($child);
            $redirect_url .= "&room=" . urlencode($room_count);
            $redirect_url .= "&firstname=" . urlencode($firstname);
            $redirect_url .= "&lastname=" . urlencode($lastname);
            $redirect_url .= "&nationality=" . urlencode($nationality);
            $redirect_url .= "&email=" . urlencode($email);
            $redirect_url .= "&phone=" . urlencode($phone);
            $redirect_url .= "&room_price=" . urlencode($room_price);
            $redirect_url .= "&tax=" . urlencode($tax);
            $redirect_url .= "&total=" . urlencode($total);
            
            header("Location: " . $redirect_url);
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($connection);
            $error_message = "Error saving customer data: " . $e->getMessage();
        }
    }
}

// NOW it's safe to start outputting HTML
include 'userHeader.php'; ?>
<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/hotelBookInfo.css">
<script src="js/loginReminder.js"></script>

<?php
// Debug output for troubleshooting
// Remove or comment out after debugging
//
// echo "<div style='background:#ffe;border:1px solid #fc0;padding:8px;margin:8px 0;'>DEBUG: room_count=$room_count, nights=$nights, checkin=$checkin, checkout=$checkout, adult=$adult, child=$child</div>";
?>
<div class="hotel-book-info-main">
    <div class="hotel-book-info-left-section">
        <h2 class="hotel-book-info-title">Who's Staying?</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
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
                        <input type="hidden" name="room" value="<?php echo htmlspecialchars($room_count); ?>">
                        <input type="hidden" name="r_type_id" value="<?php echo htmlspecialchars($r_type_id); ?>">
                        
                        <div class="payment-button-container">
                            <button type="submit" class="btn btn-primary hotel-book-info-submit">Continue</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="hotel-book-info-right">
                <div class="price-details-box">
                    <h3 class="price-details-title">Price Details (<?php echo htmlspecialchars($room['type_name']); ?>)</h3>
                    <div class="price-details-row"><span>Room Price ($<?php echo $room['price_per_night']; ?> × <?php echo $nights; ?> nights × <?php echo $room_count; ?> rooms)</span><span>$<?php echo $room_price; ?></span></div>
                    <div class="price-details-row"><span>Taxes & Fees (6%)</span><span>$<?php echo $tax; ?></span></div>
                    <div class="price-details-row"><span>Discount</span><span>$0</span></div>
                    <div class="price-details-total"><span>Total</span><span class="price-details-total-amount">$<?php echo $total; ?></span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/hotelBookInfo.js"></script>
<?php include 'u_footer_2.php'; ?>
