<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=devic e-width, initial-scale=1.0">
    <title>Passenger Information Popup</title>
    <link rel="stylesheet" href="css/pass_info_popup.css">
</head>
<body>
<div class="popup-bg">
    <div class="popup">
        <div class="popup-close" onclick="this.closest('.popup-bg').style.display='none'">
            <img src="../icon/Close_square_light.svg" alt="Close" width="24" height="24">
        </div>
        <div class="popup-container">
            <div class="popup-title">Passenger Information</div>
            <form>
                <div class="popup-form">
                    <!-- First row: First name, Last name -->
                    <div class="popup-form-row">
                        <div class="popup-form-col">
                            <label class="popup-label" for="first_name">First name*</label>
                            <input class="popup-input" type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="popup-form-col">
                            <label class="popup-label" for="last_name">Last name*</label>
                            <input class="popup-input" type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="popup-divider"></div>
                    <!-- Second row: Gender, Date of birth, Country -->
                    <div class="popup-form-row">
                        <div class="popup-form-col-gender">
                            <label class="popup-label" for="gender">Gender</label>
                            <select class="popup-select" id="gender" name="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="popup-form-col-dob">
                            <label class="popup-label" for="dob">Date of birth*</label>
                            <input class="popup-input" type="date" id="dob" name="dob" required>
                            <div class="popup-helper">MM/DD/YY</div>
                        </div>
                        <div class="popup-form-col-country">
                            <label class="popup-label" for="country">Country</label>
                            <select class="popup-select" id="country" name="country">
                                <option value="">Select</option>
                                <option value="us">United States</option>
                                <option value="uk">United Kingdom</option>
                                <option value="ca">Canada</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="popup-note">
                    <ul>
                        <li>Please enter the name exactly as it appears on your travel documents for check-in. If the name is incorrect, you may not be able to board your flight and a cancellation fee will be charged.</li>
                        <li>To ensure your trip goes smoothly, please make sure that the passenger's travel document is valid on the date the trip ends.</li>
                    </ul>
                </div>
                <div class="popup-actions">
                    <button class="popup-save-btn" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
    <div id="form-warning" class="popup-warning" style="display:none;">
    Please fill in all required fields.
</div>
</div>
</body>
<script src="js/warning.js"></script>
</html>
