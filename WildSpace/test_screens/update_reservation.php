<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: reservation_status.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'];

$sql = "SELECT * FROM tblreservation 
        WHERE reservation_id = $1 AND user_id = $2";

$result = pg_query_params($conn, $sql, [$reservation_id, $user_id]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($result) == 0) {
    echo "Reservation not found.";
    exit();
}

$reservation = pg_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Reservation</title>
</head>
<body>

<h2>Update Reservation</h2>

<form action="../actions/update_reservation_action.php" method="POST">
    <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">

    <label>New Reservation Date</label>
    <input 
        type="date" 
        name="reservation_date" 
        value="<?php echo $reservation['reservation_date']; ?>" 
        required
    >

    <button type="submit" name="update_reservation">Update Reservation</button>
</form>

</body>
</html>