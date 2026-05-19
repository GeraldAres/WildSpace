<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../client_side/student_dashboard.php");
    exit();
}

$reservation_id = $_GET['id'];
$student_id = $_SESSION['student_id'];

$sql = "DELETE FROM tblreservation 
        WHERE reservation_id = $1 
        AND student_id = $2";

$result = pg_query_params($conn, $sql, [$reservation_id, $student_id]);

header("Location: ../../client_side/student_dashboard.php");
exit();
?>