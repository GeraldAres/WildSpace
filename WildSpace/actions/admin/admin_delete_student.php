<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header('Location: ../../client_side/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../../client_side/admin_dashboard.php?view=students');
    exit();
}

$student_id = (int) $_GET['id'];

pg_query($conn, 'BEGIN');

$deleteReservations = pg_query_params(
    $conn,
    'DELETE FROM tblreservation WHERE student_id = $1',
    [$student_id]
);

if (!$deleteReservations) {
    pg_query($conn, 'ROLLBACK');
    echo 'Failed to delete student reservations: ' . pg_last_error($conn);
    exit();
}

$findStudent = pg_query_params(
    $conn,
    'SELECT user_id FROM tblstudent WHERE student_id = $1',
    [$student_id]
);

if (!$findStudent || pg_num_rows($findStudent) === 0) {
    pg_query($conn, 'ROLLBACK');
    header('Location: ../../client_side/admin_dashboard.php?view=students');
    exit();
}

$student = pg_fetch_assoc($findStudent);
$user_id = (int) $student['user_id'];

$deleteStudent = pg_query_params(
    $conn,
    'DELETE FROM tblstudent WHERE student_id = $1',
    [$student_id]
);

if (!$deleteStudent) {
    pg_query($conn, 'ROLLBACK');
    echo 'Failed to delete student: ' . pg_last_error($conn);
    exit();
}

$deleteUser = pg_query_params(
    $conn,
    'DELETE FROM tbluser WHERE user_id = $1',
    [$user_id]
);

if (!$deleteUser) {
    pg_query($conn, 'ROLLBACK');
    echo 'Failed to delete user account: ' . pg_last_error($conn);
    exit();
}

pg_query($conn, 'COMMIT');

header('Location: ../../client_side/admin_dashboard.php?view=students');
exit();
