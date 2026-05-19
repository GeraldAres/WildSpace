<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="landingPage.php" class="nav-link">About Us</a>
        </div>

        <div class="logo">
            <h1 class="logo-text">WildSpace</h1>
        </div>

        <div class="nav-right">
            <a href="book.php" class="nav-link">Reservation</a>
            <button class="cta-button" onclick="location.href='contact.php'">Contact Us</button>
        </div>
    </div>
</nav>

<img src="../assets/images/bg_login.jpg" alt="Background Image" class="background-image">

<main class="auth-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h1 class="auth-title">Reset <span>Password</span></h1>
            <p class="auth-subtitle">Enter your email and new password</p>
        </div>

        <form class="auth-form" action="../actions/forgot_password_action.php" method="POST">

            <?php if (isset($_SESSION['forgot_error'])) { ?>
                <div class="auth-message auth-message--error">
                    <?php 
                        echo htmlspecialchars($_SESSION['forgot_error']); 
                        unset($_SESSION['forgot_error']);
                    ?>
                </div>
            <?php } ?>

            <?php if (isset($_SESSION['forgot_success'])) { ?>
                <div class="auth-message auth-message--success">
                    <?php 
                        echo htmlspecialchars($_SESSION['forgot_success']); 
                        unset($_SESSION['forgot_success']);
                    ?>
                </div>
            <?php } ?>

            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Educational Email:" required>
            </div>

            <div class="form-group">
                <input type="password" name="new_password" class="form-input" placeholder="New Password:" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" class="form-input" placeholder="Confirm New Password:" required>
            </div>

            <button type="submit" name="reset_password" class="auth-submit-button">
                Reset Password
            </button>
        </form>

        <div class="auth-footer">
            <p class="footer-text">
                Remembered your password? <a href="login.php">Log in</a>
            </p>
        </div>
    </div>
</main>

</body>
</html>