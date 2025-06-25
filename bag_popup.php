<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baggage Information Popup</title>
    <link rel="stylesheet" href="css/bag_popup.css">
</head>
<body>
<div class="popup-bg">
    <div class="popup">
        <div class="popup-close" onclick="this.closest('.popup-bg').style.display='none'">
            <img src="icon/Close_square_light.svg" alt="Close" width="24" height="24">
        </div>
        <div class="popup-container">
            <div class="popup-title">Baggage Information</div>
            
            <div class="bag-options">
                <div class="bag-option selected" data-bag="10kg" data-price="0">
                    <div class="bag-weight">10Kg</div>
                    <div class="bag-price free">Free</div>
                </div>
                <div class="bag-option" data-bag="25kg" data-price="20">
                    <div class="bag-weight">25 Kg</div>
                    <div class="bag-price">RM 20</div>
                </div>
                <div class="bag-option" data-bag="50kg" data-price="40">
                    <div class="bag-weight">50 Kg</div>
                    <div class="bag-price">RM 40</div>
                </div>
            </div>           
            <div class="popup-divider"></div>
            
            <div class="bag-quantity-section">
                <div class="bag-quantity-label">Bag quantity:</div>
                <div class="bag-quantity-controls">
                    <button class="quantity-btn minus" onclick="decrementQuantity()">−</button>
                    <span class="quantity-display">1</span>
                    <button class="quantity-btn plus" onclick="incrementQuantity()">+</button>
                </div>
            </div>
            
            <div class="bag-content">
                <div class="popup-note">
                    <p>Each passenger is allowed one carry-on bag and one personal item for free, along with a complimentary checked bag up to 10 Kg. Bags exceeding 10 Kg will incur additional charges.</p>
                    <p>Each airline has different regulations on special baggage (such as musical instruments, sports equipment, etc.). Therefore, for baggage other than regular backpacks and suitcases, we recommend checking the baggage regulations on the airline's website or contacting our customer support before travelling.</p>
                </div>
            </div>
            
            <div class="bottom-section">
                <div class="bag-image">
                    <img src="background/bag.png" alt="Baggage" />
                </div>
                <div class="popup-actions">
                    <button class="popup-save-btn" type="submit">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/bag_popup.js"></script>
</body>
</html>
