<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'admin') {
    $role_id = $_SESSION['admin_id'] ?? null;
    $dashboard = "admin_dashboard.php";
    $roleLabel = "Admin";
} elseif ($role === 'student') {
    $role_id = $_SESSION['student_id'] ?? null;
    $dashboard = "student_dashboard.php";
    $roleLabel = "Student";
} else {
    header("Location: login.php");
    exit();
}

if (!$role_id) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT 
            user_id,
            firstname,
            lastname,
            email,
            mobile_number,
            gender
        FROM tbluser
        WHERE user_id = $1";

$result = pg_query_params($conn, $sql, [$user_id]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($result) == 0) {
    echo "Profile not found.";
    exit();
}

$user = pg_fetch_assoc($result);

$success = $_SESSION['profile_success'] ?? '';
$error = $_SESSION['profile_error'] ?? '';

unset($_SESSION['profile_success']);
unset($_SESSION['profile_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Edit Profile</title>

    <style>
        .delete-account-btn {
    background: #b42318;
    color: #fff;
    border: none;
    padding: 13px 28px;
    border-radius: 999px;
    font-weight: 700;
    cursor: pointer;
}
        :root {
            --black: #000000;
            --white: #ffffff;
            --gray: #f5f5f5;
            --text-gray: #666666;
            --border: #e5e5e5;
            --green-bg: #e8f7ee;
            --green: #0f8a3b;
            --red-bg: #fdecec;
            --red: #b42318;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--white);
            color: var(--black);
        }

        .navbar {
            width: 100%;
            padding: 24px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background: var(--white);
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--black);
            font-size: 15px;
            font-weight: 600;
        }

        .page-wrapper {
            max-width: 760px;
            margin: 70px auto;
            padding: 0 24px;
        }

        h1 {
            font-size: 44px;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: var(--text-gray);
            margin-bottom: 35px;
            font-size: 16px;
        }

        .form-card {
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 36px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.06);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input,
        select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #dddddd;
            border-radius: 14px;
            font-size: 15px;
            font-family: inherit;
        }

        select {
            padding-right: 48px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='14' height='14' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5 7.5L10 12.5L15 7.5' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 14px;
        }

        .readonly-input {
            background: #f5f5f5;
            color: #666666;
            cursor: not-allowed;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--black);
        }

        .button-row {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .save-btn {
            background: var(--black);
            color: var(--white);
            border: none;
            padding: 13px 28px;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
        }

        .cancel-btn {
            background: var(--gray);
            color: var(--black);
            text-decoration: none;
            padding: 13px 28px;
            border-radius: 999px;
            font-weight: 700;
        }

        .account-info {
            background: var(--gray);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 24px;
            color: var(--text-gray);
            font-size: 14px;
        }

        .message {
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .success {
            background: var(--green-bg);
            color: var(--green);
        }

        .error {
            background: var(--red-bg);
            color: var(--red);
        }

        @media (max-width: 700px) {
            .navbar {
                padding: 20px;
            }

            .page-wrapper {
                margin: 40px auto;
            }

            h1 {
                font-size: 34px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-card {
                padding: 24px;
            }
        }

        .profile-actions,
.delete-action-row {
    display: flex;
    align-items: center;
}

.left-actions {
    display: flex;
    gap: 12px;
}

.delete-action-row {
    justify-content: flex-end;
    margin-top: -43px;
}
    </style>
</head>
<body>

<nav class="navbar">
    <a href="<?php echo htmlspecialchars($dashboard); ?>" class="nav-link">Back to Dashboard</a>
    <div class="logo-text">WildSpace</div>
    <div></div>
</nav>

<main class="page-wrapper">
    <h1>Edit Profile</h1>
    <p class="subtitle">Update your <?php echo strtolower($roleLabel); ?> account information.</p>



    <?php if (!empty($error)) { ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <div class="form-card">
        <div class="account-info">
            <?php echo htmlspecialchars($roleLabel); ?> ID: <?php echo htmlspecialchars($role_id); ?> |
            User ID: <?php echo htmlspecialchars($user['user_id']); ?>
        </div>

        <form action="../actions/admin/update_profile.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input 
                        type="text" 
                        name="firstname" 
                        value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input 
                        type="text" 
                        name="lastname" 
                        value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input 
                    type="email" 
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    disabled
                    class="readonly-input"
                >
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input 
                    type="text" 
                    name="mobile_number" 
                    maxlength="11"
                    value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="">Select gender</option>
                    <option value="Male" <?php if (($user['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if (($user['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Prefer not to say" <?php if (($user['gender'] ?? '') == 'Prefer not to say') echo 'selected'; ?>>Prefer not to say</option>
                </select>
            </div>

<div class="button-row profile-actions">
    <div class="left-actions">
        <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
        <a href="<?php echo htmlspecialchars($dashboard); ?>" class="cancel-btn">Cancel</a>
    </div>
</div>
</form>

<?php if ($role === 'student') { ?>
    <form action="../actions/student/delete_account.php"
          method="POST"
          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">

        <div class="delete-action-row">
            <button type="submit" name="delete_account" class="delete-account-btn">
                Delete Account
            </button>
        </div>
    </form>
<?php } ?>

    </div>
</main>

</body>
</html>