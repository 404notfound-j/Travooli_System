<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Travooli - Payment Complete</title>
  <link rel="stylesheet" href="css/payment_complete.css">
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
    <?php
include 'connection.php';
if (isset($_GET['classId'])) $_SESSION['selectedClass'] = $_GET['classId'];
$classId = $_SESSION['selectedClass'] ?? 'PE'; 
$classLabelMap = [
    'EC' => 'Economy Class',
    'PE' => 'Premium Economy',
    'BC' => 'Business Class',
    'FC' => 'First Class'
];
$classLabel = $classLabelMap[$classId] ?? 'Unknown';
$bookingIds = $_SESSION['booking_ids'] ?? [];
if (empty($bookingIds)) {
    echo "<p>No booking info found.</p>";
    exit;
}

$flightDetails = [];
foreach ($bookingIds as $bookingId) {
    $query = "SELECT fb.f_book_id, fb.flight_id, fb.book_date, ud.fst_name, ud.lst_name,
                     ft.departure_time, ft.arrival_time, ft.orig_airport_id, ft.dest_airport_id,
                     ft.date AS flight_date,
                     a.airline_name
              FROM flight_booking_t fb
              JOIN flight_info_t ft ON fb.flight_id = ft.flight_id
              JOIN airline_t a ON ft.airline_id = a.airline_id
              JOIN user_detail_t ud ON fb.user_id = ud.user_id
              WHERE fb.f_book_id = '$bookingId'";
    $result = mysqli_query($connection, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $flightDetails[] = $row;
    }
}
?>
  <main class="container">
    <h1 class="title">Have a good trip, <?= $flightDetails[0]['fst_name'] ?? 'Guest' ?>!</h1>
    <p class="reference">Booking Reference:
      <span>
        <?= count($bookingIds) > 1 ? implode(", ", $bookingIds) : $bookingIds[0] ?>
      </span>
    </p>
    <p class="description">
        Thank you for booking your travel with <span>Travooli</span>!<br>
        Below is a summary of your trip.
        A copy of your booking confirmation has been sent to your email address. You can always revisit this information in the My Trips section of our app. Safe travels!
    </p>
    <?php foreach ($flightDetails as $index => $flight): ?>
    <section class="layout flex-layout">
      <div class="ticket">
        <div class="flight-times">
          <div class="departure">
            <h2><?= date("h:i A", strtotime($flight['departure_time'])) ?></h2>
            <p><?= $flight['orig_airport_id'] ?></p>
          </div>
          <div class="flight-path">
            <div class="line"></div>
            <i class="fas fa-plane"></i>
            <div class="line"></div>
          </div> 
          <br>
          <br>
          <div class="arrival">
            <h2><?= date("h:i A", strtotime($flight['arrival_time'])) ?></h2>
            <p><?= $flight['dest_airport_id'] ?></p>
          </div>
        </div>
        <div class="boarding-pass">
          <div class="passenger-info">
            <img src="icon/avatar.svg" alt="Passenger" class="avatar">
            <div>
              <h3><?= $flight['fst_name'] . ' ' . $flight['lst_name'] ?></h3>
              <p>Boarding Pass <?= $flight['f_book_id'] ?></p>
            </div>
            <?php echo "<div class='ticket-class' style='margin-left: 100px;'>$classLabel ($classId)</div>"; ?>
          </div>
          <div class="flight-details">
            <div class="detail">
              <div class="icon"><img src="icon/calendar1.svg" alt="Calendar"></div>
              <div><p>Date</p><span><?= $flight['flight_date'] ?></span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/timmer.svg" alt="Clock"></div>
              <div><p>Flight time</p><span><?= date("H:i", strtotime($flight['departure_time'])) ?></span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/door.svg" alt="Gate"></div>
              <div><p>Gate</p><span>A12</span></div>
            </div>
            <div class="detail">
              <div class="icon"><img src="icon/seat.svg" alt="Seat"></div>
              <div><p>Seat</p><span>128</span></div>
            </div>
          </div>
          <div class="flight-code">
            <div class="flight-code-content">
            <?php echo "<h3>{$row['airline_name']}</h3>"; ?>
              <p><?= $flight['flight_id'] ?></p>
            </div>
            <img src="icon/barcode.svg" alt="Barcode" class="barcode">
          </div>
        </div>
      </div>
      <?php if ($index === 0): ?>
  <div class="price-breakdown">
    <h3>Price breakdown</h3>
    <div class="price-items">
      <div class="price-item"><span>Flight-price</span><span>RM 340</span></div>
      <div class="price-item"><span>Baggage fees</span><span>RM 20</span></div>
      <div class="price-item"><span>Multi-meal</span><span>RM 30</span></div>
      <div class="price-item"><span>Taxes and Fees</span><span>RM 121</span></div>
      <div class="price-total"><span>Amount Paid</span><span>RM 491</span></div>
    </div>
  </div>
  <?php endif; ?>
    </section>
    <?php endforeach; ?>
    <section class="action-buttons">
      <div class="share-btn"><img src="icon/share.svg" alt="btn"></div>
      <button class="download-btn">Download</button>
      <button class="home-btn" onclick="window.location.href='index.php'">Back to Homepage</button>
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
</body>
</html>