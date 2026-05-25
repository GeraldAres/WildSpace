<?php
session_start();
if (isset($_SESSION['forgot_success'])) {
    $_SESSION['popup_success'] = $_SESSION['forgot_success'];
    unset($_SESSION['forgot_success']);
}

if (isset($_SESSION['forgot_error'])) {
    $_SESSION['popup_error'] = $_SESSION['forgot_error'];
    unset($_SESSION['forgot_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>
<body>
    <?php include __DIR__ . '/popup.php'; ?>
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
            <div class="nav-actions">
                <a href="community.php" class="nav-link">Community</a>
                <button class="cta-button" onclick="location.href='contact.php'">Contact Us</button>
            </div>

            <div class="account-area">
                <div class="profile-dropdown">
                    <button type="button" class="profile-button">
                        <span class="profile-avatar">
                            <?php if ($isLoggedIn) { echo htmlspecialchars($profileInitial); } else { ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path fill-rule="evenodd" d="M8 9a5 5 0 0 0-5 5v.5A.5.5 0 0 0 3.5 15h9a.5.5 0 0 0 .5-.5V14a5 5 0 0 0-5-5z"/></svg>
                            <?php } ?>
                        </span>
                        <span class="profile-name"><?php echo htmlspecialchars($profileName); ?></span>
                    </button>
                    <div class="profile-menu">
                        <?php if ($isLoggedIn) { ?>
                            <a href="student_dashboard.php">Dashboard</a>
                            <a href="../actions/logout.php">Log Out</a>
                        <?php } else { ?>
                            <a href="login.php">Log In</a>
                            <a href="register.php">Register</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
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
<script>
function closePopup() {
    const popup = document.getElementById("systemPopup");
    if (popup) {
        popup.classList.remove("active");
        popup.style.display = "none";
    }
}
</script>
</body>
</html>