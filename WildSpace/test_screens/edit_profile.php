<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

$sql = "SELECT 
            a.admin_id,
            a.user_id,
            u.firstname,
            u.lastname,
            u.email,
            u.mobile_number,
            u.gender
        FROM tbladmin a
        INNER JOIN tbluser u ON a.user_id = u.user_id
        WHERE a.admin_id = $1";

$result = pg_query_params($conn, $sql, [$admin_id]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($result) == 0) {
    echo "Admin profile not found.";
    exit();
}

$admin = pg_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Edit Profile</title>

    <style>
        :root {
            --black: #000000;
            --white: #ffffff;
            --gray: #f5f5f5;
            --text-gray: #666666;
            --border: #e5e5e5;
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
    </style>
</head>
<body>

<nav class="navbar">
    <a href="admin_reservations.php" class="nav-link">Back to Dashboard</a>
    <div class="logo-text">WildSpace</div>
    <div></div>
</nav>

<main class="page-wrapper">
    <h1>Edit Profile</h1>
    <p class="subtitle">Update your admin account information. Changes will be saved directly to the database.</p>

    <div class="form-card">
        <div class="account-info">
            Admin ID: <?php echo htmlspecialchars($admin['admin_id']); ?> |
            User ID: <?php echo htmlspecialchars($admin['user_id']); ?>
        </div>

        <form action="../actions/admin/update_profile.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input 
                        type="text" 
                        name="firstname" 
                        value="<?php echo htmlspecialchars($admin['firstname'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input 
                        type="text" 
                        name="lastname" 
                        value="<?php echo htmlspecialchars($admin['lastname'] ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input 
                    type="email" 
                    value="<?php echo htmlspecialchars($admin['email']); ?>"
                    disabled
                    class="readonly-input"
                >

                <input 
                    type="hidden" 
                    name="email" 
                    value="<?php echo htmlspecialchars($admin['email']); ?>"
                >
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input 
                    type="text" 
                    name="mobile_number" 
                    maxlength="11"
                    value="<?php echo htmlspecialchars($admin['mobile_number'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="">Select gender</option>
                    <option value="Male" <?php if (($admin['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if (($admin['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Prefer not to say" <?php if (($admin['gender'] ?? '') == 'Prefer not to say') echo 'selected'; ?>>Prefer not to say</option>
                </select>
            </div>

            <div class="button-row">
                <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
                <a href="admin_reservations.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>