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
include 'userHeader.php';
require_once 'connection.php'; 
$isLoggedIn = isset($_SESSION['user_id']);
$loggedUser = null;

if ($isLoggedIn) {
    include 'connection.php';
    $userId = $_SESSION['user_id'];
    $query = "SELECT fst_name, lst_name, gender, country FROM user_detail_t WHERE user_id = '$userId'";
    $result = mysqli_query($connection, $query);
    $loggedUser = mysqli_fetch_assoc($result);
}
$departFlightId = $_GET['depart'] ?? null;
$returnFlightId = $_GET['return'] ?? null;
$flightId = $_GET['flightId'] ?? null; // fallback for one-way

function getFlightRoute($connection, $flightId) {
    $sql = "
        SELECT 
            o.city_full AS origin_city, 
            d.city_full AS destination_city
        FROM flight_info_t f
        JOIN airport_t o ON f.orig_airport_id = o.airport_short
        JOIN airport_t d ON f.dest_airport_id = d.airport_short
        WHERE f.flight_id = ?
    ";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $flightId);
    $stmt->execute();
    $stmt->bind_result($originCity, $destinationCity);

    if ($stmt->fetch()) {
        return ['origin' => $originCity, 'destination' => $destinationCity];
    }

    return null;
}


$departInfo = $departFlightId ? getFlightRoute($connection, $departFlightId) : ($flightId ? getFlightRoute($connection, $flightId) : null);
$returnInfo = $returnFlightId ? getFlightRoute($connection, $returnFlightId) : null;
?>

    </header>
    <div class="main-content">
        <div class="booking-layout">
            <div class="travel-info-container">
                <div class="passenger-info-section">
                    <h2>Who's Travelling ?</h2>
                    <strong>Passengers</strong>
                    <div class="info-box">
                        <p class="info-text">
                            Names must match ID Please make sure that you enter the name exactly as it appears on the ID that will be used when checking in<br><br>
                            ID validity requirements To ensure your trip goes smoothly, please make sure that the passenger's travel document is valid on the date the trip ends
                        </p>
                    </div>
                    <div class="passenger-list">
                        <div class="passenger-item">
                            <input type="checkbox" checked>
                            <!-- Edit icon placeholder -->
                            <div class="passenger-details">
                            <span class="passenger-name">
                                 <?php echo isset($_SESSION['fst_name']) ? htmlspecialchars($_SESSION['fst_name']) : 'Guest'; ?>
                            </span>
                                <span class="passenger-type">Adult / Male / Malaysia</span>
                            </div>
                            <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
                        </div>
                        <!-- New passengers will be inserted here -->
                        <div id="passenger-list"></div>
                </div>
                    <button class="add-passenger-btn">Add Passengers</button>   
</div>
                <div class="additional-info-section">
                     <h2>Additional Information</h2>
                     <div class="info-box">
                        <p class="info-text">
                           Meal Add-on Choose from meal options based on your preferences, whether it's no meal, a single meal, or multiple meals for longer flights.<br><br>
                           Additional Baggage Select your baggage options to manage weight and quantity, with one free carry-on and personal item. Additional baggage can be added for a fee.
                        </p>
                    </div>
                    <div class="trip-details">
                        <div class="trip-segment" data-segment="depart">
                            <div class="segment-header">
                                 <button class="depart-btn">Depart</button>
                                 <span class="route">
                                    <?php 
                                        if ($departInfo) {
                                            echo htmlspecialchars($departInfo['origin'] . ' - ' . $departInfo['destination']);
                                        } else {
                                            echo "Depart flight unavailable";
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="passenger-addon-item">
                                <div class="addons-row">
                                <div class="passenger-name">
                                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                                </div>
                                <div class="addon-details">
                                    <span class="addon-type">Meal Add-on</span>
                                    <span class="addon-value" data-price="30">Multi-meal</span>
                                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                            <div class="addon-details">
                                <span class="addon-type">Additional Baggage</span>
                                <span class="addon-value" data-price="20">1piece, 25kg</span>
                                <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                            </div>
                        </div>
                    </div>
                <div class="add-on-container">
                    <!-- new passenger add-on placeholder -->
                </div>
                <?php if (isset($_GET['return']) && !empty($_GET['return'])): ?>
    <div class="trip-segment" data-segment="return">
        <div class="segment-header">
            <button class="return-btn">Return</button>
            <span class="route">
                <?php 
                if ($returnInfo) {
                    echo htmlspecialchars($returnInfo['origin'] . ' - ' . $returnInfo['destination']);
                } else {
                    echo "Return flight unavailable";
                }
                ?>
            </span>
        </div>
        <div class="passenger-addon-item">
            <div class="addons-row">
                <div class="passenger-name">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                </div>
                <div class="addon-details">
                    <span class="addon-type">Meal Add-on</span>
                    <span class="addon-value" data-price="30">Multi-meal</span>
                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                </div>
                <div class="addon-details">
                    <span class="addon-type">Additional Baggage</span>
                    <span class="addon-value" data-price="20">1piece, 25kg</span>
                    <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <div class="add-on-container">
                 <!-- new passenger add-on placeholder -->
        </div>
            <!-- Price and Button Section -->
            <div class="price-and-button-container">
            <div class="price-details-section">
                <h2>Price Details</h2>
                <div class="price-items">
                <div class="price-item">
                    <span class="item-name" id="ticket-count-label">Tickets (1 Adult)</span>
                    <span class="item-price" id="flight-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span class="item-name">Baggage Fees</span>
                    <span class="baggage-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span class="item-name">Multi-meal</span>
                    <span class="meal-price">RM 0</span>
                </div>
                <div class="price-item">
                    <span class="item-name">Taxes & Fees</span>
                    <span class="item-price" id="tax-fees">RM 121</span>
                </div>
                <div class="price-item">
                    <span class="item-name">Discount</span>
                    <span class="item-price">RM 0</span>
                </div>
                </div>
                <hr>
                <div class="total-price">
                <span class="total-text">Total </span>
                <span class="total-value" id="totalText">RM 0</span>
                </div>
            </div>

            <!-- ✅ Updated button for JS -->
            <div class="button-row">
                <button class="select-seats-btn">Select Seats</button>
            </div>

            <!-- Required hidden fields -->
            <input type="hidden" id="depart_flight_id" value="<?= htmlspecialchars($_GET['depart'] ?? '') ?>">
            <input type="hidden" id="return_flight_id" value="<?= htmlspecialchars($_GET['return'] ?? '') ?>">
            <input type="hidden" id="flight_id" value="<?= htmlspecialchars($_GET['flightId'] ?? '') ?>">
            <input type="hidden" id="seat_class_field" value="<?= htmlspecialchars($_GET['class_id'] ?? 'PE') ?>">


            </div>

</div>
<!-- Popup Overlay -->
<div class="popup-bg hidden" id="popup-overlay">
  <div class="popup" id="popup-body"></div>
</div>
</body>
<script src="js/popupAddFlight.js"></script>
<script src="js/passenger.js"></script>
</html>

