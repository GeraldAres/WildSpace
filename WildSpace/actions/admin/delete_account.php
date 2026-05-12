<?php
session_start();
include '../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: ../../test_screens/login.php");
    exit();
}

if (!isset($_POST['delete_account'])) {
    header("Location: ../../test_screens/admin_reservations.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

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

$deleteUser = "DELETE FROM tbluser WHERE user_id = $1";
$deleteResult = pg_query_params($conn, $deleteUser, [$user_id]);

if ($deleteResult) {
    session_unset();
    session_destroy();

    header("Location: ../../test_screens/login.php");
    exit();
} else {
    echo "Failed to delete account: " . pg_last_error($conn);
    exit();
}
?>