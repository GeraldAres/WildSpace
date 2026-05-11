<?php
include '../database/connection.php';

if (isset($_POST['register'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check empty fields
    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($gender) ||
        empty($mobile_number) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {
        echo "Please fill out all fields.";
        exit();
    }

    // Institutional email validation
    if (!str_ends_with($email, '@cit.edu')) {
        echo "Only institutional emails ending with @cit.edu are allowed.";
        exit();
    }

    // Mobile number validation
    if (!preg_match('/^[0-9]{11}$/', $mobile_number)) {
        echo "Mobile number must contain exactly 11 digits.";
        exit();
    }

    // Gender validation
    $allowed_genders = ['Male', 'Female', 'Prefer not to say'];

    if (!in_array($gender, $allowed_genders)) {
        echo "Invalid gender selected.";
        exit();
    }

    // Confirm password validation
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    // Password strength validation
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        exit();
    }

    // Check if email already exists
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

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user first
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
        echo "User registration failed: " . pg_last_error($conn);
        exit();
    }

    // Get newly inserted user_id
    $user = pg_fetch_assoc($userResult);
    $user_id = $user['user_id'];

    // Insert into tbladmin because the registered account is an admin
    $insertAdmin = "INSERT INTO tbladmin (user_id) VALUES ($1)";
    $adminResult = pg_query_params($conn, $insertAdmin, [$user_id]);

    if ($adminResult) {
        header("Location: ../test_screens/login.php");
        exit();
    } else {
        echo "Admin registration failed: " . pg_last_error($conn);
        exit();
    }
} else {
    header("Location: ../test_screens/register.php");
    exit();
}
?>