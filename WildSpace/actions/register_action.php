<?php
include '../database/connection.php';

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

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

    $checkEmail = "SELECT * FROM tbluser WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkEmail);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "Email already exists.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $insertUser = "INSERT INTO tbluser (email, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertUser);
    mysqli_stmt_bind_param($stmt, "ss", $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);

        $insertAdmin = "INSERT INTO tbladmin (user_id) VALUES (?)";
        $stmtAdmin = mysqli_prepare($conn, $insertAdmin);
        mysqli_stmt_bind_param($stmtAdmin, "i", $user_id);

        if (mysqli_stmt_execute($stmtAdmin)) {
            header("Location: ../test_screens/login.php");
            exit();
        } else {
            echo "Admin registration failed: " . mysqli_error($conn);
        }
    } else {
        echo "User registration failed: " . mysqli_error($conn);
    }
}
?>