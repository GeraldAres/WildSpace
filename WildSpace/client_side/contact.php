<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Contact Us</title>
    <link rel="stylesheet" href="../assets/css/contact.css">
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

    <!-- Background Image -->
    <img src="../assets/images/bg_login.jpg" alt="Background" class="background-image">

    <main class="auth-wrapper">
        <div class="contact-card" id="contactCard">
            <div class="contact-header">
                <h1 class="contact-title">Send us a <span>message</span></h1>
                <p class="contact-subtitle">We'd love to hear from you.</p>
            </div>

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

    <script src="../assets/js/contact.js"></script>
</body>
</html>