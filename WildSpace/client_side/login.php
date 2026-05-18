<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Log In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <div class="login-card" id="loginCard">
            <div class="login-header">
                <h1 class="auth-title">Your <span>productivity</span> starts with us</h1>
                <p class="auth-subtitle">Log in</p>
            </div>

            <form class="auth-form" id="loginForm" action="../actions/login_action.php" method="POST">

                <?php if (isset($_SESSION['login_success'])) { ?>
                    <div class="auth-message auth-message--success" id="loginMessage">
                        <?php 
                            echo htmlspecialchars($_SESSION['login_success']); 
                            unset($_SESSION['login_success']);
                        ?>
                    </div>
                <?php } ?>

                <?php if (isset($_SESSION['login_error'])) { ?>
                    <div class="auth-message auth-message--error" id="loginMessage">
                        <?php 
                            echo htmlspecialchars($_SESSION['login_error']); 
                            unset($_SESSION['login_error']);
                        ?>
                    </div>
                <?php } ?>

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

    <script>
        setTimeout(() => {
            const message = document.getElementById("loginMessage");
            if (message) {
                message.style.display = "none";
            }
        }, 3000);
    </script>

    <script src="../assets/js/auth.js"></script>
</body>
</html>