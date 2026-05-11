<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../test_screens/login.php");
    exit();
}

if (isset($_POST['update_reservation'])) {
    $user_id = $_SESSION['user_id'];
    $reservation_id = $_POST['reservation_id'];
    $reservation_date = $_POST['reservation_date'];

    $studentSql = "SELECT student_id FROM tblstudent WHERE user_id = $1";
    $studentResult = pg_query_params($conn, $studentSql, [$user_id]);

    if (!$studentResult || pg_num_rows($studentResult) == 0) {
        echo "Student account not found.";
        exit();
    }

    $student = pg_fetch_assoc($studentResult);
    $student_id = $student['student_id'];

    $sql = "UPDATE tblreservation
            SET reservation_date = $1, status = 'Pending'
            WHERE reservation_id = $2 AND student_id = $3";

    $result = pg_query_params($conn, $sql, [
        $reservation_date,
        $reservation_id,
        $student_id
    ]);

    if ($result) {
        header("Location: ../test_screens/reservation_status.php");
        exit();
    } else {
        echo "Failed to update reservation: " . pg_last_error($conn);
        exit();
    }
}
?>