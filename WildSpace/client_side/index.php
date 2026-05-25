<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Study Without the Hassle</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                <a href="book.php" class="nav-link">Reservation</a>
                <button class="cta-button" onclick="location.href='contact.php'">Contact Us</button>
            </div>
        </div>
    </nav>

  <section class="hero">
        <div class="hero-left">
            <div class="hero-copy">
                <span class="eyebrow">Studio-grade booking for campus life</span>
                <h1 class="hero-title">Book smarter study spaces with a premium edge.</h1>
                <p class="hero-copytext">WildSpace gives CIT-U students fast access to the best study spots, group rooms, and workspace support—all from one polished dashboard.</p>
                <div class="hero-actions">
                    <button onclick="location.href='login.php'" class="cta-button cta-primary">Get Started</button>
                    <a href="landingPage.php" class="secondary-link">See the platform</a>
                </div>
            </div>
        </div>

        <aside class="hero-right" aria-labelledby="hero-right-heading">
            <div class="feature-card" style="margin-bottom:1rem;">
                <h3 id="hero-right-heading" style="margin-bottom:0.5rem;">Quick Actions</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem;">Sign in or make a reservation in seconds.</p>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                    <button class="cta-button" onclick="location.href='login.php'">Log In</button>
                    <button class="cta-button" onclick="location.href='register.php'">Register</button>
                    <a class="secondary-link" href="book.php">Book Now</a>
                </div>
            </div>

            <div class="feature-card" style="margin-bottom:1rem;">
                <h4 style="margin-bottom:0.75rem;">At a glance</h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <div style="background:rgba(255,255,255,0.02);padding:0.75rem;border-radius:12px;text-align:center;">
                        <div style="font-weight:800;font-size:1.25rem;color:var(--text-main);">34</div>
                        <div style="font-size:0.85rem;color:var(--text-muted);">Spaces</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.02);padding:0.75rem;border-radius:12px;text-align:center;">
                        <div style="font-weight:800;font-size:1.25rem;color:var(--text-main);">12</div>
                        <div style="font-size:0.85rem;color:var(--text-muted);">Active Bookings</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.02);padding:0.75rem;border-radius:12px;text-align:center;">
                        <div style="font-weight:800;font-size:1.25rem;color:var(--text-main);">4</div>
                        <div style="font-size:0.85rem;color:var(--text-muted);">Popular Rooms</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.02);padding:0.75rem;border-radius:12px;text-align:center;">
                        <div style="font-weight:800;font-size:1.25rem;color:var(--text-main);">97%</div>
                        <div style="font-size:0.85rem;color:var(--text-muted);">On-time</div>
                    </div>
                </div>
            </div>

            <div class="feature-card">
                <h4 style="margin-bottom:0.5rem;">How it works</h4>
                <ol style="color:var(--text-muted);padding-left:1.1rem;">
                    <li style="margin-bottom:0.5rem;">Choose date & time</li>
                    <li style="margin-bottom:0.5rem;">Pick a space</li>
                    <li style="margin-bottom:0.5rem;">Confirm reservation</li>
                </ol>
            </div>
        </aside>
    </section>

    <section class="feature-strip">
        <div class="feature-card">
            <strong>Fast reservations</strong>
            <span>Reserve spaces in seconds with instant confirmation.</span>
        </div>
        <div class="feature-card">
            <strong>Premium study hubs</strong>
            <span>Choose the best locations and table setups for your session.</span>
        </div>
        <div class="feature-card">
            <strong>Collaborate easily</strong>
            <span>Invite classmates and reserve group-ready seating.</span>
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

    <script src="../assets/js/index.js"></script>
</body>
</html>