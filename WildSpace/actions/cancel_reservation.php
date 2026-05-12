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

$studentSql = "SELECT student_id FROM tblstudent WHERE user_id = $1";
$studentResult = pg_query_params($conn, $studentSql, [$user_id]);

if (!$studentResult || pg_num_rows($studentResult) == 0) {
    echo "Student account not found.";
    exit();
}

$student = pg_fetch_assoc($studentResult);
$student_id = $student['student_id'];

$sql = "UPDATE tblreservation
        SET status = 'Cancelled'
        WHERE reservation_id = $1 AND student_id = $2";

$result = pg_query_params($conn, $sql, [$reservation_id, $student_id]);

if ($result) {
    header("Location: ../test_screens/reservation_status.php");
    exit();
} else {
    echo "Failed to cancel reservation: " . pg_last_error($conn);
    exit();
}
?>