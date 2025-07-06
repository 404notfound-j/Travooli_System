<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Flight Booking</title>
    <link rel="stylesheet" href="css/dlt_acc_popup.css">
    <link rel="stylesheet" href="css/adminCancelFlight.css">
</head>
<body>
    <!-- Modal Overlay -->
    <div class="modal-overlay" id="cancelFlightModal">
        <!-- Modal Dialog -->
        <div class="modal-dialog">
            <!-- Title Row -->
            <div class="title-row">
                <h2 class="modal-title">Are you sure to cancel your room?</h2>
            </div>
            
            <!-- Description -->
            <div class="modal-description">
                Cancelling this flight booking will result in all passenger reservations being removed from the system. Depending on the airline's policy, this may incur cancellation fees. Please confirm that you want to proceed with cancelling this booking.
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <div class="button-row">
                    <button class="btn btn-secondary" onclick="closeModal()">Back</button>
                    <button class="btn btn-primary btn-danger" onclick="confirmCancelFlight()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/adminCancelFlight.js"></script>
</body>
</html> 