<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signIn.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$messageType = '';
$isNewUser = false;

// Check if this is a new user (first time visiting profile)
if (!isset($_SESSION['profile_visited'])) {
    $isNewUser = true;
    $_SESSION['profile_visited'] = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = trim($_POST['fst_name']);
    $lastName = trim($_POST['lst_name']);
    $email = trim($_POST['email_address']);
    $phone = trim($_POST['phone_no']);
    $country = trim($_POST['country']);
    
    // Validate input
    if (empty($firstName) || empty($lastName)) {
        $message = "First name and last name are required.";
        $messageType = "error";
    } elseif (empty($phone)) {
        $message = "Please provide your phone number to complete your profile.";
        $messageType = "error";
    } else {
        // Update user data (excluding email_address)
        $updateQuery = "UPDATE user_detail_t SET fst_name = ?, lst_name = ?, phone_no = ?, country = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($connection, $updateQuery);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $phone, $country, $userId);
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "Profile updated successfully!";
                $messageType = "success";
                // Mark profile as complete
                $_SESSION['profile_complete'] = true;
            } else {
                $message = "Error updating profile. Please try again.";
                $messageType = "error";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch current user data
$query = "SELECT user_id, fst_name, lst_name, email_address, phone_no, country FROM user_detail_t WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
    } else {
        header("Location: signIn.php");
        exit();
    }
    
    mysqli_stmt_close($stmt);
}

// Check if profile is complete (has phone number)
$profileComplete = !empty($userData['phone_no']);
$profileContainerClass = $profileComplete ? 'profile-complete' : 'profile-incomplete';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Travooli</title>
    <link rel="stylesheet" href="css/userHeader.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/sign.css">
</head>
<body>

<?php include 'userHeader.php'; ?>

<main class="profile-main">
    <div class="profile-container <?= $profileContainerClass ?>">
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="https://i.pinimg.com/736x/fe/ca/35/feca353a62bc5974bc699ec41b9cebcc.jpg" alt="Profile Picture" class="avatar-img">
            </div>
            <div class="profile-title">
                <h1>My Profile</h1>
                <p>Manage your personal information</p>
                <?php if ($message): ?>
                    <div id="profile-message" class="profile-message" data-type="<?= $messageType ?>" style="display: none;">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($isNewUser): ?>
                    <div id="welcome-message" class="welcome-message" style="display: none;">
                        Welcome to Travooli! Please complete your profile information below.
                    </div>
                <?php endif; ?>
                

            </div>
        </div>

        <form class="profile-form" method="POST" action="">
            <div class="form-section">
                <h2>Personal Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fst_name">First Name *</label>
                        <input type="text" id="fst_name" name="fst_name" value="<?= htmlspecialchars($userData['fst_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lst_name">Last Name *</label>
                        <input type="text" id="lst_name" name="lst_name" value="<?= htmlspecialchars($userData['lst_name']) ?>" required>
                    </div>
                    <div class="form-group readonly">
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" value="<?= htmlspecialchars($userData['user_id']) ?>" readonly>
                    </div>
                </div>

                <div class="form-row full-width">
                    <div class="form-group readonly">
                        <label for="email_address">Email Address</label>
                        <input type="email" id="email_address" name="email_address" value="<?= htmlspecialchars($userData['email_address']) ?>" readonly>
                    </div>
                </div>

                <div class="form-row two-columns">
                    <div class="form-group required">
                        <label for="phone_no">Phone Number</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($userData['phone_no']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country">
                            <option value="">Select Country</option>
                            <option value="Malaysia" <?= $userData['country'] == 'Malaysia' ? 'selected' : '' ?>>Malaysia</option>
                            <option value="Singapore" <?= $userData['country'] == 'Singapore' ? 'selected' : '' ?>>Singapore</option>
                            <option value="Thailand" <?= $userData['country'] == 'Thailand' ? 'selected' : '' ?>>Thailand</option>
                            <option value="Indonesia" <?= $userData['country'] == 'Indonesia' ? 'selected' : '' ?>>Indonesia</option>
                            <option value="Philippines" <?= $userData['country'] == 'Philippines' ? 'selected' : '' ?>>Philippines</option>
                            <option value="Other" <?= !in_array($userData['country'], ['Malaysia', 'Singapore', 'Thailand', 'Indonesia', 'Philippines']) && !empty($userData['country']) ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <?php if ($profileComplete): ?>
                    <button type="button" class="btn-secondary" onclick="window.history.back()">Back</button>
                <?php endif; ?>
                <button type="submit" class="btn-primary">
                    <?= $profileComplete ? 'Update Profile' : 'Complete Profile' ?>
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Slide Message Animation Script -->
<script src="js/sign.js"></script>
<script src="js/profile.js"></script>

<?php if ($isNewUser): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show welcome message for new users
    setTimeout(() => {
        showSuccessMessage('Welcome to Travooli! Please complete your profile information below.');
    }, 500);
});
</script>
<?php endif; ?>

</body>
</html>
