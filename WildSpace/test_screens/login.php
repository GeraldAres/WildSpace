<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Log In</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="nav-link">Home</a>
                <a href="#" class="nav-link">About Us</a>
            </div>
            
            <div class="logo">
                <h1 class="logo-text">WildSpace</h1>
            </div>
            
            <div class="nav-right">
                <a href="#" class="nav-link">Reservation</a>
                <button class="cta-button nav-cta">Contact Us</button>
            </div>
        </div>
    </nav>

    <!-- Main Auth Section -->
    <section class="auth-section login-section">
        <div class="auth-left">
            <div class="auth-form-container">
                <h1 class="auth-title">Your <span class="auth-title-bold">productive</span> starts with us</h1>
                <p class="auth-subtitle">Log in</p>
<form class="auth-form" id="loginForm" action="../actions/login_action.php" method="POST">
    <div class="form-group">
        <input 
            type="email" 
            name="email"
            class="form-input" 
            placeholder="Educational Email:" 
            required
        >
    </div>

    <div class="form-group">
        <input 
            type="password" 
            name="password"
            class="form-input" 
            placeholder="Password:" 
            required
        >
    </div>

    <button type="submit" name="login" class="auth-submit-button">Log In</button>
</form>

                <div class="auth-footer">
                    <p class="auth-footer-text">Don't have an account? <a href="register.php" class="auth-link">Sign-up now</a></p>
                    
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">f</a>
                        <a href="#" class="social-icon" title="LinkedIn">in</a>
                        <a href="#" class="social-icon" title="Instagram">📷</a>
                        <a href="#" class="social-icon" title="Email">✉</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-image-container" style="width: 650px; height: 650px; overflow: visible;">
                <img 
                    src="../assets/images/person1.png" 
                    alt="Person Image" 
                    class="auth-image"
                    style="width: 600px; max-width: none; height: auto;"
                >
            </div>

        </div>
    </section>

    <script src="../assets/js/auth.js"></script>
</body>
</html>