<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header('Location: ../../client_side/login.php');
    exit();
}

if (!isset($_GET['id'], $_GET['status'])) {
    header('Location: ../../client_side/admin_reservations.php?view=requests');
    exit();
}

$reservation_id = (int) $_GET['id'];
$status = $_GET['status'];

$allowed = ['Approved', 'Rejected', 'Pending'];

if (!in_array($status, $allowed, true)) {
    header('Location: ../../client_side/admin_reservations.php?view=requests');
    exit();
}

$sql = 'UPDATE tblreservation SET status = $1 WHERE reservation_id = $2';
$result = pg_query_params($conn, $sql, [$status, $reservation_id]);

if (!$result) {
    echo 'Failed to update reservation: ' . pg_last_error($conn);
    exit();
}

header('Location: ../../client_side/admin_reservations.php?view=requests');
exit();
