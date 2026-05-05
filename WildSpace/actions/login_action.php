<?php
session_start();
include '../database/connection.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "Please fill out all fields.";
        exit();
    }

    $sql = "SELECT * FROM tbluser WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Database error: " . mysqli_error($conn);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];

            // Check if the user is an admin
            $adminSql = "SELECT * FROM tbladmin WHERE user_id = ?";
            $adminStmt = mysqli_prepare($conn, $adminSql);
            mysqli_stmt_bind_param($adminStmt, "i", $user['user_id']);
            mysqli_stmt_execute($adminStmt);

            $adminResult = mysqli_stmt_get_result($adminStmt);

            if (mysqli_num_rows($adminResult) == 1) {
                $admin = mysqli_fetch_assoc($adminResult);
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['role'] = "admin";
            } else {
                $_SESSION['role'] = "user";
            }

            // Always redirect to reservation page after login
            header("Location: ../test_screens/admin_reservations.php");
            exit();

        } else {
            echo "Incorrect password.";
            exit();
        }
    } else {
        echo "Email not found.";
        exit();
    }
} else {
    header("Location: ../test_screens/login.php");
    exit();
}
?>