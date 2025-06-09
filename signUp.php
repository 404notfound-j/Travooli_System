<?php
session_start();
include 'connection.php';

$signupError = $signupSuccess = "";

// Sign-Up Processing (Only for users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSignUp'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists in user_detail_t
    $checkEmailQuery = "SELECT * FROM user_detail_t WHERE email_address = '$email'";
    $checkEmailResult = mysqli_query($connection, $checkEmailQuery);

    // Also check if email exists in admin_detail_t (to prevent conflicts)
    $checkAdminEmailQuery = "SELECT * FROM admin_detail_t WHERE email_address = '$email'";
    $checkAdminEmailResult = mysqli_query($connection, $checkAdminEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0 || mysqli_num_rows($checkAdminEmailResult) > 0) {
        $signupError = "This email is already registered. Please use another.";
    } else {
        // Generate next user_id
        $getLastIdQuery = "SELECT user_id FROM user_detail_t WHERE user_id LIKE 'U%' ORDER BY CAST(SUBSTRING(user_id, 2) AS UNSIGNED) DESC LIMIT 1";
        $lastIdResult = mysqli_query($connection, $getLastIdQuery);
        
        if (mysqli_num_rows($lastIdResult) > 0) {
            $lastRow = mysqli_fetch_assoc($lastIdResult);
            $lastNumber = intval(substr($lastRow['user_id'], 1)); // Remove 'U' and convert to int
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1; // First user
        }
        
        $newUserId = 'U' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // Format as U001, U002, etc.
        
        $insertQuery = "INSERT INTO user_detail_t (user_id, fst_name, lst_name, email_address, password, country) VALUES ('$newUserId', '$firstName', '$lastName', '$email', '$password', 'Malaysia')";
        if (mysqli_query($connection, $insertQuery)) {
            $signupSuccess = "Sign-up successful. You can now log in.";
        } else {
            $signupError = "Error: " . mysqli_error($connection);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Travooli</title>
    <link rel="stylesheet" href="css/sign.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <?php include 'userHeader.php';?>
    </header>
    
    <div class="signin-bg">
        <div class="signin-container">
            <h2 class="signin-title">Sign up for Travooli</h2>
            <p class="signin-desc">Travooli is totally free to use. Sign up using your email address or phone number below to get started.</p>
            <form class="signin-form" method="post" action="">
                <?php if (!empty($signupError)): ?>
                    <p style="color: red; font-size: 14px; margin-bottom: 15px;"> <?php echo $signupError; ?> </p>
                <?php elseif (!empty($signupSuccess)): ?>
                    <p style="color: green; font-size: 14px; margin-bottom: 15px;"> <?php echo $signupSuccess; ?> </p>
                <?php endif; ?>
                <input type="email" id="email" name="email" placeholder="Email or phone number" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <div class="name-container">
                    <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                    <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the terms and conditions</label>
                </div>
                <button type="submit" name="btnSignUp" class="btn btn-primary">Create account</button>
            </form>
            <div class="signin-divider"><span>or</span></div>
            <div class="signin-socials">
                <button class="btn-social google" type="button">
                    <span class="icon">
                        <img src="icon/google.svg" alt="Google" width="18" height="18">
                    </span>
                    Continue with Google
                </button>
                <button class="btn-social apple" type="button">
                    <span class="icon">
                        <img src="icon/apple.svg" alt="Apple" width="18" height="18">
                    </span>
                    Continue with Apple
                </button>
                <button class="btn-social facebook" type="button">
                    <span class="icon">
                        <img src="icon/facebook.svg" alt="Facebook" width="18" height="18">
                    </span>
                    Continue with Facebook
                </button>
            </div>
        </div>
    </div>

    <?php include 'u_footer_1.php'; ?>
    <?php include 'u_footer_2.php'; ?>
</body>
</html>
