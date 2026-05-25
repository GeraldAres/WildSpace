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
    <title>WildSpace - Reservation</title>
    <link rel="stylesheet" href="../assets/css/book.css">
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

    <!-- MAIN BOOKING SECTION -->
<main class="booking-wrapper">
    <section class="booking-container">

        <!-- LEFT SIDE -->
        <div class="booking-visual">
            <div class="image-overlay">
                <h2>Find your focus.</h2>
                <p>Reserved seating ensures you have the perfect spot to get work done.</p>
            </div>
            <img src="../assets/images/bookimg.png" alt="Booking Illustration" class="hero-book-img">
        </div>

        <!-- RIGHT SIDE -->
        <div class="booking-form-content">

            <div class="form-header">
                <h1 class="auth-title">Reserve your <span>study</span> space</h1>
                <p class="auth-subtitle">Choose your preferred slot below.</p>
            </div>

            <form class="auth-form" id="bookingForm" action="../actions/book_action.php" method="POST">

                <div class="form-group">
                    <label for="bookingDate">Select Date</label>
                    <input type="date" name="booking_date" class="form-input" id="bookingDate" required>
                </div>

                <div class="form-group">
                    <label for="capacity">Table Capacity</label>
                    <select name="capacity" class="form-input" id="capacity" required>
                        <option value="" disabled selected>Who are you bringing?</option>
                        <option value="1">Solo (1 Person)</option>
                        <option value="2">Duo (2 People)</option>
                        <option value="4">Small Group (4 People)</option>
                        <option value="6">Large Table (6 People)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="timeSlot">Available Time Slot</label>
                    <select name="time_slot" class="form-input" id="timeSlot" required>
                        <option value="" disabled selected>Select a time</option>
                        <option value="08:00-10:00">Morning (08:00 AM - 10:00 AM)</option>
                        <option value="10:00-12:00">Late Morning (10:00 AM - 12:00 PM)</option>
                        <option value="13:00-15:00">Afternoon (01:00 PM - 03:00 PM)</option>
                        <option value="15:00-17:00">Late Afternoon (03:00 PM - 05:00 PM)</option>
                    </select>
                </div>

                <div id="bookingSummary" class="booking-summary"></div>

                <button type="submit" name="submit_booking" class="auth-submit-button">
                    Confirm Reservation
                </button>

            </form>

            <div class="auth-footer">
                <p class="auth-footer-text">
                    Need help? <a href="contact.php" class="auth-link">Contact us</a>
                </p>

                <div class="social-links">
                    <a href="#" class="social-icon">f</a>
                    <a href="#" class="social-icon">in</a>
                    <a href="#" class="social-icon">📷</a>
                    <a href="#" class="social-icon">✉</a>
                </div>
            </div>

        </div>
    </section>
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

    <script src="../assets/js/book.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>