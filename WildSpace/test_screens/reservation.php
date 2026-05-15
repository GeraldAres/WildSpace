<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Book Reservation</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

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
                <a href="reservation.php" class="nav-link">Reservation</a>
                <button class="cta-button" onclick="location.href='contact.php'">Contact Us</button>
            </div>
        </div>
    </nav>

    <section class="reservation-section">
        <div class="reservation-left">
            <h1>Focus on studying, we’ll handle the seat.</h1>
            <p>The easiest way to claim your study spot on campus.</p>
        </div>

        <div class="reservation-right">
            <form class="reservation-form" action="../actions/add_reservation.php" method="POST">
                
                <div class="form-group">
                    <label>Date:</label>
                    <input 
                        type="date" 
                        name="reservation_date" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Capacity:</label>
                    <input 
                        type="number" 
                        name="capacity" 
                        min="1" 
                        max="20" 
                        required
                    >
                </div>

                <button type="submit" name="book_reservation" class="auth-submit-button">
                    Book Now
                </button>
            </form>

            <br>

            <a href="reservation_status.php">View My Reservations</a>
        </div>
    </section>

</body>
</html>