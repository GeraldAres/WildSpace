<?php
include '../database/connection.php';



header('Content-Type: application/json');



function registerResponse(bool $success, string $message): void

{

    echo json_encode(['success' => $success, 'message' => $message]);

    exit();

}



if (!isset($_POST['register'])) {

    registerResponse(false, 'Invalid request.');

}



$email = trim($_POST['email'] ?? '');

$password = $_POST['password'] ?? '';

$confirm_password = $_POST['confirm_password'] ?? '';



$firstname = trim($_POST['firstname'] ?? '');

$lastname = trim($_POST['lastname'] ?? '');

$gender = trim($_POST['gender'] ?? '');

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        exit();
    }

if (

    empty($email) ||

    empty($password) ||

    if (!preg_match('/^[0-9]{11}$/', $mobile_number)) {
        echo "Mobile number must contain exactly 11 digits.";
        exit();
    }

    empty($lastname) ||

    empty($gender) ||

    empty($mobile_number)

) {
    if (pg_num_rows($emailResult) > 0) {
        echo "Email already exists.";
        exit();
    }
}



if (!str_ends_with($email, '@cit.edu')) {

    registerResponse(false, 'Only institutional emails ending with @cit.edu are allowed.');

}



if ($password !== $confirm_password) {

    registerResponse(false, 'Passwords do not match.');

}



if (strlen($password) < 8) {

    registerResponse(false, 'Password must be 8 characters long');

}



$allowed_genders = ['Male', 'Female', 'Prefer not to say'];



if (!in_array($gender, $allowed_genders)) {

    registerResponse(false, 'Invalid gender selected.');

}




    registerResponse(false, 'Mobile number must contain exactly 11 digits.');

}



$checkEmail = "SELECT user_id FROM tbluser WHERE email = $1";

$emailResult = pg_query_params($conn, $checkEmail, [$email]);



if (!$emailResult) {

    registerResponse(false, 'Database error. Please try again later.');

}



if (pg_num_rows($emailResult) > 0) {

    registerResponse(false, 'Email already exists.');

}



$hashed_password = password_hash($password, PASSWORD_DEFAULT);



pg_query($conn, "BEGIN");



$insertUser = "INSERT INTO tbluser 

                (firstname, lastname, email, password, mobile_number, gender)

               VALUES 

                ($1, $2, $3, $4, $5, $6)

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

    registerResponse(false, 'User registration failed. Please try again later.');

}



    $user = pg_fetch_assoc($userResult);
    $user_id = $user['user_id'];



$insertRole = "INSERT INTO tblstudent (user_id) VALUES ($1)";

$roleResult = pg_query_params($conn, $insertRole, [$user_id]);



if (!$roleResult) {

    pg_query($conn, "ROLLBACK");

    registerResponse(false, 'Role registration failed. Please try again later.');

}



pg_query($conn, "COMMIT");



registerResponse(true, 'Created account successfully');

?>

