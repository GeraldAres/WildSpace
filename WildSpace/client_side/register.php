<?php
session_start();
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
    <title>WildSpace - Create Account</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>

<body>
<?php include __DIR__ . '/popup.php'; ?>
<!-- ================= NAVBAR ================= -->
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

<!-- ================= BACKGROUND ================= -->
<img src="../assets/images/bg_login.jpg" alt="Background" class="background-image">

<!-- ================= AUTH WRAPPER ================= -->
<main class="auth-wrapper">

    <div class="login-card" id="registerCard">

        <div class="login-header">
            <h1 class="auth-title">
                Your <span>productive</span> starts with us
            </h1>

            <p class="auth-subtitle">
                Create account
            </p>
        </div>

        <!-- ================= FORM ================= -->
        <form
            id="registerForm"
            class="auth-form"
            action="../actions/register_action.php"
            method="POST"
            novalidate
        >

            <!-- ROLE: native radios — each label is fully clickable -->
            <span class="role-label">Choose account type</span>

            <div class="role-selector" role="radiogroup" aria-label="Account type">

                <label class="role-box role-box--student">
                    <input
                        type="radio"
                        name="role"
                        id="roleStudent"
                        value="student"
                        class="role-radio"
                        checked
                    >
                    <span class="role-check" aria-hidden="true">
                        <i class="fas fa-check"></i>
                    </span>
                    <i class="fas fa-user-graduate role-icon" aria-hidden="true"></i>
                    <span class="role-name">Student</span>
                    <span class="role-desc">Book study spaces</span>
                </label>

                <label class="role-box role-box--admin">
                    <input
                        type="radio"
                        name="role"
                        id="roleAdmin"
                        value="admin"
                        class="role-radio"
                    >
                    <span class="role-check" aria-hidden="true">
                        <i class="fas fa-check"></i>
                    </span>
                    <i class="fas fa-user-shield role-icon" aria-hidden="true"></i>
                    <span class="role-name">Admin</span>
                    <span class="role-desc">Manage reservations</span>
                </label>

            </div>

                <div class="form-group">
                    <input type="text" name="firstname" class="form-input" placeholder="First Name:" required>
                </div>

            <!-- LAST NAME -->
            <div class="form-group">
                <input
                    type="text"
                    name="lastname"
                    class="form-input"
                    placeholder="Last Name:"
                    required
                >
            </div>

            <!-- GENDER -->
            <div class="form-group select-wrapper">
                <select name="gender" class="form-input" required>
                    <option value="" disabled selected>Gender:</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>

                <i class="fas fa-chevron-down select-icon"></i>
            </div>
            <!-- MOBILE -->
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

            <!-- EMAIL -->
            <div class="form-group">
                <input
                    type="email"
                    name="email"
                    class="form-input"
                    placeholder="Educational Email:"
                    required
                >
            </div>

            <!-- PASSWORD -->
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
                    <input type="password" name="confirm_password" class="form-input" placeholder="Confirm Password:" required>
                    <div id="registerMessage" class="auth-message" role="alert" aria-live="polite" hidden></div>
                </div>

                    <button type="submit" name="register" id="registerSubmit" class="auth-submit-button">
        Create Account
    </button>
            </form>

        <!-- ================= FOOTER ================= -->
        <div class="auth-footer">

            <p class="footer-text">
                Have an account?
                <a href="login.php">Log-in</a>
            </p>

            <div class="social-links">

                <a href="#" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <a href="#" class="social-icon">
                    <i class="fab fa-linkedin-in"></i>
                </a>

                <a href="#" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>

                <a href="#" class="social-icon">
                    <i class="fas fa-envelope"></i>
                </a>

            </div>

        </div>

    </div>

</main>

<script src="../assets/js/auth.js"></script>
<script src="../assets/js/script.js"></script>

</body>
</html>