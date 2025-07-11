<?php
// adminEditUser.php: Admin can edit any user's profile by user_id from GET
include 'connection.php';

// Get user_id from GET
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die('User ID not specified.');
}
$userId = $_GET['user_id'];
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = trim($_POST['fst_name']);
    $lastName = trim($_POST['lst_name']);
    $phone = trim($_POST['phone_no']);
    $country = trim($_POST['country']);
    $gender = trim($_POST['gender']);
    $profilePicData = null;

    if (isset($_FILES['profile_pic'])) {
        $uploadedFile = $_FILES['profile_pic'];
        if ($uploadedFile['error'] == UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = $uploadedFile['type'];
            if (in_array($fileType, $allowedTypes)) {
                $maxSize = 5 * 1024 * 1024;
                if ($uploadedFile['size'] <= $maxSize) {
                    $profilePicData = file_get_contents($uploadedFile['tmp_name']);
                } else {
                    $message = "Profile picture must be smaller than 5MB.";
                    $messageType = "error";
                }
            } else {
                $message = "Only JPEG, PNG, and GIF images are allowed. Got: " . $fileType;
                $messageType = "error";
            }
        }
    }

    // Validate input
    if (empty($firstName) || empty($lastName)) {
        $message = "First name and last name are required.";
        $messageType = "error";
    } elseif (!preg_match('/^[A-Za-z]+$/', $firstName)) {
        $message = "First name can only contain letters (no numbers, symbols, or spaces).";
        $messageType = "error";
    } elseif (!preg_match('/^[A-Za-z]+$/', $lastName)) {
        $message = "Last name can only contain letters (no numbers, symbols, or spaces).";
        $messageType = "error";
    } elseif (empty($phone)) {
        $message = "Please provide a phone number.";
        $messageType = "error";
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $message = "Phone number can only contain numbers (no letters, symbols, or spaces).";
        $messageType = "error";
    } elseif (strlen($phone) < 8 || strlen($phone) > 15) {
        $message = "Phone number must be between 8-15 digits.";
        $messageType = "error";
    } else {
        if ($profilePicData !== null) {
            $escapedImageData = mysqli_real_escape_string($connection, $profilePicData);
            $escapedFirstName = mysqli_real_escape_string($connection, $firstName);
            $escapedLastName = mysqli_real_escape_string($connection, $lastName);
            $escapedPhone = mysqli_real_escape_string($connection, $phone);
            $escapedCountry = mysqli_real_escape_string($connection, $country);
            $escapedGender = mysqli_real_escape_string($connection, $gender);
            $escapedUserId = mysqli_real_escape_string($connection, $userId);
            $updateQuery = "UPDATE user_detail_t SET fst_name = '$escapedFirstName', lst_name = '$escapedLastName', phone_no = '$escapedPhone', country = '$escapedCountry', gender = '$escapedGender', profile_pic = '$escapedImageData' WHERE user_id = '$escapedUserId'";
            $result = mysqli_query($connection, $updateQuery);
            $stmt = null;
        } else {
            $updateQuery = "UPDATE user_detail_t SET fst_name = ?, lst_name = ?, phone_no = ?, country = ?, gender = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($connection, $updateQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $firstName, $lastName, $phone, $country, $gender, $userId);
            }
        }
        if ($stmt !== null) {
            if (mysqli_stmt_execute($stmt)) {
                $message = "Profile updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating profile. Please try again.";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        } else if (isset($result)) {
            if ($result) {
                $message = "Profile and profile picture updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating profile. Please try again.";
                $messageType = "error";
            }
        } else {
            $message = "Error updating profile. Please try again.";
            $messageType = "error";
        }
    }
}

// Fetch user data
$query = "SELECT user_id, fst_name, lst_name, email_address, phone_no, country, gender, profile_pic FROM user_detail_t WHERE user_id = ?";
$stmt = mysqli_prepare($connection, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
    } else {
        die('User not found.');
    }
    mysqli_stmt_close($stmt);
} else {
    die('Database error.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile (Admin) - Travooli</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/sign.css">
</head>
<body>
<div class="signin-bg">
<main class="profile-main">
    <div class="profile-container profile-admin-edit">
        <div class="profile-header">
            <div class="profile-avatar clickable-avatar" onclick="triggerFileUpload()" title="Click to change profile picture">
                <?php if (!empty($userData['profile_pic'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($userData['profile_pic']) ?>" alt="Profile Picture" class="avatar-img">
                <?php else: ?>
                    <img src="https://i.pinimg.com/736x/fe/ca/35/feca353a62bc5974bc699ec41b9cebcc.jpg" alt="Profile Picture" class="avatar-img">
                <?php endif; ?>
                <div class="avatar-overlay">
                    <div class="camera-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23 19C23 19.5304 22.7893 20.0391 22.4142 20.4142C22.0391 20.7893 21.5304 21 21 21H3C2.46957 21 1.96086 20.7893 1.58579 20.4142C1.21071 20.0391 1 19.5304 1 19V8C1 7.46957 1.21071 6.96086 1.58579 6.58579C1.96086 6.21071 2.46957 6 3 6H7L9 4H15L17 6H21C21.5304 6 22.0391 6.21071 22.4142 6.58579C22.7893 6.96086 23 7.46957 23 8V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 17C14.2091 17 16 15.2091 16 13C16 10.7909 14.2091 9 12 9C9.79086 9 8 10.7909 8 13C8 15.2091 9.79086 17 12 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="avatar-text">Change Photo</span>
                </div>
            </div>
            <div class="profile-title">
                <h1>Edit User Profile (Admin)</h1>
                <p>Manage user information for user ID: <?= htmlspecialchars($userId) ?></p>
                <?php if ($message): ?>
                    <div id="profile-message" class="profile-message" data-type="<?= $messageType ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form class="profile-form" method="POST" action="" enctype="multipart/form-data">
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display: none;">
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
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= (isset($userData['gender']) && $userData['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (isset($userData['gender']) && $userData['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
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
                        <label for="phone_no">Phone Number *</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($userData['phone_no']) ?>" required>
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
                <a href="U_Manage.php" class="btn-secondary">Back</a>
                <button type="submit" class="btn-primary">Update User Profile</button>
            </div>
        </form>
    </div>
</main>
</div>
<script>
function triggerFileUpload() {
    document.getElementById('profile_pic').click();
}
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_pic');
    const form = document.querySelector('.profile-form');
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                alert('Only JPEG, PNG, and GIF images are allowed.');
                e.target.value = '';
                return;
            }
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Profile picture must be smaller than 5MB.');
                e.target.value = '';
                return;
            }
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
});
</script>
</body>
</html> 