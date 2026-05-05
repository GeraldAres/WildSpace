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

    $sql = "UPDATE tblreservation
            SET reservation_date = ?, status = 'Pending'
            WHERE reservation_id = ? AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $reservation_date, $reservation_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../test_screens/reservation_status.php");
        exit();
    } else {
        echo "Failed to update reservation: " . mysqli_error($conn);
    }
}
?>