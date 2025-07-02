<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Travooli</title>
    <link rel="stylesheet" href="css/admin_profile.css">
</head>
<body>

<?php
// Start output buffering to capture content
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signIn.php");
    exit();
}

$adminId = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = trim($_POST['fst_name']);
    $lastName = trim($_POST['lst_name']);
    $email = trim($_POST['email_address']);
    $phone = trim($_POST['phone_no']);
    $country = trim($_POST['country']);
    $gender = trim($_POST['gender']);
    
    // Handle profile picture upload
    $profilePicData = null;
    
    if (isset($_FILES['profile_pic'])) {
        $uploadedFile = $_FILES['profile_pic'];
        
        if ($uploadedFile['error'] == UPLOAD_ERR_OK) {
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = $uploadedFile['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                // Validate file size (max 5MB)
                $maxSize = 5 * 1024 * 1024; // 5MB
                if ($uploadedFile['size'] <= $maxSize) {
                    // Read file content
                    $profilePicData = file_get_contents($uploadedFile['tmp_name']);
                } else {
                    $message = "Profile picture must be smaller than 5MB.";
                    $messageType = "error";
                }
            } else {
                $message = "Only JPEG, PNG, and GIF images are allowed.";
                $messageType = "error";
            }
        } else {
            $uploadError = $uploadedFile['error'];
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File too large (php.ini limit)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
                UPLOAD_ERR_PARTIAL => 'File partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
                UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
                UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
            ];
            $message = "Upload failed: " . ($errorMessages[$uploadError] ?? "Unknown error ($uploadError)");
            $messageType = "error";
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
        $message = "Phone number is required.";
        $messageType = "error";
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $message = "Phone number can only contain numbers (no letters, symbols, or spaces).";
        $messageType = "error";
    } elseif (strlen($phone) < 8 || strlen($phone) > 15) {
        $message = "Phone number must be between 8-15 digits.";
        $messageType = "error";
    } else {
        // Update admin data with or without profile picture
        if ($profilePicData !== null) {
            // Update with profile picture
            $escapedImageData = mysqli_real_escape_string($connection, $profilePicData);
            $escapedFirstName = mysqli_real_escape_string($connection, $firstName);
            $escapedLastName = mysqli_real_escape_string($connection, $lastName);
            $escapedPhone = mysqli_real_escape_string($connection, $phone);
            $escapedCountry = mysqli_real_escape_string($connection, $country);
            $escapedGender = mysqli_real_escape_string($connection, $gender);
            $escapedAdminId = mysqli_real_escape_string($connection, $adminId);
            
            $updateQuery = "UPDATE admin_detail_t SET fst_name = '$escapedFirstName', lst_name = '$escapedLastName', phone_no = '$escapedPhone', country = '$escapedCountry', gender = '$escapedGender', profile_pic = '$escapedImageData' WHERE admin_id = '$escapedAdminId'";
            
            $result = mysqli_query($connection, $updateQuery);
            $stmt = null;
        } else {
            // Update without profile picture
            $updateQuery = "UPDATE admin_detail_t SET fst_name = ?, lst_name = ?, phone_no = ?, country = ?, gender = ? WHERE admin_id = ?";
            $stmt = mysqli_prepare($connection, $updateQuery);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $firstName, $lastName, $phone, $country, $gender, $adminId);
            }
        }
        
        if ($stmt !== null) {
            // Handle prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $message = "Admin profile updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating profile. Please try again.";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        } else if (isset($result)) {
            // Handle direct query result
            if ($result) {
                if ($profilePicData !== null) {
                    $message = "Admin profile and profile picture updated successfully!";
                } else {
                    $message = "Admin profile updated successfully!";
                }
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

// Fetch current admin data including profile picture
$query = "SELECT admin_id, fst_name, lst_name, email_address, phone_no, country, gender, profile_pic FROM admin_detail_t WHERE admin_id = ?";
$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $adminId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $adminData = mysqli_fetch_assoc($result);
    } else {
        header("Location: signIn.php");
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    header("Location: signIn.php");
    exit();
}

// Generate admin initials for fallback
$initials = strtoupper(substr($adminData['fst_name'], 0, 1) . substr($adminData['lst_name'], 0, 1));
?>

<div class="signin-bg">
<main class="profile-main">
    <div class="profile-container profile-complete">
        <div class="profile-header">
            <div class="profile-avatar clickable-avatar" onclick="triggerFileUpload()" title="Click to change profile picture">
                <?php if (!empty($adminData['profile_pic'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($adminData['profile_pic']) ?>" alt="Profile Picture" class="avatar-img">
                <?php else: ?>
                    <div class="admin-avatar-large"><?= $initials ?></div>
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
                <h1>Admin Profile</h1>
                <p>Manage your administrator information</p>
                <?php if ($message): ?>
                    <div id="profile-message" class="profile-message" data-type="<?= $messageType ?>" style="display: none;">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <form class="profile-form" method="POST" action="" enctype="multipart/form-data">
            <!-- Hidden file input for profile picture -->
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display: none;">
            
            <div class="form-section">
                <h2>Administrator Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="fst_name">First Name *</label>
                        <input type="text" id="fst_name" name="fst_name" value="<?= htmlspecialchars($adminData['fst_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lst_name">Last Name *</label>
                        <input type="text" id="lst_name" name="lst_name" value="<?= htmlspecialchars($adminData['lst_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= (isset($adminData['gender']) && $adminData['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (isset($adminData['gender']) && $adminData['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-row full-width">
                    <div class="form-group readonly">
                        <label for="email_address">Email Address</label>
                        <input type="email" id="email_address" name="email_address" value="<?= htmlspecialchars($adminData['email_address']) ?>" readonly>
                    </div>
                </div>

                <div class="form-row two-columns">
                    <div class="form-group required">
                        <label for="phone_no">Phone Number *</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($adminData['phone_no']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country">
                            <option value="">Select Country</option>
                            <option value="Malaysia" <?= $adminData['country'] == 'Malaysia' ? 'selected' : '' ?>>Malaysia</option>
                            <option value="Singapore" <?= $adminData['country'] == 'Singapore' ? 'selected' : '' ?>>Singapore</option>
                            <option value="Thailand" <?= $adminData['country'] == 'Thailand' ? 'selected' : '' ?>>Thailand</option>
                            <option value="Indonesia" <?= $adminData['country'] == 'Indonesia' ? 'selected' : '' ?>>Indonesia</option>
                            <option value="Philippines" <?= $adminData['country'] == 'Philippines' ? 'selected' : '' ?>>Philippines</option>
                            <option value="Other" <?= !in_array($adminData['country'], ['Malaysia', 'Singapore', 'Thailand', 'Indonesia', 'Philippines']) && !empty($adminData['country']) ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.location.href='A_dashboard.php'">Back to Dashboard</button>
                <button type="submit" class="btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</main>
</div>

<?php
// Capture the content
$pageContent = ob_get_clean();

// Include the admin sidebar which will display the content
include 'adminSidebar.php';
?>

</body>
</html> 