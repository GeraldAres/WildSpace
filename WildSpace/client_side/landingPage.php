<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Study Without the Hassle</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
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

    <!-- Main Hero Section (centered) -->
    <section class="hero hero-centered">
        <div class="hero-center">
            <span class="eyebrow">Cebu Institute of Technology - University</span>
            <h1 class="hero-title">Study without the hassle.</h1>
            <p class="hero-subtitle">Reserve CIT‑U study spots, group rooms, and quiet areas—all in one place.</p>

            <div class="hero-actions" style="justify-content:center;margin-top:1.25rem;">
                <button class="cta-button cta-primary" onclick="location.href='book.php'">Get Started</button>
                <a class="secondary-link" href="landingPage.php#features">Platform features</a>
            </div>

            <div class="muted" style="margin-top:1rem;color:var(--text-muted);">Built specifically for CIT‑U students and campus workflows.</div>
            <!-- Hero feature cards (moved here for emphasis) -->
            <div class="hero-features" style="margin-top:1.25rem; width:100%;">
                <div class="feature-item" style="padding:1rem;">
                    <strong>Real-time Availability</strong>
                    <p class="feature-text">See live room status and avoid double-booking.</p>
                </div>

                <div class="feature-item" style="padding:1rem;">
                    <strong>Campus-first Design</strong>
                    <p class="feature-text">Workflows tuned for CIT‑U policies and timetables.</p>
                </div>

                <div class="feature-item" style="padding:1rem;">
                    <strong>Secure & Auditable</strong>
                    <p class="feature-text">Admin logs and easy management for staff.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Page 2 Section -->
    <section class="page2">
        <div class="page2-left">
            <h2 class="page2-title">Book <span class="bold-word">Smart.</span> Study <span class="bold-word">Better.</span></h2>
            <p class="page2-description">
                Easily locate and reserve study spaces across CIT-U with a streamlined system designed to reduce conflicts, save time, and improve collaboration.
            </p>
            <button class="cta-button page2-cta" onclick="location.href='book.php'">Book Now</button>
        </div>

        <div class="page2-right">
            <div class="feature-card" style="display:flex;flex-direction:column;gap:0.75rem;align-items:flex-start;">
                <div style="font-weight:800;font-size:1.25rem;color:var(--text-main);">Available Now</div>
                <div style="color:var(--text-muted);">Rooms near you with instant booking</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-top:0.5rem;width:100%;">
                    <div style="background:rgba(255,255,255,0.03);padding:0.6rem;border-radius:10px;">Quiet Room · 2 left</div>
                    <div style="background:rgba(255,255,255,0.03);padding:0.6rem;border-radius:10px;">Group Table · 1 left</div>
                    <div style="background:rgba(255,255,255,0.03);padding:0.6rem;border-radius:10px;">Open Desk · 6 left</div>
                    <div style="background:rgba(255,255,255,0.03);padding:0.6rem;border-radius:10px;">AV Room · 0 left</div>
                </div>
                <a class="secondary-link" href="book.php" style="margin-top:0.75rem;">See all spaces</a>
            </div>
        </div>
    </section>

    <!-- Section 3 -->
    <section class="section3">
        <div class="section3-content">
            <div class="section3-title">
                 <h1 class="logo-text">WildSpace</h1>
            </div>
            <p class="section3-description">
                A centralized platform for CIT-U students to check availability, reserve tables, and manage study spaces in real time—so you spend less time searching and more time learning.
            </p>
        </div>
    </section>

    <!-- Section 5 -->
    <section class="section5">
        <div class="section5-container">
            <div class="section5-header">
                <p class="section5-branding">WildSpace</p>
                <h2 class="section5-main-title"><span class="section5-bold">Platform</span>Features</h2>
                <p class="section5-subtitle">Book Your Space in Seconds</p>
            </div>

            <div class="section5-content">
                <div class="platform-features">
                    <div class="feature-item">
                        <button class="feature-button">Book a Space</button>
                        <p class="feature-text">Reserve tables in seconds without the hassle of walking around campus.</p>
                    </div>

                    <div class="feature-item">
                        <button class="feature-button">Book by Group</button>
                        <p class="feature-text">Request larger areas for meetings, discussions, and team collaboration.</p>
                    </div>

                    <div class="feature-item">
                        <button class="feature-button">Live Space Tracker</button>
                        <p class="feature-text">See real-time availability and secure your spot before it's taken.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script src="../assets/js/script.js"></script>
</body>
</html>