<?php
// Get popup parameters from URL or session
$title = isset($_GET['title']) ? $_GET['title'] : 'Confirm Action';
$description = isset($_GET['description']) ? $_GET['description'] : 'Are you sure you want to proceed with this action?';
$cancelText = isset($_GET['cancelText']) ? $_GET['cancelText'] : 'Back';
$confirmText = isset($_GET['confirmText']) ? $_GET['confirmText'] : 'Confirm';
$confirmClass = isset($_GET['confirmClass']) ? $_GET['confirmClass'] : 'btn-danger';
$actionType = isset($_GET['actionType']) ? $_GET['actionType'] : 'generic';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <link rel="stylesheet" href="css/confirm_popup.css">
</head>
<body>
    <!-- Modal Overlay -->
    <div class="modal-overlay" id="confirmModal">
        <!-- Modal Dialog -->
        <div class="modal-dialog">
            <!-- Title Row -->
            <div class="title-row">
                <h2 class="modal-title"><?php echo htmlspecialchars($title); ?></h2>
            </div>
            
            <!-- Description -->
            <div class="modal-description">
                <?php echo htmlspecialchars($description); ?>
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <div class="button-row">
                    <button class="btn btn-secondary" onclick="closeModal()"><?php echo htmlspecialchars($cancelText); ?></button>
                    <button class="btn btn-primary <?php echo htmlspecialchars($confirmClass); ?>" onclick="confirmAction('<?php echo htmlspecialchars($actionType); ?>')"><?php echo htmlspecialchars($confirmText); ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/confirm_popup.js"></script>
</body>
</html> 