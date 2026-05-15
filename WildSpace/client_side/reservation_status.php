<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$studentSql = "SELECT student_id FROM tblstudent WHERE user_id = $1";
$studentResult = pg_query_params($conn, $studentSql, [$user_id]);

if (!$studentResult) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($studentResult) == 0) {
    echo "Student account not found.";
    exit();
}

$student = pg_fetch_assoc($studentResult);
$student_id = $student['student_id'];

$sql = "SELECT * FROM tblreservation 
        WHERE student_id = $1
        ORDER BY date_created DESC";

$result = pg_query_params($conn, $sql, [$student_id]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}
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
        <th>Date</th>
        <th>Status</th>
        <th>Date Created</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = pg_fetch_assoc($result)) { ?>
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