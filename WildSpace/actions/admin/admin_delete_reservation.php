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

$sql = "DELETE FROM tblreservation WHERE reservation_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo "Database error: " . mysqli_error($conn);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $reservation_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
} else {
    echo "Failed to delete reservation: " . mysqli_error($conn);
    exit();
}
?>