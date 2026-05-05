<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM tblreservation 
        WHERE user_id = ?
        ORDER BY date_created DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation Status</title>
</head>
<body>

<h2>My Reservations</h2>

<a href="reservation.php">Book New Reservation</a>
<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Reservation ID</th>
        <th>Date and Time</th>
        <th>Status</th>
        <th>Date Created</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['reservation_id']; ?></td>
            <td><?php echo $row['reservation_date']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['date_created']; ?></td>
            <td>
                <?php if ($row['status'] !== 'Cancelled') { ?>
                    <a href="update_reservation.php?id=<?php echo $row['reservation_id']; ?>">Update</a>
                    |
                    <a href="../actions/cancel_reservation.php?id=<?php echo $row['reservation_id']; ?>"
                       onclick="return confirm('Are you sure you want to cancel this reservation?');">
                       Cancel
                    </a>
                <?php } else { ?>
                    No actions available
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>