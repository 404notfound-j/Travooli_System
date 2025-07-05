<?php
session_start();
include 'connection.php';

$loginError = "";

// Sign-In Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnLogin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user is in admin_detail_t table (admin login)
    $queryAdmin = "SELECT * FROM admin_detail_t WHERE email_address = '$email' AND password = '$password'";
    $resultAdmin = mysqli_query($connection, $queryAdmin);

    // Check if user is in user_detail_t table (regular user login)
    $queryUser = "SELECT * FROM user_detail_t WHERE email_address = '$email' AND password = '$password'";
    $resultUser = mysqli_query($connection, $queryUser);

    // Admin login check
    if ($row = mysqli_fetch_assoc($resultAdmin)) {
        $_SESSION['user_id'] = $row['admin_id'];  
        $_SESSION['username'] = $row['fst_name'] . ' ' . $row['lst_name'];   
        $_SESSION['role'] = 'admin';             
        header("Location: A_dashboard.php");

    // Regular user login check
    } elseif ($row = mysqli_fetch_assoc($resultUser)) {
        $_SESSION['user_id'] = $row['user_id']; 
        $_SESSION['username'] = $row['fst_name'] . ' ' . $row['lst_name'];     
        $_SESSION['role'] = 'user';               
        header("Location: U_dashboard.php");

    } else {
        $loginError = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Travooli</title>
    <link rel="stylesheet" href="css/sign.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,600,700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'userHeader.php';?>
    
    <div class="signin-bg">
        <div class="signin-container">
            <h2 class="signin-title">Sign In to Travooli</h2>
            <p class="signin-desc">Welcome back to Travooli! Sign in with your email address or phone number to continue where you left off and enjoy seamless access to all our features.</p>
            <form class="signin-form" method="post" action="">
                <?php if (!empty($loginError)): ?>
                    <p id="signin-error-message" style="color: red; font-size: 14px; margin-bottom: 15px;"> <?php echo $loginError; ?> </p>
                <?php endif; ?>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit" name="btnLogin" class="btn btn-primary">Sign In</button>
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
    
    <!-- Slide Message Animation Script -->
    <script src="js/sign.js"></script>
</body>
</html>

