<?php
include '../database/connection.php';

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!str_ends_with($email, '@cit.edu')) {
        echo "Only institutional emails ending with @cit.edu are allowed.";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        exit();
    }

    $checkEmail = "SELECT * FROM tbluser WHERE email = $1";
    $result = pg_query_params($conn, $checkEmail, [$email]);

    if (!$result) {
        echo "Database error: " . pg_last_error($conn);
        exit();
    }

    if (pg_num_rows($result) > 0) {
        echo "Email already exists.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $insertUser = "INSERT INTO tbluser (email, password) VALUES ($1, $2) RETURNING user_id";
    $userResult = pg_query_params($conn, $insertUser, [$email, $hashed_password]);

    if (!$userResult) {
        echo "User registration failed: " . pg_last_error($conn);
        exit();
    }

    $user = pg_fetch_assoc($userResult);
    $user_id = $user['user_id'];

    $insertAdmin = "INSERT INTO tbladmin (user_id) VALUES ($1)";
    $adminResult = pg_query_params($conn, $insertAdmin, [$user_id]);

    if ($adminResult) {
        header("Location: ../test_screens/login.php");
        exit();
    } else {
        echo "Admin registration failed: " . pg_last_error($conn);
        exit();
    }
}
?>