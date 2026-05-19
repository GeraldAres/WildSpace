<?php
session_start();
include '../database/connection.php';

header('Content-Type: application/json');

function registerResponse($success, $message, $redirect = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit();
}

if (!isset($_POST['register'])) {
    registerResponse(false, "Invalid request.");
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$mobile_number = trim($_POST['mobile_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'student';

if (
    empty($firstname) ||
    empty($lastname) ||
    empty($gender) ||
    empty($mobile_number) ||
    empty($email) ||
    empty($password) ||
    empty($confirm_password) ||
    empty($role)
) {
    registerResponse(false, "Please fill out all fields.");
}

if (!in_array($role, ['student', 'admin'])) {
    registerResponse(false, "Invalid account role selected.");
}

if (!str_ends_with($email, '@cit.edu')) {
    registerResponse(false, "Only institutional emails ending with @cit.edu are allowed.");
}

if ($password !== $confirm_password) {
    registerResponse(false, "Passwords do not match.");
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    registerResponse(false, "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
}

$allowed_genders = ['Male', 'Female', 'Prefer not to say'];

if (!in_array($gender, $allowed_genders)) {
    registerResponse(false, "Invalid gender selected.");
}

if (!preg_match('/^[0-9]{11}$/', $mobile_number)) {
    registerResponse(false, "Mobile number must contain exactly 11 digits.");
}

$checkEmail = "SELECT user_id FROM tbluser WHERE email = $1";
$emailResult = pg_query_params($conn, $checkEmail, [$email]);

if (!$emailResult) {
    registerResponse(false, "Database error while checking email.");
}

if (pg_num_rows($emailResult) > 0) {
    registerResponse(false, "Email already exists.");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

pg_query($conn, "BEGIN");

$insertUser = "INSERT INTO tbluser 
    (firstname, lastname, email, password, mobile_number, gender)
    VALUES ($1, $2, $3, $4, $5, $6)
    RETURNING user_id";

$userResult = pg_query_params($conn, $insertUser, [
    $firstname,
    $lastname,
    $email,
    $hashed_password,
    $mobile_number,
    $gender
]);

if (!$userResult) {
    pg_query($conn, "ROLLBACK");
    registerResponse(false, "User registration failed: " . pg_last_error($conn));
}

$user = pg_fetch_assoc($userResult);
$user_id = $user['user_id'];

if ($role === 'admin') {
    $insertRole = "INSERT INTO tbladmin (user_id) VALUES ($1)";
} else {
    $insertRole = "INSERT INTO tblstudent (user_id) VALUES ($1)";
}

$roleResult = pg_query_params($conn, $insertRole, [$user_id]);

if (!$roleResult) {
    pg_query($conn, "ROLLBACK");
    registerResponse(false, "Role registration failed: " . pg_last_error($conn));
}

pg_query($conn, "COMMIT");

registerResponse(true, "Account created successfully.", "../client_side/login.php");
?>