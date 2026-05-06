<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../test_screens/login.php");
    exit();
}

if (isset($_POST['admin_create_reservation'])) {
    $email = trim($_POST['email']);
    $reservation_date = $_POST['reservation_date'];
    $capacity = $_POST['capacity'];
    $admin_id = $_SESSION['admin_id'];
    $status = "Approved";

    if (empty($email) || empty($reservation_date) || empty($capacity)) {
        echo "Please fill out all fields.";
        exit();
    }

    if ($capacity < 1) {
        echo "Capacity must be at least 1.";
        exit();
    }

    $findUser = "SELECT user_id FROM tbluser WHERE email = $1";
    $userResult = pg_query_params($conn, $findUser, [$email]);

    if (!$userResult) {
        echo "Database error: " . pg_last_error($conn);
        exit();
    }

    if (pg_num_rows($userResult) == 0) {
        echo "No user found with that email.";
        exit();
    }

    $user = pg_fetch_assoc($userResult);
    $user_id = $user['user_id'];

    $insertReservation = "INSERT INTO tblreservation 
                          (user_id, admin_id, status, reservation_date, capacity)
                          VALUES ($1, $2, $3, $4, $5)";

    $reservationResult = pg_query_params($conn, $insertReservation, [
        $user_id,
        $admin_id,
        $status,
        $reservation_date,
        $capacity
    ]);

    if ($reservationResult) {
        header("Location: ../test_screens/admin_reservations.php");
        exit();
    } else {
        echo "Failed to create reservation: " . pg_last_error($conn);
        exit();
    }
}
?>