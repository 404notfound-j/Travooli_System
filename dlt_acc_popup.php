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
                <h2 class="modal-title">Delete User Confirmation</h2>
            </div>
            
            <!-- Description -->
            <div class="modal-description">
                Are you sure you want to delete the following user?<br><br>
                <strong>User ID:</strong> <span id="del-user-id"></span><br>
                <strong>First Name:</strong> <span id="del-fst-name"></span><br>
                <strong>Last Name:</strong> <span id="del-lst-name"></span><br><br>
                This action will permanently delete all data for this user. This cannot be undone.
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
