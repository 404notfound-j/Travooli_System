<?php
  // Start session to check login status
  session_start();
  include 'connection.php';

  // --- Cancel Booking Backend Logic (AJAX/POST) ---
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelBooking') {
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
    $response = ['success' => false, 'message' => 'Unknown error'];

    if ($booking_id) {
      // Update booking status to 'Cancelled'
      $update = "UPDATE hotel_booking_t SET status='Cancelled' WHERE h_book_id='$booking_id'";
      if (mysqli_query($connection, $update)) {
        $response = ['success' => true, 'message' => 'Booking cancelled successfully.'];
      } else {
        $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)];
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
    header("Location: noBooking.php");
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
    error_log("No bookings found for user ID: $user_id - Redirecting to noBooking.php");
    unset($_SESSION['last_booking_id']);
    header("Location: noBooking.php");
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
          This flight has a flexible cancellation policy. If you cancel or change your flight up to 24 hours before the departure date, you may be eligible for a refund or minimal fees, depending on your ticket type. Refundable ticket holders are entitled to a full or partial refund.
        </p>
        <p>
          All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. However, cancellation policies may vary based on the airline and ticket type. For full details, please review the cancellation policy for this flight during the booking process.
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