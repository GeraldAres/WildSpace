<?php
include '../database/connection.php';

if (isset($_POST['register'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');

    if (
        empty($email) ||
        empty($password) ||
        empty($confirm_password) ||
        empty($firstname) ||
        empty($lastname) ||
        empty($gender) ||
        empty($mobile_number)
    ) {
        echo "Please fill out all fields.";
        exit();
    }

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

    $allowed_genders = ['Male', 'Female', 'Prefer not to say'];

    if (!in_array($gender, $allowed_genders)) {
        echo "Invalid gender selected.";
        exit();
    }

    if (!preg_match('/^[0-9]{11}$/', $mobile_number)) {
        echo "Mobile number must contain exactly 11 digits.";
        exit();
    }

    $checkEmail = "SELECT user_id FROM tbluser WHERE email = $1";
    $emailResult = pg_query_params($conn, $checkEmail, [$email]);

    if (!$emailResult) {
        echo "Database error: " . pg_last_error($conn);
        exit();
    }

    if (pg_num_rows($emailResult) > 0) {
        echo "Email already exists.";
        exit();
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
        echo "User registration failed: " . pg_last_error($conn);
        exit();
    }

    $user = pg_fetch_assoc($userResult);
    $user_id = $user['user_id'];

    $insertStudent = "INSERT INTO tblstudent (user_id) VALUES ($1)";

    $studentResult = pg_query_params($conn, $insertStudent, [$user_id]);

    if (!$studentResult) {
        pg_query($conn, "ROLLBACK");
        echo "Student registration failed: " . pg_last_error($conn);
        exit();
    }

    pg_query($conn, "COMMIT");

    header("Location: ../client_side/login.php");
    exit();

} else {
    header("Location: ../client_side/register.php");
    exit();
}
?>