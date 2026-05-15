<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reservation for Student</title>
</head>
<body>

<h2>Create Reservation for Student</h2>

<form action="../actions/admin_add_reservation.php" method="POST">
    <label>Student/User Email</label><br>
    <input type="email" name="email" required>
    <br><br>

    <label>Reservation Date</label><br>
    <input type="date" name="reservation_date" required>
    <br><br>

    <label>Capacity</label><br>
    <input type="number" name="capacity" min="1" max="20" required>
    <br><br>

    <button type="submit" name="admin_create_reservation">Create Reservation</button>
</form>

<br>

<a href="admin_reservations.php">Back to Admin Reservation Dashboard</a>

</body>
</html>