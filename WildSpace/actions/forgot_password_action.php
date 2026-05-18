<?php
session_start();
include '../database/connection.php';

if (!isset($_POST['reset_password'])) {
    header("Location: ../client_side/forgot_password.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($email) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['forgot_error'] = "Please fill out all fields.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['forgot_error'] = "Passwords do not match.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
    $_SESSION['forgot_error'] = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

$checkSql = "SELECT user_id FROM tbluser WHERE email = $1";
$checkResult = pg_query_params($conn, $checkSql, [$email]);

if (!$checkResult) {
    $_SESSION['forgot_error'] = "Database error. Please try again.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

if (pg_num_rows($checkResult) === 0) {
    $_SESSION['forgot_error'] = "Email does not exist.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$updateSql = "UPDATE tbluser SET password = $1 WHERE email = $2";
$updateResult = pg_query_params($conn, $updateSql, [$hashed_password, $email]);

if (!$updateResult) {
    $_SESSION['forgot_error'] = "Failed to reset password.";
    header("Location: ../client_side/forgot_password.php");
    exit();
}

$_SESSION['login_error'] = "Password reset successful. Please log in using your new password.";
header("Location: ../client_side/login.php");
exit();
?>