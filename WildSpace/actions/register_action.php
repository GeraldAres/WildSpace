<?php

include '../database/connection.php';

header('Content-Type: application/json');

function registerResponse($success, $message)
{
    echo json_encode([
        "success" => $success,
        "message" => $message
    ]);
    exit();
}

/* ================= VALIDATE REQUEST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    registerResponse(false, "Invalid request.");
}

$isRegistrationSubmit = isset($_POST['register'])
    || (isset($_POST['firstname'], $_POST['email'], $_POST['password']));

if (!$isRegistrationSubmit) {
    registerResponse(false, "Invalid request.");
}

/* ================= GET INPUTS ================= */
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$gender = trim($_POST['gender'] ?? '');
$mobile_number = trim($_POST['mobile_number'] ?? '');
$role = trim($_POST['role'] ?? 'student');

/* ================= EMPTY CHECK ================= */
if (
    empty($firstname) ||
    empty($lastname) ||
    empty($email) ||
    empty($password) ||
    empty($confirm_password) ||
    empty($gender) ||
    empty($mobile_number)
) {
    registerResponse(false, "All fields are required.");
}

/* ================= EMAIL VALIDATION ================= */
if (!str_ends_with($email, '@cit.edu')) {
    registerResponse(false, "Only @cit.edu emails are allowed.");
}

/* ================= PASSWORD VALIDATION ================= */
if (
    !preg_match(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        $password
    )
) {
    registerResponse(
        false,
        "Password must contain uppercase, lowercase, number, and special character."
    );
}

/* ================= PASSWORD MATCH ================= */
if ($password !== $confirm_password) {
    registerResponse(false, "Passwords do not match.");
}

/* ================= MOBILE VALIDATION ================= */
if (!preg_match('/^[0-9]{11}$/', $mobile_number)) {
    registerResponse(false, "Mobile number must contain exactly 11 digits.");
}

/* ================= GENDER VALIDATION ================= */
$allowed_genders = ['Male', 'Female', 'Prefer not to say'];

if (!in_array($gender, $allowed_genders)) {
    registerResponse(false, "Invalid gender selected.");
}

/* ================= EMAIL EXISTS ================= */
$checkEmail = "SELECT user_id FROM tbluser WHERE email = $1";

$emailResult = pg_query_params($conn, $checkEmail, [$email]);

if (!$emailResult) {
    registerResponse(false, "Database error.");
}

if (pg_num_rows($emailResult) > 0) {
    registerResponse(false, "Email already exists.");
}

/* ================= HASH PASSWORD ================= */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* ================= TRANSACTION ================= */
pg_query($conn, "BEGIN");

/* ================= INSERT USER ================= */
$insertUser = "
    INSERT INTO tbluser
    (
        firstname,
        lastname,
        email,
        password,
        mobile_number,
        gender
    )
    VALUES
    (
        $1,
        $2,
        $3,
        $4,
        $5,
        $6
    )
    RETURNING user_id
";

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

    registerResponse(false, "Registration failed.");
}

$user = pg_fetch_assoc($userResult);

$user_id = $user['user_id'];

/* ================= INSERT ROLE ================= */
if ($role === "admin") {

    $insertRole = "INSERT INTO tbladmin (user_id) VALUES ($1)";

} else {

    $insertRole = "INSERT INTO tblstudent (user_id) VALUES ($1)";
}

$roleResult = pg_query_params($conn, $insertRole, [$user_id]);

if (!$roleResult) {

    pg_query($conn, "ROLLBACK");

    registerResponse(false, "Role assignment failed.");
}

/* ================= COMMIT ================= */
pg_query($conn, "COMMIT");

registerResponse(true, "Account created successfully.");
?>