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

include '../database/edit_profile_database.php';

if (isset($_SESSION['profile_success'])) {
    $_SESSION['popup_success'] = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

if (isset($_SESSION['profile_error'])) {
    $_SESSION['popup_error'] = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

$isLoggedIn = isset($_SESSION['user_id'], $_SESSION['role']);
$profileName = '';
$profileInitial = 'P';
if ($isLoggedIn) {
    $profileName = trim(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));
    if ($profileName === '') {
        $profileName = $_SESSION['email'] ?? 'Profile';
    }
    $profileInitial = strtoupper(substr($profileName, 0, 1));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/edit-profile.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/popup.php'; ?>
<nav class="navbar">
    <a href="<?php echo htmlspecialchars($dashboard); ?>" class="nav-link">
        Back to Dashboard
    </a>

    <div class="logo">
        <h1 class="logo-text">WildSpace</h1>
    </div>

</nav>

<main class="page-wrapper">
    <h1>Edit Profile</h1>
    <p class="subtitle">Update your <?php echo strtolower($roleLabel); ?> account information.</p>


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
      id="deleteAccountForm">

    <input type="hidden" name="delete_account" value="1">

    <div class="delete-action-row">
        <button type="button" class="delete-account-btn" onclick="openDeletePopup()">
            Delete Account
        </button>
    </div>
</form>
<?php } ?>

    </div>
</main>
<script src="../assets/js/edit-profile.js"></script>

<div class="confirm-overlay" id="deleteConfirmPopup">
    <div class="confirm-card">
        <button type="button" class="confirm-x" onclick="closeDeletePopup()">
            &times;
        </button>

        <div class="confirm-icon">
            <i class="fas fa-exclamation"></i>
        </div>

        <h2>Are you sure?</h2>

        <p>
            Are you sure you want to delete your account?
            <br>
            This action cannot be undone.
        </p>

        <div class="confirm-actions">
            <button type="button" class="confirm-delete" onclick="submitDeleteAccount()">
                Delete
            </button>

            <button type="button" class="confirm-cancel" onclick="closeDeletePopup()">
                Cancel
            </button>
        </div>
    </div>
</div>
</body>
</html>