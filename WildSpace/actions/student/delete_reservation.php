<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../client_side/student_dashboard.php?view=reservations");
    exit();
}

$reservation_id = $_GET['id'];
$student_id = $_SESSION['student_id'];

/* Delete related violation first, if any */
pg_query_params($conn, "DELETE FROM tblviolation WHERE reservation_id = $1", [$reservation_id]);

$sql = "DELETE FROM tblreservation 
        WHERE reservation_id = $1 
        AND student_id = $2";

$result = pg_query_params($conn, $sql, [$reservation_id, $student_id]);

if ($result && pg_affected_rows($result) > 0) {
    $_SESSION['popup_success'] = "Reservation deleted successfully.";
} else {
    $_SESSION['popup_error'] = "Failed to delete reservation.";
}

header("Location: ../../client_side/student_dashboard.php?view=reservations");
exit();
?>