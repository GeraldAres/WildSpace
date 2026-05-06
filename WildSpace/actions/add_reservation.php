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
            VALUES ($1, $2, $3, $4)";

    $result = pg_query_params($conn, $sql, [
        $user_id,
        $status,
        $reservation_date,
        $capacity
    ]);

    if ($result) {
        header("Location: ../test_screens/reservation_status.php");
        exit();
    } else {
        echo "Failed to book reservation: " . pg_last_error($conn);
        exit();
    }
}
?>