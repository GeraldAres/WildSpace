<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['send_message'])) {
    header('Location: ../client_side/contact.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    $_SESSION['contact_message'] = 'All fields are required. Please fill in the form completely.';
    $_SESSION['contact_message_type'] = 'error';
    header('Location: ../client_side/contact.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_message'] = 'Please provide a valid email address.';
    $_SESSION['contact_message_type'] = 'error';
    header('Location: ../client_side/contact.php');
    exit();
}

$to = 'geraldbeyou@gmail.com';
$subjectLine = 'WildSpace Contact Message: ' . substr($subject, 0, 78);
$body = "Name: {$name}\n" .
        "Email: {$email}\n\n" .
        "Message:\n{$message}\n";
$body = wordwrap($body, 70);

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: WildSpace Contact Form <no-reply@wildspace.com>';
$headers[] = 'Reply-To: ' . filter_var($email, FILTER_SANITIZE_EMAIL);

$sent = mail($to, $subjectLine, $body, implode("\r\n", $headers));

if ($sent) {
    $_SESSION['contact_message'] = 'Message sent successfully. I will respond to you as soon as possible.';
    $_SESSION['contact_message_type'] = 'success';
} else {
    $_SESSION['contact_message'] = 'Unable to send your message right now. Please try again later.';
    $_SESSION['contact_message_type'] = 'error';
}

header('Location: ../client_side/contact.php');
exit();
