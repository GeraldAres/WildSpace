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

    $sql = "SELECT * FROM tbluser WHERE email = $1";
    $result = pg_query_params($conn, $sql, [$email]);

    if (!$result) {
        echo "Database error: " . pg_last_error($conn);
        exit();
    }

    if (pg_num_rows($result) == 1) {
        $user = pg_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];

            $adminSql = "SELECT * FROM tbladmin WHERE user_id = $1";
            $adminResult = pg_query_params($conn, $adminSql, [$user['user_id']]);

            if (!$adminResult) {
                echo "Database error: " . pg_last_error($conn);
                exit();
            }

            if (pg_num_rows($adminResult) == 1) {
                $admin = pg_fetch_assoc($adminResult);
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['role'] = "admin";
            } else {
                $_SESSION['role'] = "user";
            }

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