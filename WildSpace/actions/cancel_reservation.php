<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../test_screens/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'];

$sql = "UPDATE tblreservation
        SET status = 'Cancelled'
        WHERE reservation_id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $reservation_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../test_screens/reservation_status.php");
    exit();
} else {
    echo "Failed to cancel reservation: " . mysqli_error($conn);
}
?>