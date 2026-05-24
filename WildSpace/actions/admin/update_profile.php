<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../client_side/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {
    $dashboard = "../../client_side/admin_dashboard.php";
} elseif ($role === 'student') {
    $dashboard = "../../client_side/student_dashboard.php";
} else {
    $_SESSION['profile_error'] = "Invalid account role.";
    header("Location: ../client_side/edit_profile.php");
    exit();
}

if (!isset($_POST['update_profile'])) {
    header("Location: " . $dashboard);
    exit();
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$mobile_number = trim($_POST['mobile_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');

if (!empty($mobile_number) && !preg_match('/^[0-9]{11}$/', $mobile_number)) {
    $_SESSION['profile_error'] = "Mobile number must contain exactly 11 digits.";
    header("Location: ../client_side/edit_profile.php");
    exit();
}

$allowed_genders = ['', 'Male', 'Female', 'Prefer not to say'];

if (!in_array($gender, $allowed_genders)) {
    $_SESSION['profile_error'] = "Invalid gender selected.";
    header("Location: ../client_side/edit_profile.php");
    exit();
}

$sql = "UPDATE tbluser
        SET firstname = $1,
            lastname = $2,
            mobile_number = $3,
            gender = $4
        WHERE user_id = $5";    

$result = pg_query_params($conn, $sql, [
    $firstname,
    $lastname,
    $mobile_number,
    $gender,
    $user_id
]);

if ($result) {
    header("Location: " . $dashboard);
    exit();
}

$_SESSION['profile_error'] = "Failed to update profile: " . pg_last_error($conn);
header("Location: ../client_side/edit_profile.php");
exit();
?>