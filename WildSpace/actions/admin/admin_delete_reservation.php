<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../client_side/admin_dashboard.php?view=requests");
    exit();
}

$reservation_id = $_GET['id'];

$sql = "DELETE FROM tblreservation WHERE reservation_id = $1";

$result = pg_query_params($conn, $sql, [$reservation_id]);

if ($result) {
    header("Location: ../../client_side/admin_dashboard.php?view=requests");
    exit();
} else {
    echo "Failed to delete reservation: " . pg_last_error($conn);
    exit();
}
?>