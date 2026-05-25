<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../../client_side/admin_reservations.php?view=requests");
    exit();
}

$reservation_id = $_GET['id'];
$admin_id = $_SESSION['admin_id'];

$sql = "SELECT reservation_id, student_id, status
        FROM tblreservation
        WHERE reservation_id = $1";

$result = pg_query_params($conn, $sql, [$reservation_id]);

if (!$result || pg_num_rows($result) === 0) {
    header("Location: ../../client_side/admin_dashboard.php?view=requests");
    exit();
}

$reservation = pg_fetch_assoc($result);

if ($reservation['status'] !== 'Approved') {
    header("Location: ../../client_side/admin_dashboard.php?view=requests");
    exit();
}

$insertSql = "INSERT INTO tblviolation
                (reservation_id, student_id, admin_id, violation_type, description)
              VALUES
                ($1, $2, $3, $4, $5)
              ON CONFLICT (reservation_id) DO NOTHING";

$insertResult = pg_query_params($conn, $insertSql, [
    $reservation['reservation_id'],
    $reservation['student_id'],
    $admin_id,
    'No Show',
    'Student did not show up for an approved reservation.'
]);

if ($insertResult) {
    $_SESSION['popup_success'] = "No-show violation added successfully.";
} else {
    $_SESSION['popup_error'] = "Failed to add no-show violation.";
}

header("Location: ../../client_side/admin_dashboard.php?view=requests");
exit();
?>