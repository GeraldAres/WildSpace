<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_POST['delete_account'])) {
    header("Location: ../../client_side/student_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$student_id = $_SESSION['student_id'];

pg_query($conn, "BEGIN");

pg_query_params($conn, "DELETE FROM tblreservation WHERE student_id = $1", [$student_id]);
pg_query_params($conn, "DELETE FROM tblstudent WHERE student_id = $1", [$student_id]);
$result = pg_query_params($conn, "DELETE FROM tbluser WHERE user_id = $1", [$user_id]);

if (!$result) {
    pg_query($conn, "ROLLBACK");
    $_SESSION['profile_error'] = "Failed to delete account.";
    header("Location: ../../client_side/edit_profile.php");
    exit();
}

pg_query($conn, "COMMIT");

session_unset();
session_destroy();

header("Location: ../../client_side/login.php");
exit();
?>