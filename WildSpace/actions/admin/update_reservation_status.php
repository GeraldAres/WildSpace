<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../test_screens/login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
}

$reservation_id = $_GET['id'];
$status = $_GET['status'];
$admin_id = $_SESSION['admin_id'];

$allowed_statuses = ['Approved', 'Rejected'];

if (!in_array($status, $allowed_statuses)) {
    echo "Invalid reservation status.";
    exit();
}

$sql = "UPDATE tblreservation
        SET status = $1, admin_id = $2
        WHERE reservation_id = $3";

$result = pg_query_params($conn, $sql, [
    $status,
    $admin_id,
    $reservation_id
]);

if ($result) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
} else {
    echo "Failed to update reservation status: " . pg_last_error($conn);
    exit();
}
?>