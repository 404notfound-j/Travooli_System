<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Travooli Flight History</title>
    <link rel="stylesheet" href="./css/FlightHistory.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  </head>
  <body>
    <?php include 'userHeader.php'; ?>
    <div id="app">
      <!-- Main Content Section -->
      <div class="main-container">
        <div class="booking-card">
          <div class="text-and-button-section">
            <div class="content-section">
              <p class="booking-message">
                You don't have any bookings or we can't access your bookings at this time. Please check back later or contact support if you need assistance with your bookings.
              </p>
            </div>
            <button class="search-button">Search Bookings</button>
          </div>
        </div>
      </div>
    </div>
    <?php include 'u_footer_1.php'; ?>
  </body>
</html>