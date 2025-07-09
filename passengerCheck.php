<?php
session_start();
// Ensure connection.php is included at the very top, before any database operations
require_once 'connection.php'; // Use require_once to prevent multiple inclusions

$isLoggedIn = isset($_SESSION['user_id']);
$loggedUser = null;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT fst_name, lst_name, gender, country FROM user_detail_t WHERE user_id = '$userId'";
    // Now $connection should be defined here
    $result = mysqli_query($connection, $query);
    if ($result) { // Always check if query was successful
        $loggedUser = mysqli_fetch_assoc($result);
    } else {
        error_log("Database query failed: " . mysqli_error($connection));
        // Handle error appropriately, e.g., set $loggedUser to default or show a message
        $loggedUser = null; // Or some default guest data
    }
}

// The rest of your HTML and PHP should follow without needing another include 'connection.php'

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli Passenger Info</title>
    <link rel="stylesheet" href="css/passenger.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header>
    <?php
// No need to include connection.php again here
include 'userHeader.php';
?>
    </header>
    <div class="main-content">
        <div class="booking-layout">
            <div class="travel-info-container">
                <div class="passenger-info-section">
                    <h2>Who's Travelling ?</h2>
                    <strong>Passengers</strong>
                    <div class="info-box">
                        <li class="info-text">
                            Names must match ID Please make sure that you enter the name exactly as it appears on the ID that will be used when checking in
                        </li>
                        <li class="info-text">
                            ID validity requirements To ensure your trip goes smoothly, please make sure that the passenger's travel document is valid on the date the trip ends
                        </li>
                    </div>
                    <div class="passenger-list" id="passenger-list-wrapper"> <div id="passenger-list"></div> </div>
                </div>
                <div class="additional-info-section">
                     <h2>Additional Information</h2>
                     <div class="info-box">
                        <li class="info-text">
                           Meal Add-on Choose from meal options based on your preferences, whether it's no meal, a single meal, or multiple meals for longer flights.
                        </li>
                        <li class="info-text">
                           Additional Baggage Select your baggage options to manage weight and quantity, with one free carry-on and personal item. Additional baggage can be added for a fee.
                        </li>
                    </div>
                    <div class="trip-details">
                        <div class="trip-segment" data-segment="depart">
                            <div class="segment-header">
                                 <button class="depart-btn">Depart</button>
                                 <span class="route" id="depart-route-display">Loading route...</span>
                            </div>
                            <div class="add-on-container" id="depart-addon-container">
                                </div>
                        </div>
                        <div class="trip-segment" data-segment="return" style="display:none;">
                            <div class="segment-header">
                                <button class="return-btn">Return</button>
                                <span class="route" id="return-route-display">Loading route...</span>
                            </div>
                            <div class="add-on-container" id="return-addon-container">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="price-and-button-container">
                <div class="price-details-section">
                    <h2>Price Details</h2>
                    <div class="price-items">
                        <div class="price-item">
                        <span class="item-name" id="ticket-count-label">Tickets (1 Adult)</span>
                            <span class="item-price" id="flight-price">RM 0.00</span>
                        </div>
                        <div class="price-item">
                            <span class="item-name">Baggage Fees</span>
                            <span class="baggage-price">RM 0.00</span>
                        </div>
                         <div class="price-item">
                            <span class="item-name">Meal Add-on</span>
                            <span class="meal-price">RM 0.00</span>
                        </div>
                        <div class="price-item">
                            <span class="item-name">Taxes & Fees (6%)</span>
                            <span class="tax-price">RM 0.00</span>
                        </div>
                         <div class="price-item">
                            <span class="item-name">Discount</span>
                            <span class="item-price">RM 0.00</span>
                        </div>
                    </div>
                    <hr>
                    <div class="total-price">
                        <span class="total-text">Total </span>
                        <span class="total-value" id="totalText">RM 0.00</span>
                        </form>
                    </div>
                </div>
                 <div class="button-row">
                 <a href="#" class="select-seats-btn">Select seats</a>
                 </div>
            </div>
        </div>
</div>
<div class="popup-bg hidden" id="popup-overlay">
  <div class="popup" id="popup-body"></div>
</div>
</body>
<script>
  window.loggedUser = <?php echo json_encode($loggedUser); ?>;
</script>
<script src="js/passenger.js"></script>
<script src="js/bag_popup.js"></script>
<script src="js/meal_popup.js"></script>
<script src="js/popupAddFlight.js"></script> 
</html>