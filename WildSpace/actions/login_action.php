<?php
session_start();
include '../database/connection.php';

// Hardcoded admin credentials
const HARDCODED_ADMIN_LOGIN = 'admin@wildspacecit.edu';
const HARDCODED_ADMIN_PASSWORD = 'WildspaceAdmin!';

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        echo "Please fill out all fields.";
        exit();
    }

    /*
    =========================================
    1. HARDCODED ADMIN LOGIN
    =========================================
    */
    if (
        $email === HARDCODED_ADMIN_LOGIN &&
        $password === HARDCODED_ADMIN_PASSWORD
    ) {
        $_SESSION['user_id'] = 0;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = "admin";

        header("Location: ../client_side/admin_reservations.php");
        exit();
    }

    /*
    =========================================
    2. NORMAL USER LOGIN (DATABASE)
    =========================================
    */
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
            $_SESSION['role'] = "user";

            header("Location: ../client_side/admin_reservations.php");
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
    header("Location: ../client_side/admin_reservations.php");
    exit();
}
?>