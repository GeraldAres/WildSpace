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

    $findUser = "SELECT user_id FROM tbluser WHERE email = ?";
    $stmtUser = mysqli_prepare($conn, $findUser);

    if (!$stmtUser) {
        echo "Database error: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($stmtUser, "s", $email);
    mysqli_stmt_execute($stmtUser);
    $userResult = mysqli_stmt_get_result($stmtUser);

    if (mysqli_num_rows($userResult) == 0) {
        echo "No user found with that email.";
        exit();
    }

    $user = mysqli_fetch_assoc($userResult);
    $user_id = $user['user_id'];

    $insertReservation = "INSERT INTO tblreservation 
                          (user_id, admin_id, status, reservation_date, capacity)
                          VALUES (?, ?, ?, ?, ?)";

    $stmtReservation = mysqli_prepare($conn, $insertReservation);

    if (!$stmtReservation) {
        echo "Database error: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param(
        $stmtReservation,
        "iissi",
        $user_id,
        $admin_id,
        $status,
        $reservation_date,
        $capacity
    );

    if (mysqli_stmt_execute($stmtReservation)) {
        header("Location: ../test_screens/admin_reservations.php");
        exit();
    } else {
        echo "Failed to create reservation: " . mysqli_error($conn);
    }
}
?>