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
    <title>WildSpace - Contact Us</title>
    <link rel="stylesheet" href="../assets/css/contact.css">
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

    <!-- Background Image -->
    <!-- <img src="../assets/images/bg_login.jpg" alt="Background" class="background-image"> -->

    <main class="auth-wrapper">
        <div class="contact-card" id="contactCard">
            <div class="contact-header">
                <h1 class="contact-title">Send us a <span>message</span></h1>
                <p class="contact-subtitle">We'd love to hear from you.</p>
            </div>

            <?php if (!empty($contactMessage)) { ?>
                <div class="status-msg <?php echo $contactMessageType === 'success' ? 'success-txt' : 'error-txt'; ?>" style="display:block; margin-bottom:1rem;">
                    <?php echo htmlspecialchars($contactMessage); ?>
                </div>
            <?php } ?>

            <!-- Form points to a backend action script -->
            <form class="contact-form" id="contactForm" action="../actions/contact_action.php" method="POST">
                <div class="form-group">
                    <input type="text" name="name" id="userName" class="form-input" placeholder="Full Name:" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email" id="userEmail" class="form-input" placeholder="Email Address:" required>
                </div>

                <div class="form-group">
                    <input type="text" name="subject" id="subject" class="form-input" placeholder="Subject:" required>
                </div>

                <div class="form-group">
                    <textarea name="message" id="message" class="form-input textarea-input" placeholder="Your Message:" rows="5" required></textarea>
                </div>

                <button type="submit" name="send_message" class="contact-submit-button" id="submitBtn">Send Message</button>
            </form>

            <div id="responseMessage" class="status-msg"></div>

            <div class="contact-footer">
                <p class="footer-text">Prefer social media?</p>
                <div class="social-links">
                    <a href="#" class="social-icon">f</a>
                    <a href="#" class="social-icon">in</a>
                    <a href="#" class="social-icon">📷</a>
                    <a href="mailto:support@wildspace.com" class="social-icon">✉</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                 <h1 class="logo-text">WildSpace</h1>
                <p>Study without the hassle</p>
            </div>
            <div class="footer-content">
                <p class="footer-contact">Contact: +63 XXX XXX XXXX</p>
                <p class="footer-copyright">&copy; 2024 WildSpace. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>