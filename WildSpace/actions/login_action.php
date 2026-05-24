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

$sql = "SELECT user_id, email, password
        FROM tbluser
        WHERE email = $1";

$result = pg_query_params($conn, $sql, [$email]);

if (!$result) {
    $_SESSION['login_error'] = "Database error. Please try again.";
    header("Location: ../client_side/login.php");
    exit();
}

if (pg_num_rows($result) === 0) {
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

$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $user['email'];

$adminSql = "SELECT admin_id
             FROM tbladmin
             WHERE user_id = $1";

$adminResult = pg_query_params($conn, $adminSql, [$user_id]);

if ($adminResult && pg_num_rows($adminResult) > 0) {
    $admin = pg_fetch_assoc($adminResult);

    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['role'] = 'admin';

    unset($_SESSION['student_id']);
    unset($_SESSION['login_error']);

    header("Location: ../client_side/admin_dashboard.php");
    exit();
}

$studentSql = "SELECT student_id
               FROM tblstudent
               WHERE user_id = $1";

$studentResult = pg_query_params($conn, $studentSql, [$user_id]);

if ($studentResult && pg_num_rows($studentResult) > 0) {
    $student = pg_fetch_assoc($studentResult);

    $_SESSION['student_id'] = $student['student_id'];
    $_SESSION['role'] = 'student';

    unset($_SESSION['admin_id']);
    unset($_SESSION['login_error']);

    header("Location: ../client_side/student_dashboard.php");
    exit();
}

session_unset();
session_destroy();

session_start();
$_SESSION['login_error'] = "No valid account role was found for this user.";

header("Location: ../client_side/login.php");
exit();
?>