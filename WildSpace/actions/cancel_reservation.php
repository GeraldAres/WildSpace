<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../test_screens/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../test_screens/reservation_status.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reservation_id = $_GET['id'];

$sql = "UPDATE tblreservation
        SET status = 'Cancelled'
        WHERE reservation_id = $1 AND user_id = $2";

$result = pg_query_params($conn, $sql, [$reservation_id, $user_id]);

if ($result) {
    header("Location: ../test_screens/reservation_status.php");
    exit();
} else {
    echo "Failed to cancel reservation: " . pg_last_error($conn);
    exit();
}
?>