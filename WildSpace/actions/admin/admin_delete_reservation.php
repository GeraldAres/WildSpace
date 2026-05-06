<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../test_screens/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
}

$reservation_id = $_GET['id'];

$sql = "DELETE FROM tblreservation WHERE reservation_id = $1";

$result = pg_query_params($conn, $sql, [$reservation_id]);

if ($result) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
} else {
    echo "Failed to delete reservation: " . pg_last_error($conn);
    exit();
}
?>