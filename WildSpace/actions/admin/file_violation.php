<?php
session_start();

include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header('Location: ../../client_side/login.php');
    exit();
}

if (!isset($_POST['file_violation'])) {
    header('Location: ../../client_side/admin_dashboard.php?view=file_violation');
    exit();
}

$reservation_id = $_POST['reservation_id'] ?? '';
$violation_type = trim($_POST['violation_type'] ?? '');
$description = trim($_POST['description'] ?? '');
$admin_id = $_SESSION['admin_id'];

$allowedTypes = [
    'Noise Complaint',
    'Misuse of Study Space',
    'Damage to Property',
    'Leaving Area Unclean'
];

if (empty($reservation_id) || empty($violation_type) || empty($description)) {
    die('Please complete all fields.');
}

if (!in_array($violation_type, $allowedTypes, true)) {
    die('Invalid violation type.');
}

$reservationSql = "SELECT reservation_id, student_id, status
                   FROM tblreservation
                   WHERE reservation_id = $1
                   AND status = 'Approved'";

$reservationResult = pg_query_params($conn, $reservationSql, [$reservation_id]);

if (!$reservationResult || pg_num_rows($reservationResult) === 0) {
    die('Approved reservation not found.');
}

$reservation = pg_fetch_assoc($reservationResult);

$insertSql = "INSERT INTO tblviolation
                (reservation_id, student_id, admin_id, violation_type, description, violation_date)
              VALUES
                ($1, $2, $3, $4, $5, NOW())";

$insertResult = pg_query_params($conn, $insertSql, [
    $reservation['reservation_id'],
    $reservation['student_id'],
    $admin_id,
    $violation_type,
    $description
]);

if (!$insertResult) {
    die('Failed to file violation: ' . pg_last_error($conn));
}

header('Location: ../../client_side/admin_dashboard.php?view=file_violation');
exit();
?>