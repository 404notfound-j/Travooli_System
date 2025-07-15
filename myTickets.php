<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
  // Redirect to sign in if not logged in
  error_log("User not logged in, redirecting to signIn.php");
  header("Location: signIn.php");
  exit();
}
$bookingIdToQuery = null;

$latestBookingQuery = "SELECT f_book_id FROM flight_booking_t WHERE user_id = ? AND status = 'confirmed' ORDER BY book_date DESC LIMIT 1";
$stmtLatestBooking = mysqli_prepare($connection, $latestBookingQuery);
if ($stmtLatestBooking) {
    mysqli_stmt_bind_param($stmtLatestBooking, "s", $_SESSION['user_id']);
    mysqli_stmt_execute($stmtLatestBooking);
    $resultLatestBooking = mysqli_stmt_get_result($stmtLatestBooking);
    if ($rowLatestBooking = mysqli_fetch_assoc($resultLatestBooking)) {
        $bookingIdToQuery = $rowLatestBooking['f_book_id'];
    }
    mysqli_stmt_close($stmtLatestBooking);
}

// If no flight bookings found, redirect to no booking page
if (empty($bookingIdToQuery)) {
    error_log("No flight bookings found for user ID: " . $_SESSION['user_id'] . " - redirecting to noFlightBooking.php");
    header("Location: noFlightBooking.php");
    exit();
}

$ticketPrice = $baggagePrice = $mealPrice = $taxPrice = $finalTotalPrice = 0;
$numPassenger = 0;
$selectedSeatsDisplay = [];
$classId = 'PE';
$classLabelMap = [
    'EC' => 'Economy Class',
    'PE' => 'Premium Economy',
    'BC' => 'Business Class',
    'FC' => 'First Class'
];
$classLabel = 'Premium Economy';

$flightDetailsDB = [];
$passengersForDisplay = [];

if (!empty($bookingIdToQuery)) {
    $query = "SELECT
                fb.f_book_id AS flight_booking_id, fb.user_id, fb.flight_id, fb.book_date, fb.status AS booking_status,
                fi.departure_time, fi.arrival_time, fi.orig_airport_id, fi.dest_airport_id,
                a.airline_name, a.airline_id,
                fbi.total_amount AS total_amount_paid,
                fbi.base_fare_total AS ticket_base_price,
                fbi.baggage_total AS baggage_fees,
                fbi.meal_total AS meal_fees,
                fbi.passenger_count AS num_passenger,
                fbi.flight_date AS flight_date
              FROM flight_booking_t fb
              JOIN flight_info_t fi ON fb.flight_id = fi.flight_id
              JOIN airline_t a ON fi.airline_id = a.airline_id
              JOIN flight_booking_info_t fbi ON fb.f_book_id = fbi.f_book_id
              WHERE fb.f_book_id = ?";

    $stmt = mysqli_prepare($connection, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $bookingIdToQuery);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $flightDetailsDB[] = $row;
            $ticketPrice = $row['ticket_base_price'];
            $baggagePrice = $row['baggage_fees'];
            $mealPrice = $row['meal_fees'];
            $finalTotalPrice = $row['total_amount_paid'];
            $numPassenger = $row['num_passenger'];
            $classLabel = $classLabelMap[$classId] ?? 'Unknown';
        }
        mysqli_stmt_close($stmt);
    }

    $passengerQuery = "
      SELECT 
        pt.pass_id, pt.fst_name, pt.lst_name, pt.gender, pt.dob, pt.country,
        ps.class_id, ps.baggage_id, ps.meal_id,
        fs.seat_no
      FROM passenger_service_t ps
      JOIN passenger_t pt ON ps.pass_id = pt.pass_id
      LEFT JOIN flight_seats_t fs ON fs.pass_id = pt.pass_id
      WHERE ps.f_book_id = ?
    ";

    $stmtPassengers = mysqli_prepare($connection, $passengerQuery);
    if ($stmtPassengers) {
        mysqli_stmt_bind_param($stmtPassengers, "s", $bookingIdToQuery);
        mysqli_stmt_execute($stmtPassengers);
        $resultPassengers = mysqli_stmt_get_result($stmtPassengers);
        while ($row = mysqli_fetch_assoc($resultPassengers)) {
            $passengersForDisplay[] = $row;
        }

        $seen = [];
        $uniquePassengers = [];

        foreach ($passengersForDisplay as $p) {
            if (!in_array($p['pass_id'], $seen)) {
                $seen[] = $p['pass_id'];
                $uniquePassengers[] = $p;
            }
        }
        $passengersForDisplay = $uniquePassengers;

        mysqli_stmt_close($stmtPassengers);
    }
}

$userName = "Guest";
$userProfilePhotoSrc = 'images/default_profile.png';
$loggedInUserId = $_SESSION['user_id'] ?? null;

if ($loggedInUserId) {
    $userQuery = "SELECT fst_name, profile_pic FROM user_detail_t WHERE user_id = ?";
    $stmtUser = mysqli_prepare($connection, $userQuery);
    if ($stmtUser) {
        mysqli_stmt_bind_param($stmtUser, "s", $loggedInUserId);
        mysqli_stmt_execute($stmtUser);
        $userResult = mysqli_stmt_get_result($stmtUser);
        if ($userRow = mysqli_fetch_assoc($userResult)) {
            $userName = htmlspecialchars($userRow['fst_name']);
            if (!empty($userRow['profile_pic'])) {
                $userProfilePhotoSrc = 'getProfileImage.php?user_id=' . urlencode($loggedInUserId);
            }
        }
        mysqli_stmt_close($stmtUser);
    }
}

// Dummy data fallback removed - users with no bookings are now redirected to noFlightBooking.php
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Payment Complete</title>
  <link rel="stylesheet" href="css/payment_complete.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..1000&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <header>
    <?php include 'userHeader.php'; ?>
  </header>
  <main class="container">
    <h1 class="title">Have a good trip, <?= $userName ?>!</h1>
    <p class="reference">Booking Reference: <span>#<?= $flightDetailsDB[0]['flight_booking_id'] ?></span></p>
    <p class="description">
        Thank you for booking your travel with <span>Travooli</span>!<br>
        Below is a summary of your trip.
        A copy of your booking confirmation has been sent to your email address. You can always revisit this information in the My Trips section of our app. Safe travels!
    </p>

    <?php foreach ($passengersForDisplay as $index => $passenger): ?>
    <section class="layout flex-layout">
      <div class="ticket">
        <div class="flight-times">
          <div class="departure">
            <h2><?= date("h:i A", strtotime($flightDetailsDB[0]['departure_time'])) ?></h2>
            <p><?= $flightDetailsDB[0]['orig_airport_id'] ?></p>
          </div>
          <div class="flight-path">
            <div class="line"></div>
            <i class="fas fa-plane"></i>
            <div class="line"></div>
          </div>
          <div class="arrival">
            <h2><?= date("h:i A", strtotime($flightDetailsDB[0]['arrival_time'])) ?></h2>
            <p><?= $flightDetailsDB[0]['dest_airport_id'] ?></p>
          </div>
        </div>
        <div class="boarding-pass">
          <div class="passenger-info">
            <img src="icon/avatar.svg" alt="Passenger" class="avatar">
            <div>
              <h3><?= htmlspecialchars($passenger['fst_name']) ?> <?= htmlspecialchars($passenger['lst_name']) ?></h3>
              <p>Boarding Pass <?= $flightDetailsDB[0]['flight_booking_id'] ?></p>
            </div>
            <?php 
              $paxClassId = $passenger['class_id'] ?? 'PE'; 
              $paxClassLabel = $classLabelMap[$paxClassId] ?? 'Unknown';
            ?>
            <div class='ticket-class' style='margin-left: 100px;'><?= $paxClassLabel ?> (<?= $paxClassId ?>)</div>
          </div>
          <div class="flight-details">
            <div class="detail">
              <div class="icon"><img src="icon/calendar1.svg" alt="Calendar"></div>
              <div><p>Date</p><span><?= date('j F Y', strtotime($flightDetailsDB[0]['flight_date'])) ?></span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/timmer.svg" alt="Clock"></div>
              <div><p>Flight time</p><span><?= date("H:i", strtotime($flightDetailsDB[0]['departure_time'])) ?></span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/door.svg" alt="Gate"></div>
              <div><p>Gate</p><span>A12</span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/seat.svg" alt="Seat"></div>
              <div><p>Seat</p><span><?= $passenger['seat_no'] ?? 'N/A' ?></span></div>
            </div>
          </div>
          <div class="flight-code">
            <div class="flight-code-content">
              <h3><?= $flightDetailsDB[0]['airline_name'] ?></h3>
              <p><?= $flightDetailsDB[0]['flight_id'] ?></p>
            </div>
            <img src="icon/barcode.svg" alt="Barcode" class="barcode">
          </div>
        </div>
      </div>

      <?php if ($index === 0): ?>
      <div class="price-breakdown">
        <h3>Price breakdown</h3>
        <div class="price-items">
          <div class="price-item"><span>Flight Price</span><span>RM <?= number_format($ticketPrice, 2) ?></span></div>
          <div class="price-item"><span>Baggage Fees</span><span>RM <?= number_format($baggagePrice, 2) ?></span></div>
          <div class="price-item"><span>Meal Add-on</span><span>RM <?= number_format($mealPrice, 2) ?></span></div>
          <div class="price-item"><span>Taxes and Fees</span><span>RM <?= number_format(($ticketPrice+$baggagePrice+$mealPrice) *0.06, 2)?></span></div>
          <div class="price-total"><span>Amount Paid</span><span>RM <?= number_format($finalTotalPrice, 2) ?></span></div>
        </div>
      </div>
      <?php endif; ?>
    </section>
    <?php endforeach; ?>

    <section class="action-buttons">
      <div class="share-btn"><img src="icon/share.svg" alt="btn"></div>
      <button class="download-btn">Download</button>
      <button class="home-btn" onclick="window.location.href='U_dashboard.php'">Back to Homepage</button>
    </section>

    <section class="ratings">
      <p>Your feedback matters to us. Let us know how we can improve your experience.</p>
      <div class="stars">
        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
      </div>
      <textarea placeholder="Share your thoughts..."></textarea>
      <div class="rating-buttons">
        <button class="cancel-btn">Cancel</button>
        <button class="submit-btn">Submit</button>
      </div>
    </section>

    <section class="cancel-flight"> 
      <h1>Cancellation Policy</h1> 
      <p>This flight has a flexible cancellation policy. You may be eligible for a refund if cancelled at least 24 hours before departure.</p> 
      <p>All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee. 
      However, cancellation policies may vary based on the airline and ticket type. For full details, please review the cancellation policy for this flight during the booking process.</p> 
      <button class="cancel-flight-btn" onclick="showCancelConfirmation()">Cancel Flight</button> 
    </section>
  </main>
  <script src="js/flight_Complete.js"></script>
  <script>const bookingIdFromPHP = "<?= $bookingIdToQuery ?>";
 sessionStorage.setItem("bookingId", bookingIdFromPHP);
</script>
  <?php include 'u_footer_1.php'; ?>
  <?php include 'u_footer_2.php'; ?>
</body>
</html>