<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Add-on Popup</title>
    <link rel="stylesheet" href="css/meal_popup.css">
</head>
<body>
<div class="popup"> <div class="popup-close"> <img src="icon/Close_square_light.svg" alt="Close" width="24" height="24">
        </div>
        <div class="popup-container">
            <div class="popup-title">Meal Add-on</div>
            
            <div class="meal-options">
                <div class="meal-option selected" data-meal="no-meal" data-price="0" data-meal-id="M01"> 
                    <div class="meal-name">No meal</div>
                    <div class="popup-meal-price free">Free</div>
                </div>
                <div class="meal-option" data-meal="single-meal" data-price="15" data-meal-id="ML01"> 
                    <div class="meal-name">Single meal</div>
                    <div class="popup-meal-price">RM 15</div>
                </div>
                <div class="meal-option" data-meal="multi-meal" data-price="30" data-meal-id="ML02"> 
                    <div class="meal-name">Multi-meal</div>
                    <div class="popup-meal-price">RM 30</div>
                </div>
            </div>
            <div class="popup-divider"></div>
            
            <div class="meal-content">
                <div class="popup-note">
                    <p>Passengers can choose from a variety of meal options based on their preferences and flight duration. A complimentary light meal is available for short flights, or passengers can opt for a more satisfying meal or multiple meals on longer journeys to ensure comfort and convenience throughout the flight. Each meal option provides flexibility to suit individual needs, whether it's a quick snack or a full dining experience.</p>
                </div>
            </div>
            
            <div class="bottom-section">
                <div class="meal-image">
                    <img src="background/meal.png" alt="Meal" />
                </div>
                <div class="popup-actions">
                    <button class="popup-save-btn" type="submit">Save</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>