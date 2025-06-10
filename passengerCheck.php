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
    <?php include 'userHeader.php'; ?>
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
                                <span class="passenger-name">James Doe</span>
                                <span class="passenger-type">Adult / Male / Malaysia</span>
                            </div>
                            <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
                        </div>
                         <div class="passenger-item">
                            <input type="checkbox" checked>
                            <!-- Edit icon placeholder -->
                            <div class="passenger-details">
                                <span class="passenger-name">Peter Doe</span>
                                <span class="passenger-type">Adult / Male / Malaysia</span>
                            </div>
                            <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
                        </div>
                         <div class="passenger-item">
                            <input type="checkbox">
                            <div class="passenger-details">
                                <span class="passenger-name">John Pork</span>
                                <span class="passenger-type">Child / Male / Malaysia</span>
                            </div>
                            <button class="edit-passenger-btn"><i class="fa-solid fa-user-pen"></i></button>
                        </div>
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
                        <div class="trip-segment">
                            <div class="segment-header">
                                 <button class="depart-btn">Depart</button>
                                <span class="route">Kuala Lumpur - Penang</span>
                            </div>
                            <div class="passenger-addon-item">
                                <div class="addons-row">
                                <div class="passenger-name">James Doe</div>
                                <div class="addon-details">
                                    <span class="addon-type">Meal Add-on</span>
                                    <span class="addon-value">Multi-meal</span>
                                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                            <div class="addon-details">
                                <span class="addon-type">Additional Baggage</span>
                                <span class="addon-value">1piece, 25kg</span>
                                <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                            </div>
                        </div>
                    </div>
                            <div class="passenger-addon-item">
                                <div class="addons-row">
                                <div class="passenger-name">Peter Doe</div>
                                <div class="addon-details">
                                    <span class="addon-type">Meal Add-on</span>
                                    <span class="addon-value">Multi-meal</span>
                                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                                <div class="addon-details">
                                    <span class="addon-type">Additional Baggage</span>
                                    <span class="addon-value">1piece, 25kg</span>
                                    <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                        </div>
                    </div>
                        <div class="trip-segment">
                            <div class="segment-header">
                                <button class="return-btn">Return</button>
                                <span class="route">Penang - Kuala Lumpur </span>
                            </div>
                            <div class="passenger-addon-item">
                                <div class="addons-row">
                                <div class="passenger-name">James Doe</div>
                                    <div class="addon-details">
                                    <span class="addon-type">Meal Add-on</span>
                                    <span class="addon-value">Multi-meal</span>
                                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                                <div class="addon-details">
                                    <span class="addon-type">Additional Baggage</span>
                                    <span class="addon-value">1piece, 25kg</span>
                                    <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                            </div>
                        </div>
                    </div>
                            <div class="passenger-addon-item">
                                <div class="addons-row">
                                <div class="passenger-name">Peter Doe</div>
                                    <div class="addon-details">
                                    <span class="addon-type">Meal Add-on</span>
                                    <span class="addon-value">Multi-meal</span>
                                    <button class="edit-meals-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                                <div class="addon-details">
                                    <span class="addon-type">Additional Baggage</span>
                                    <span class="addon-value">1piece, 25kg</span>
                                    <button class="edit-baggage-btn"><i class="fa-solid fa-user-pen"></i></button>
                                </div>
                                 </div>
                                </div>       
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
                            <span class="item-name">Tickets (2 Adults, 1 Child)</span>
                            <span class="item-price">$340</span>
                        </div>
                        <div class="price-item">
                            <span class="item-name">Baggage Fees</span>
                            <span class="item-price">$20</span>
                        </div>
                         <div class="price-item">
                            <span class="item-name">Multi-meal</span>
                            <span class="item-price">$30</span>
                        </div>
                         <div class="price-item">
                            <span class="item-name">Taxes & Fees</span>
                            <span class="item-price">$121</span>
                        </div>
                         <div class="price-item">
                            <span class="item-name">Discount</span>
                            <span class="item-price">$0</span>
                        </div>
                    </div>
                    <hr>
                    <div class="total-price">
                        <span class="total-text">Total </span>
                        <span class="total-value">$491</span>
                    </div>
                </div>
                 <div class="button-row">
                     <button class="select-seats-btn">Select seats</button>
                 </div>
            </div>
        </div>
</div>
<!-- Popup Overlay -->
<div class="popup-bg hidden" id="popup-overlay">
  <div class="popup" id="popup-body"></div>
</div>
</body>
<script src="js/popupAddFlight.js"></script>
<script src="js/bag_popup.js"></script>
<script src="js/meal_popup.js"></script>
</html>

