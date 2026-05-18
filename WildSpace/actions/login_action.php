<?php
session_start();

include '../database/connection.php';

if (!isset($_POST['login'])) {
    header("Location: ../client_side/login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Please enter your email and password.";
    header("Location: ../client_side/login.php");
    exit();
}

$sql = "SELECT user_id, email, password FROM tbluser WHERE email = $1";
$result = pg_query_params($conn, $sql, [$email]);

if (!$result || pg_num_rows($result) === 0) {
    $_SESSION['login_error'] = "Incorrect email or password.";
    header("Location: ../client_side/login.php");
    exit();
}

$user = pg_fetch_assoc($result);

if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Incorrect email or password.";
    header("Location: ../client_side/login.php");
    exit();
}

$user_id = $user['user_id'];

/* Check if this user is an admin */
$adminSql = "SELECT admin_id FROM tbladmin WHERE user_id = $1";
$adminResult = pg_query_params($conn, $adminSql, [$user_id]);

if (!$adminResult || pg_num_rows($adminResult) === 0) {
    $_SESSION['login_error'] = "This account is not an admin account.";
    header("Location: ../client_side/login.php");
    exit();
}

$admin = pg_fetch_assoc($adminResult);

$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $user['email'];
$_SESSION['admin_id'] = $admin['admin_id'];

unset($_SESSION['login_error']);

header("Location: ../client_side/admin_reservations.php");
exit();
?>