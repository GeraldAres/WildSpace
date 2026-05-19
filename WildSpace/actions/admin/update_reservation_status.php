<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: ../../client_side/admin_reservations.php?view=requests");
    exit();
}

$reservation_id = $_GET['id'];
$status = $_GET['status'];
$admin_id = $_SESSION['admin_id'];

$allowedStatuses = ['Approved', 'Rejected'];

if (!in_array($status, $allowedStatuses)) {
    header("Location: ../../client_side/admin_reservations.php?view=requests");
    exit();
}

$sql = "UPDATE tblreservation
        SET status = $1,
            admin_id = $2
        WHERE reservation_id = $3";

$result = pg_query_params($conn, $sql, [
    $status,
    $admin_id,
    $reservation_id
]);

header("Location: ../../client_side/admin_reservations.php?view=requests");
exit();
?>