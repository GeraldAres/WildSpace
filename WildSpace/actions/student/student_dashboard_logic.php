<?php
session_start();

include __DIR__ . '/../../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../client_side/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$message = "";
$messageType = "";

include __DIR__ . '/../../database/student_dashboard_database.php';

function reservationStatusClass(string $status): string
{
    if ($status === 'Approved') {
        return 'status-approved';
    }

    if ($status === 'Rejected') {
        return 'status-rejected';
    }

    if ($status === 'Cancelled') {
        return 'status-cancelled';
    }

    return 'status-pending';
}

function formatReadableDate(?string $date): string
{
    if (empty($date)) {
        return "Not available";
    }

    return date("F d, Y", strtotime($date));
}

$view = $_GET['view'] ?? 'reservations';

if (!in_array($view, ['reservations', 'book', 'violations'])) {
    $view = 'reservations';
}
?>