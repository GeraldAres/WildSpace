<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'];

$sql = "SELECT * FROM tblreservation 
        WHERE reservation_id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $reservation_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Reservation not found.";
    exit();
}

$reservation = mysqli_fetch_assoc($result);
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

    <label>New Reservation Date and Time</label>
    <input 
        type="datetime-local" 
        name="reservation_date" 
        value="<?php echo date('Y-m-d\TH:i', strtotime($reservation['reservation_date'])); ?>" 
        required
    >

    <button type="submit" name="update_reservation">Update Reservation</button>
</form>

</body>
</html>