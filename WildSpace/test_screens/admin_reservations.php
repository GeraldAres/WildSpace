<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT 
            r.reservation_id,
            r.user_id,
            r.admin_id,
            r.status,
            r.reservation_date,
            r.capacity,
            r.date_created,
            u.email
        FROM tblreservation r
        INNER JOIN tbluser u ON r.user_id = u.user_id
        ORDER BY r.date_created DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Admin Reservation Dashboard</title>
</head>
<body>

<h2>Admin Reservation Dashboard</h2>

<a href="admin_create_reservation.php">Create Reservation for Student</a>
<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Reservation ID</th>
        <th>Student/User Email</th>
        <th>Date</th>
        <th>Capacity</th>
        <th>Status</th>
        <th>Date Created</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['reservation_id']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['reservation_date']; ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['date_created']; ?></td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
<a href="../actions/admin/update_reservation_status.php?id=<?php echo $row['reservation_id']; ?>&status=Approved"
   onclick="return confirm('Approve this reservation?');">
   Approve
</a>

                    |

<a href="../actions/admin/update_reservation_status.php?id=<?php echo $row['reservation_id']; ?>&status=Rejected"
   onclick="return confirm('Reject this reservation?');">
   Reject
</a>

                    |
                <?php } ?>

              <a href="../actions/admin/admin_delete_reservation.php?id=<?php echo $row['reservation_id']; ?>"
   onclick="return confirm('Delete this reservation permanently?');">
   Delete
</a>
            </td>
        </tr>
    <?php } ?>
</table>

<br>


</body>
</html>