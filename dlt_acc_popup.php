<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account Confirmation</title>
    <link rel="stylesheet" href="css/dlt_acc_popup.css">
</head>
<body>
    <!-- Modal Overlay -->
    <div class="modal-overlay" id="deleteAccountModal">
        <!-- Modal Dialog -->
        <div class="modal-dialog">
            <!-- Title Row -->
            <div class="title-row">
                <h2 class="modal-title">Are you sure to delete this user?</h2>
            </div>
            
            <!-- Description -->
            <div class="modal-description">
                Lorem ipsum odor amet, consectetuer adipiscing elit. Porttitor eget quam dui neque aenean. Facilisis feugiat conubia bibendum lobortis nunc. Mi nibh cubilia habitant dignissim curae.
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <div class="button-row">
                    <button class="btn btn-secondary" onclick="closeModal()">Back</button>
                    <button class="btn btn-primary btn-danger" onclick="confirmDelete()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/dlt_acc_popup.js"></script>
</body>
</html>
