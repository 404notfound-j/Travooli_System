<?php
// payment_complete.php
session_start();
include 'connection.php'; // Include your database connection file

// --- Retrieve booking ID ---
// Priority: 1. From URL (after successful payment) 2. Latest for logged-in user if no URL ID
$bookingIdFromURL = $_GET['bookingId'] ?? null;

$bookingIdToQuery = $bookingIdFromURL; // Primary source
if (empty($bookingIdToQuery) && isset($_SESSION['user_id'])) {
    $latestBookingQuery = "SELECT flight_booking_id FROM flight_booking_t WHERE user_id = ? ORDER BY booking_date DESC LIMIT 1";
    $stmtLatestBooking = mysqli_prepare($connection, $latestBookingQuery);
    if ($stmtLatestBooking) {
        mysqli_stmt_bind_param($stmtLatestBooking, "s", $_SESSION['user_id']);
        mysqli_stmt_execute($stmtLatestBooking);
        $resultLatestBooking = mysqli_stmt_get_result($stmtLatestBooking);
        if ($rowLatestBooking = mysqli_fetch_assoc($resultLatestBooking)) {
            $bookingIdToQuery = $rowLatestBooking['flight_booking_id'];
        }
        mysqli_stmt_close($stmtLatestBooking);
    } else {
        error_log("DB Query prep failed for latest booking in payment_complete.php: " . mysqli_error($connection));
    }
}


// --- Initialize all booking data variables (will be populated from DB) ---
$ticketPrice = 0;
$baggagePrice = 0;
$mealPrice = 0;
$taxPrice = 0;
$finalTotalPrice = 0;
$numPassenger = 0;
$selectedSeatsDisplay = []; // Array of seat numbers for display
$classId = 'PE';
$classLabelMap = [ // Re-define map here to ensure it's always available
    'EC' => 'Economy Class',
    'PE' => 'Premium Economy',
    'BC' => 'Business Class',
    'FC' => 'First Class'
];
$classLabel = 'Premium Economy'; // Default


$flightDetailsDB = [];
$passengersForDisplay = []; // Array to hold passenger_t records


// --- Main DB Query to get ALL booking and flight info ---
if (!empty($bookingIdToQuery)) {
    $query = "SELECT
                fb.flight_booking_id, fb.user_id, fb.flight_id, fb.booking_date, fb.status AS booking_status,
                ft.departure_time, ft.arrival_time, ft.orig_airport_id, ft.dest_airport_id,
                a.airline_name, a.airline_id,
                fbi.total_amount_paid, fbi.ticket_base_price, fbi.baggage_fees,
                fbi.meal_fees, fbi.tax_amount, 
                fbi.num_passenger, fbi.selected_seat_numbers, fbi.class_id AS booking_class_id,
                fbi.flight_date
              FROM flight_booking_t fb
              JOIN flight_info_t ft ON fb.flight_id = ft.flight_id
              JOIN airline_t a ON ft.airline_id = a.airline_id
              JOIN flight_booking_info_t fbi ON fb.flight_booking_id = fbi.flight_booking_id
              WHERE fb.flight_booking_id = ?";
    
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
            $taxPrice = $row['tax_amount'];
            $finalTotalPrice = $row['total_amount_paid'];
            $numPassenger = $row['num_passenger'];
            $classId = $row['booking_class_id'];
            $classLabel = $classLabelMap[$classId] ?? 'Unknown';

            $selectedSeatsDisplay = explode(',', $row['selected_seat_numbers']);

            // --- Fetch all passengers for this booking ---
            // $passengersQuery = "SELECT passenger_id, fst_name, lst_name, gender, dob, country, class_id, baggage_id, meal_id
            //                     FROM passenger_t WHERE flight_booking_id = ?";
            // $stmtPassengers = mysqli_prepare($connection, $passengersQuery);
            // if ($stmtPassengers) {
            //     mysqli_stmt_bind_param($stmtPassengers, "s", $bookingIdToQuery);
            //     mysqli_stmt_execute($stmtPassengers);
            //     $resultPassengers = mysqli_stmt_get_result($stmtPassengers);
            //     while($paxRow = mysqli_fetch_assoc($resultPassengers)) {
            //         $passengersForDisplay[] = $paxRow;
            //     }
            //     mysqli_stmt_close($stmtPassengers);
            // } else {
            //     error_log("DB Query prep failed for passengers_t in payment_complete.php: " . mysqli_error($connection));
            // }

        } else {
            error_log("No booking found for ID: " . $bookingIdToQuery);
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("DB Query prep failed in payment_complete.php: " . mysqli_error($connection));
    }
}


// --- Fetch User's Name and Profile Photo ---
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


// Use a default flight if DB query failed (no booking ID or DB error)
if (empty($flightDetailsDB)) {
    $flightDetailsDB[] = [
        'flight_booking_id' => $bookingIdToQuery ?? 'N/A',
        'flight_id' => 'N/A',
        'booking_date' => date('Y-m-d H:i:s'),
        'departure_time' => '00:00:00', 'arrival_time' => '00:00:00',
        'orig_airport_id' => 'N/A',
        'dest_airport_id' => 'N/A',
        'flight_date' => date('Y-m-d'),
        'duration' => '00:00',
        'airline_name' => 'Travooli Airlines',
        'airline_id' => 'TR',
        // Default values for info fields
        'total_amount_paid' => 0, 'ticket_base_price' => 0, 'baggage_fees' => 0,
        'meal_fees' => 0, 'tax_amount' => 0, 
        'num_passenger' => 0, 'selected_seat_numbers' => '', 'booking_class_id' => 'PE'
    ];
    // Also reset main price vars to 0 if defaults are used
    $ticketPrice = $baggagePrice = $mealPrice = $taxPrice = $finalTotalPrice = 0;
    $numPassenger = 0;
    $selectedSeatsDisplay = [];
    $classId = 'PE';
    $classLabel = 'Premium Economy';
}

// Prepare only ONE passenger for display if the request is to show only one card
if (!empty($passengersForDisplay)) {
    $firstPassenger = $passengersForDisplay[0];
    $passengersForDisplay = [$firstPassenger]; // Overwrite with only the first one
} else {
    // Fallback if no passengers were found at all (should be rare if $numPassenger > 0)
    $passengersForDisplay[] = [
        'passenger_id' => 'GENP1', 
        'fst_name' => $userName, 
        'lst_name' => '',
        'gender' => 'N/A', 'dob' => '0000-00-00', 'country' => 'N/A',
        'class_id' => $classId, 'baggage_id' => null, 'meal_id' => null,
        'seat_no' => !empty($selectedSeatsDisplay) ? $selectedSeatsDisplay[0] : 'N/A' // Use first seat from overall seats
    ];
}

?>
<!DOCTYPE html>
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
        <?php include 'userHeader.php';?>
    </header>
  <main class="container">
    <h1 class="title">Have a good trip, <?= $userName ?>!</h1>
    <p class="reference">Booking Reference:
      <span>
        <?= $flightDetailsDB[0]['flight_booking_id'] ?? ($bookingIdToQuery ?? 'N/A') ?>
      </span>
    </p>
    <p class="description">
        Thank you for booking your travel with <span>Travooli</span>!<br>
        Below is a summary of your trip.
        A copy of your booking confirmation has been sent to your email address. You can always revisit this information in the My Trips section of our app. Safe travels!
    </p>
    <?php foreach ($passengersForDisplay as $index => $passenger): // Loop through ONLY the first passenger for display ?>
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
          <br>
          <br>
          <div class="arrival">
            <h2><?= date("h:i A", strtotime($flightDetailsDB[0]['arrival_time'])) ?></h2>
            <p><?= $flightDetailsDB[0]['dest_airport_id'] ?></p>
          </div>
        </div>
        <div class="boarding-pass"> 
          <div class="passenger-info"> 
            <img src="<?= $userProfilePhotoSrc ?>" alt="User Profile Photo" class="avatar"> 
            <div>
              <h3><?= htmlspecialchars($passenger['fst_name']) ?> <?= htmlspecialchars($passenger['lst_name']) ?></h3> <p>Boarding Pass <?= $flightDetailsDB[0]['flight_booking_id'] ?? ($bookingIdToQuery ?? 'N/A') ?></p>
            </div>
            <?php 
                $paxClassId = $passenger['class_id'] ?? $classId; 
                $paxClassLabel = $classLabelMap[$paxClassId] ?? 'Unknown';
            ?>
            <?php echo "<div class='ticket-class' style='margin-left: 100px;'>{$paxClassLabel} ({$paxClassId})</div>"; ?>
          </div>
          <div class="flight-details"> 
            <div class="detail">
              <div class="icon"><img src="icon/calendar1.svg" alt="Calendar"></div> 
              <div><p>Date</p><span><?= date("Y-m-d", strtotime($flightDetailsDB[0]['flight_date'])) ?></span></div> 
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
      <?php if ($index === 0): // Price breakdown only once, after the first ticket ?> 
  <div class="price-breakdown">
    <h3>Price breakdown</h3>
    <div class="price-items">
      <div class="price-item"><span>Flight Price</span><span>RM <?= number_format($ticketPrice, 2) ?></span></div> 
      <div class="price-item"><span>Baggage Fees</span><span>RM <?= number_format($baggagePrice, 2) ?></span></div> 
      <div class="price-item"><span>Meal Add-on</span><span>RM <?= number_format($mealPrice, 2) ?></span></div> 
      <div class="price-item"><span>Taxes and Fees</span><span>RM <?= number_format($taxPrice, 2) ?></span></div> 
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
        <i class="fas fa-star"></i><i class="fas fa-star"></i> 
        <i class="fas fa-star"></i><i class="fas fa-star"></i> 
        <i class="fas fa-star"></i> 
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
      <p>All bookings made through <span>Travooli</span> are backed by our satisfaction guarantee.</p> 
      <button class="cancel-flight-btn" onclick="showCancelConfirmation()">Cancel Flight</button> 
    </section>
  </main>
</body>
</html>
<?php include 'u_footer_1.php'; ?> 
<?php include 'u_footer_2.php'; ?> 
<script src="js/flight_Complete.js"></script>