<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Create Account</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <!-- Navigation Bar -->
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
                <button class="cta-button nav-cta" onclick="location.href='book.php'">Contact Us</button>
            </div>
        </div>
    </nav>

    <!-- Main Auth Section -->
    <section class="auth-section register-section">
        <div class="auth-left">
            <div class="auth-image-container" style="width: 650px; height: 650px; overflow: visible;">
                <img 
                    src="../assets/images/registerimg.png" 
                    alt="Group Image" 
                    class="auth-image"
                    style="width: 1100px; max-width: none; height: auto;"
                >
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-container">
                <h1 class="auth-title">Your <span class="auth-title-bold">productive</span> starts with us</h1>
                <p class="auth-subtitle">Create account</p>

                <form class="auth-form" id="registerForm" action="../actions/register_action.php" method="POST">

                    <div class="form-group">
                        <input 
                            type="text"
                            name="firstname"
                            class="form-input" 
                            placeholder="First Name:" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <input 
                            type="text"
                            name="lastname"
                            class="form-input" 
                            placeholder="Last Name:" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <select 
                            name="gender" 
                            class="form-input" 
                            required
                        >
                            <option value="" disabled selected>Gender:</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Prefer not to say">Prefer not to say</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input 
                            type="text"
                            name="mobile_number"
                            class="form-input" 
                            placeholder="Mobile Number:" 
                            maxlength="11"
                            required
                        >
                    </div>

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

                    <div class="form-group">
                        <input 
                            type="password"
                            name="confirm_password"
                            class="form-input" 
                            placeholder="Confirm Password:" 
                            required
                        >
                    </div>

                    <button type="submit" name="register" class="auth-submit-button">Create Account</button>
                </form>

                <div class="auth-footer">
                    <p class="auth-footer-text">Have an account? <a href="login.php" class="auth-link">Log-in</a></p>
                    
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">f</a>
                        <a href="#" class="social-icon" title="LinkedIn">in</a>
                        <a href="#" class="social-icon" title="Instagram">📷</a>
                        <a href="#" class="social-icon" title="Email">✉</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--<script src="../assets/js/auth.js"></script>-->     
</body>
</html>