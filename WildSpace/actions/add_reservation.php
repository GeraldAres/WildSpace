<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../test_screens/login.php");
    exit();
}

if (isset($_POST['book_reservation'])) {
    $user_id = $_SESSION['user_id'];
    $reservation_date = $_POST['reservation_date'];
    $capacity = $_POST['capacity'];
    $status = "Pending";

    if (empty($reservation_date) || empty($capacity)) {
        echo "Please fill out all fields.";
        exit();
    }

    if ($capacity < 1) {
        echo "Capacity must be at least 1.";
        exit();
    }

    $sql = "INSERT INTO tblreservation 
            (user_id, status, reservation_date, capacity)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Database error: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "issi", $user_id, $status, $reservation_date, $capacity);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../test_screens/reservation_status.php");
        exit();
    } else {
        echo "Failed to book reservation: " . mysqli_error($conn);
    }
}
?>