<?php
session_start();

include '../database/connection.php';

if (!isset($_POST['reservation_id'], $_POST['rating'], $_POST['content'])) {
    header('Location: ../client_side/community.php');
    exit();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../client_side/login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$reservation_id = trim($_POST['reservation_id']);
$rating = trim($_POST['rating']);
$content = trim($_POST['content']);

if (empty($reservation_id) || empty($rating) || empty($content)) {
    $_SESSION['community_message'] = 'Please select a reservation, rating, and post content.';
    $_SESSION['community_message_type'] = 'error';
    header('Location: ../client_side/community.php');
    exit();
}

$reservationCheckSql = "SELECT reservation_id FROM tblreservation WHERE reservation_id = $1 AND student_id = $2";
$reservationCheckResult = pg_query_params($conn, $reservationCheckSql, [$reservation_id, $student_id]);

if (!$reservationCheckResult || pg_num_rows($reservationCheckResult) === 0) {
    $_SESSION['community_message'] = 'Invalid reservation selected. Please choose one of your bookings.';
    $_SESSION['community_message_type'] = 'error';
    header('Location: ../client_side/community.php');
    exit();
}

$insertSql = "INSERT INTO tblcommunity_post (student_id, reservation_id, rating, content, created_at)
              VALUES ($1, $2, $3, $4, NOW())";

$result = pg_query_params($conn, $insertSql, [$student_id, $reservation_id, $rating, $content]);

if ($result) {
    $_SESSION['community_message'] = 'Your review post has been published.';
    $_SESSION['community_message_type'] = 'success';
} else {
    $_SESSION['community_message'] = 'Unable to publish your post at this time. Please try again later.';
    $_SESSION['community_message_type'] = 'error';
}

header('Location: ../client_side/community.php');
exit();
