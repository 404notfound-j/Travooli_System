<?php
  // Start session to check login status
  session_start();
  include 'connection.php';

  // --- Cancel Booking Backend Logic (AJAX/POST) ---
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
    $response = ['success' => false, 'message' => 'Unknown error'];

    if ($booking_id) {
      // Begin transaction
      mysqli_begin_transaction($connection);
      
      try {
        // First get payment amount from hotel_payment_t
        $payment_query = "SELECT amount, method FROM hotel_payment_t WHERE h_book_id='$booking_id'";
        $payment_result = mysqli_query($connection, $payment_query);
        
        if (!$payment_result || mysqli_num_rows($payment_result) == 0) {
          throw new Exception("Payment record not found for booking ID: $booking_id");
        }
        
        $payment_data = mysqli_fetch_assoc($payment_result);
        $refund_amount = $payment_data['amount'];
        $refund_method = $payment_data['method'];
        
        // Update booking status to 'cancelled'
        $update_booking = "UPDATE hotel_booking_t SET status='Cancelled' WHERE h_book_id='$booking_id'";
        if (!mysqli_query($connection, $update_booking)) {
          throw new Exception("Failed to update booking status: " . mysqli_error($connection));
        }
        
        // Update payment status to 'refunded'
        $update_payment = "UPDATE hotel_payment_t SET status='Refunded' WHERE h_book_id='$booking_id'";
        if (!mysqli_query($connection, $update_payment)) {
          throw new Exception("Failed to update payment status: " . mysqli_error($connection));
        }
        
        // Current date and time for refund
        $refund_date = date('Y-m-d H:i:s');
        
        // Find the next available 4-digit HFID starting from 0001
        $max_attempts = 5;
        $attempt = 0;
        $refund_id = null;
        
        while ($attempt < $max_attempts && $refund_id === null) {
          try {
            // Start with HF0001 and check if it exists
            for ($i = 1; $i <= 9999; $i++) {
              // Format to exactly 4 digits with leading zeros
              $candidate_id = 'HF' . str_pad($i, 4, '0', STR_PAD_LEFT);
              
              // Check if this ID already exists
              $check_query = "SELECT h_refund_id FROM hotel_refund_t WHERE h_refund_id = '$candidate_id'";
              $check_result = mysqli_query($connection, $check_query);
              
              // If ID doesn't exist, use it
              if (!$check_result || mysqli_num_rows($check_result) == 0) {
                $refund_id = $candidate_id;
                break;
              }
            }
            
            // If we couldn't find an available ID
            if ($refund_id === null) {
              throw new Exception("No available refund IDs in the range HF0001-HF9999");
            }
            
            // Insert record into hotel_refund_t
            $refund_insert = "INSERT INTO hotel_refund_t (h_refund_id, h_book_id, refund_amt, refund_date, status, refund_method) 
                              VALUES ('$refund_id', '$booking_id', '$refund_amount', '$refund_date', 'Completed', '$refund_method')";
            
            if (!mysqli_query($connection, $refund_insert)) {
              // If duplicate entry error, retry
              if (mysqli_errno($connection) == 1062) {
                $refund_id = null;
                $attempt++;
              } else {
                throw new Exception("Failed to create refund record: " . mysqli_error($connection));
              }
            }
          } catch (Exception $e) {
            $refund_id = null;
            $attempt++;
            if ($attempt >= $max_attempts) {
              throw new Exception("Failed to generate a unique refund ID after $max_attempts attempts: " . $e->getMessage());
            }
          }
        }
        
        if ($refund_id === null) {
          throw new Exception("Failed to generate a unique refund ID after $max_attempts attempts");
        }
        
        // If we got here, commit the transaction
        mysqli_commit($connection);
        $response = ['success' => true, 'message' => 'Booking cancelled and refund processed successfully.'];
        
      } catch (Exception $e) {
        // An error occurred, rollback the transaction
        mysqli_rollback($connection);
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
      }
    } else {
      $response = ['success' => false, 'message' => 'Booking ID missing.'];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
  }

  // Check if user is logged in
  if (!isset($_SESSION['user_id'])) {
    // Redirect to sign in if not logged in
    error_log("User not logged in, redirecting to signIn.php");
    header("Location: signIn.php");
    exit();
  }
  
  // Get the latest booking info
  $user_id = $_SESSION['user_id'];
  error_log("User ID: $user_id - Checking for bookings");
  
  // Check if there's a booking ID in the session (set by hotelPayment.php)
  $booking_id = isset($_SESSION['last_booking_id']) ? $_SESSION['last_booking_id'] : null;
  
  // Build the query - if we have a specific booking ID, use it, otherwise get the latest
  $booking_query = "SELECT hb.*, hp.amount, hp.method, hp.payment_date, c.fst_name, c.lst_name, 
                    h.name AS hotel_name, rt.type_name
                    FROM hotel_booking_t hb 
                    JOIN hotel_payment_t hp ON hb.h_book_id = hp.h_book_id
                    JOIN customer_t c ON hb.customer_id = c.customer_id
                    JOIN hotel_t h ON hb.hotel_id = h.hotel_id
                    JOIN room_type_t rt ON hb.r_type_id = rt.r_type_id
                    WHERE hb.user_id = '$user_id' AND hb.status != 'Cancelled'";
                    
  // Add booking ID filter if available, otherwise get the latest
  if ($booking_id) {
    $booking_query .= " AND hb.h_book_id = '$booking_id'";
  } else {
    $booking_query .= " ORDER BY hp.payment_date DESC, hb.h_book_id DESC LIMIT 1";
  }
  
  $booking_result = mysqli_query($connection, $booking_query);
  
  // Check for database errors
  if (!$booking_result) {
    error_log("Database error in hotelPaymentComplete.php: " . mysqli_error($connection));
    header("Location: noHotelBooking.php");
    exit();
  }
  
  if (mysqli_num_rows($booking_result) > 0) {
    $booking = mysqli_fetch_assoc($booking_result);
    $customer_name = $booking['fst_name'] . ' ' . $booking['lst_name'];
    $hotel_name = $booking['hotel_name'];
    $room_type = $booking['type_name'];
    $booking_id = $booking['h_book_id'];
    $check_in = date('D, M j', strtotime($booking['check_in_date']));
    $check_out = date('D, M j', strtotime($booking['check_out_date']));
    $room_count = isset($booking['room_count']) ? (int)$booking['room_count'] : 1;
    $adult_count = isset($booking['adult_count']) ? (int)$booking['adult_count'] : 1;
    $child_count = isset($booking['child_count']) ? (int)$booking['child_count'] : 0;
    
    // Calculate nights
    $checkin_date = new DateTime($booking['check_in_date']);
    $checkout_date = new DateTime($booking['check_out_date']);
    $interval = $checkin_date->diff($checkout_date);
    $nights = $interval->days > 0 ? $interval->days : 1;
    
    // Get room price from DB
    $hotel_id = $booking['hotel_id'];
    $r_type_id = $booking['r_type_id'];
    $room_price_query = "SELECT price_per_night FROM hotel_room_t WHERE hotel_id = '$hotel_id' AND r_type_id = '$r_type_id'";
    $room_price_result = mysqli_query($connection, $room_price_query);
    $room_price_row = mysqli_fetch_assoc($room_price_result);
    $price_per_night = $room_price_row ? (float)$room_price_row['price_per_night'] : 0;
    
    // Calculate subtotal, tax, total
    $subtotal = $price_per_night * $nights * $room_count;
    $tax = round($subtotal * 0.06, 2);
    $total_amount = $subtotal + $tax;

    // Debug output
    echo "<!-- DEBUG: check_in_date={$booking['check_in_date']}, check_out_date={$booking['check_out_date']}, room_count={$booking['room_count']}, adult_count={$booking['adult_count']}, child_count={$booking['child_count']} -->";
  } else {
    // No hotel bookings found - clear session and redirect to no booking page
    error_log("No bookings found for user ID: $user_id - Redirecting to noHotelBooking.php");
    unset($_SESSION['last_booking_id']);
    header("Location: noHotelBooking.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Payment Complete</title>
  <link rel="stylesheet" href="css/hotelPaymentComplete.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>
    <main class="container">
      <h1 class="title">Have a good trip, <?php echo htmlspecialchars($customer_name); ?>!</h1>
      <p class="reference">Booking Reference: <span>#<?php echo htmlspecialchars($booking_id); ?></span></p>
      <p class="description">
          Thank you for booking your stay with <span>Travooli</span>!<br>
          Below is a summary of your trip to <?php echo htmlspecialchars($hotel_name); ?>. 
          A copy of your booking confirmation has been sent to your email address. You can always revisit this information in the My Trips section of our app. Safe travels!
      </p>

      <section class="layout">
        <div class="ticket">
          <div class="flight-times">
            <div class="departure">
              <h2><?php echo htmlspecialchars($check_in); ?></h2>
              <p>Check-in</p>
            </div>
            <div class="flight-path">
                <img src="icon/hotelPaymentComplete.svg">
            </div>
            <div class="arrival">
              <h2><?php echo htmlspecialchars($check_out); ?></h2>
              <p>Check-out</p>
            </div>
          </div>
          <div class="boarding-pass">
            <div class="passenger-info">
              <img src="icon/avatar.svg" alt="Passenger" class="avatar">
              <div>
                <h3><?php echo htmlspecialchars($customer_name); ?></h3>
              </div>
              <span class="class"><?php echo htmlspecialchars($room_type); ?></span>
            </div>
            <div class="flight-details">
              <div class="detail">
                <div class="icon">
                  <img src="icon/calendar1.svg" alt="Calendar">
                </div>
                <div>
                  <p>Check-in time</p>
                  <span>12:00 pm</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/timmer.svg" alt="Clock">
                </div>
                <div>
                  <p>Check-out time</p>
                  <span>11:30 pm</span>
                </div>
              </div>
              <div class="detail">
                <div class="icon">
                  <img src="icon/door.svg" alt="Gate">
                </div>
                <div>
                  <p>Room no.</p>
                  <span>On arrival</span>
                </div>
              </div>
            </div>
            <div class="flight-code">
              <div class="flight-code-content">
                <h3 style="font-size: 0.8em;font-weight: normal;">
                  Rooms: <?php echo $room_count; ?> | Adults: <?php echo $adult_count; ?> | Children: <?php echo $child_count; ?> | Nights: <?php echo $nights; ?>
                </h3>
                <p><?php echo htmlspecialchars($booking_id); ?></p>
              </div>
              <img src="icon/barcode.svg" alt="Barcode" class="barcode">
            </div>
          </div>
        </div>
        <div class="price-breakdown">
          <h3>Price breakdown</h3>
          <div class="price-items">
            <div class="price-item">
              <span>Room Price (RM<?php echo number_format($price_per_night, 2); ?> × <?php echo $nights; ?> nights × <?php echo $room_count; ?> rooms)</span>
              <span>RM<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="price-item">
              <span>Taxes and Fees</span>
              <span>RM<?php echo number_format($tax, 2); ?></span>
            </div>
            <div class="price-item">
              <span>Discount</span>
              <span>RM0.00</span>
            </div>
            <div class="price-total">
              <span>Amount Paid</span>
              <span>RM<?php echo number_format($total_amount, 2); ?></span>
            </div>
          </div>
        </div>
      </section>

      <section class="action-buttons">
        <div class="share-btn">
          <img src="icon/share.svg" alt="btn"> 
        </div>
        <button class="download-btn">
          Download
        </button>
        <button class="home-btn">
          Back to Homepage
        </button>
      </section>
    <!-- Ratings Section -->

      <section class="ratings">
        <p>Your feedback matters to us. Let us know how we can improve your experience.</p>
        <div class="stars">
          <i class="fas fa-star" id="star-1" data-rating="1"></i>
          <i class="fas fa-star" id="star-2" data-rating="2"></i>
          <i class="fas fa-star" id="star-3" data-rating="3"></i>
          <i class="fas fa-star" id="star-4" data-rating="4"></i>
          <i class="fas fa-star" id="star-5" data-rating="5"></i>
        </div>
        <textarea placeholder="Share your thoughts..."></textarea>
        <div class="rating-buttons">
          <button class="cancel-btn">Cancel</button>
          <button class="submit-btn"
            data-booking-id="<?php echo htmlspecialchars($booking_id); ?>"
            data-hotel-id="<?php echo htmlspecialchars($booking['hotel_id']); ?>"
            data-customer-id="<?php echo htmlspecialchars($booking['customer_id']); ?>">
            Submit
          </button>
        </div>
      </section>

      <section class ="cancel-flight">
        <h1>Cancellation Policy</h1>
        <p>
          This hotel booking has a flexible cancellation policy. If you cancel or change your reservation up to 24 hours before the check-in date, you may be eligible for a refund or minimal fees, depending on the hotel's policy. Refundable bookings are entitled to a full or partial refund.
        </p>
        <p>
          All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the hotel and room type. For full details, please review the cancellation policy for this hotel during the booking process.
        </p> 
        <button class="cancel-flight-btn" onclick="showCancelBookingReminder('<?php echo $booking_id; ?>')">
          Cancel Booking
        </button>
      </section>
    </main>
  </div>
  <?php include 'u_footer_1.php'; ?>
  <?php include 'u_footer_2.php'; ?>
  <script src="js/hotelpaymentcomplete.js"></script>
</body>
</html>