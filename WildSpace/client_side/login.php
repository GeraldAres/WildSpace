<?php
session_start();

if (isset($_SESSION['login_success'])) {
    $_SESSION['popup_success'] = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

if (isset($_SESSION['login_error'])) {
    $_SESSION['popup_error'] = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

$isLoggedIn = isset($_SESSION['user_id'], $_SESSION['role']);
$profileName = '';
$profileInitial = 'P';
if ($isLoggedIn) {
    $profileName = trim(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));
    if ($profileName === '') {
        $profileName = $_SESSION['email'] ?? 'Profile';
    }
    $profileInitial = strtoupper(substr($profileName, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Log In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <div class="login-card" id="loginCard">
            <div class="login-header">
                <h1 class="auth-title">Your <span>productivity</span> starts with us</h1>
                <p class="auth-subtitle">Log in</p>
            </div>

            <form class="auth-form" id="loginForm" action="../actions/login_action.php" method="POST">

                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-input" placeholder="Educational Email:" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-input" placeholder="Password:" required>
                </div>

                <p class="footer-text" style="text-align: right; margin-top: -10px;">
                    <a href="forgot_password.php">Forgot Password?</a>
                </p>

                <button type="submit" name="login" class="auth-submit-button">Log In</button>
            </form>

            <div class="auth-footer">
                <p class="footer-text">Don't have an account? <a href="register.php">Sign-up now</a></p>
                <div class="social-links">
                    <a href="https://facebook.com/..." class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://linkedin.com/..." class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://instagram.com/..." class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:support@wildspace.com" class="social-icon"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/auth.js"></script>
</body>
</html>