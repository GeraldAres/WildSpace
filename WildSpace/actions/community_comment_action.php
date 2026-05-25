<?php
session_start();

include '../database/connection.php';

if (!isset($_POST['post_id'], $_POST['comment_text'])) {
    header('Location: ../client_side/community.php');
    exit();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../client_side/login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$post_id = trim($_POST['post_id']);
$comment_text = trim($_POST['comment_text']);

if (empty($post_id) || empty($comment_text)) {
    $_SESSION['community_message'] = 'Comment cannot be empty.';
    $_SESSION['community_message_type'] = 'error';
    header('Location: ../client_side/community.php');
    exit();
}

$postCheckSql = "SELECT post_id FROM tblcommunity_post WHERE post_id = $1";
$postCheckResult = pg_query_params($conn, $postCheckSql, [$post_id]);

if (!$postCheckResult || pg_num_rows($postCheckResult) === 0) {
    $_SESSION['community_message'] = 'The selected post no longer exists.';
    $_SESSION['community_message_type'] = 'error';
    header('Location: ../client_side/community.php');
    exit();
}

$insertSql = "INSERT INTO tblcommunity_comment (post_id, student_id, comment_text, created_at)
              VALUES ($1, $2, $3, NOW())";

$result = pg_query_params($conn, $insertSql, [$post_id, $student_id, $comment_text]);

if ($result) {
    $_SESSION['community_message'] = 'Your comment was posted successfully.';
    $_SESSION['community_message_type'] = 'success';
} else {
    $_SESSION['community_message'] = 'Unable to post your comment at this time.';
    $_SESSION['community_message_type'] = 'error';
}

header('Location: ../client_side/community.php');
exit();
