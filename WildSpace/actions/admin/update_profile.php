<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../client_side/login.php");
    exit();
}

if (!isset($_POST['update_profile'])) {
    header("Location: ../../client_side/edit_profile.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$mobile_number = trim($_POST['mobile_number'] ?? '');
$gender = trim($_POST['gender'] ?? '');

/*
    Email is intentionally NOT included here.
    Even if the email is sent from the form, this backend will not update it.
*/

if (!empty($mobile_number) && !preg_match('/^[0-9]{11}$/', $mobile_number)) {
    echo "Mobile number must contain exactly 11 digits.";
    exit();
}

$allowed_genders = ['', 'Male', 'Female', 'Prefer not to say'];

if (!in_array($gender, $allowed_genders)) {
    echo "Invalid gender selected.";
    exit();
}

/*
    Get the user_id connected to the logged-in admin_id.
    This makes sure the update applies only to the current admin account.
*/
$findAdmin = "SELECT user_id FROM tbladmin WHERE admin_id = $1";
$adminResult = pg_query_params($conn, $findAdmin, [$admin_id]);

if (!$adminResult) {
    echo "Database error: " . pg_last_error($conn);
    exit();
}

if (pg_num_rows($adminResult) == 0) {
    echo "Admin account not found.";
    exit();
}

$admin = pg_fetch_assoc($adminResult);
$user_id = $admin['user_id'];

/*
    Update only the editable profile fields in tbluser.
    Email and password are not updated here.
*/
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
    header("Location: ../../client_side/admin_reservations.php");
    exit();
} else {
    echo "Failed to update profile: " . pg_last_error($conn);
    exit();
}
?>