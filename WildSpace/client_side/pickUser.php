<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Register</title>
    <link rel="stylesheet" href="../assets/css/pickUser.css">
</head>
<body>

<div class="container">

    <div class="card">

        <h2>Create Account</h2>
        <p class="subtitle">Choose your account type</p>

        <form action="register_action.php" method="POST">

            <!-- Role Selection -->
            <input type="hidden" name="role" id="roleInput" required>

            <div class="role-container">

                <div class="role-card" onclick="selectRole('student')" id="studentCard">
                    <h3>Student</h3>
                    <p>Book study spaces</p>
                </div>

                <div class="role-card" onclick="selectRole('admin')" id="adminCard">
                    <h3>Admin</h3>
                    <p>Manage reservations</p>
                </div>

            </div>

            <!-- User Info -->
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Create Account</button>

        </form>

    </div>

</div>

<script src="../assets/js/pickUser.js"></script>
</body>
</html>