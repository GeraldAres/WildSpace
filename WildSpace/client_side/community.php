<?php
session_start();

include __DIR__ . '/../database/connection.php';

$isLoggedIn = isset($_SESSION['user_id'], $_SESSION['student_id']) && $_SESSION['role'] === 'student';
$profileName = '';
$profileInitial = 'P';
if ($isLoggedIn) {
    $profileName = trim(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));
    if ($profileName === '') {
        $profileName = $_SESSION['email'] ?? 'Profile';
    }
    $profileInitial = strtoupper(substr($profileName, 0, 1));
}
$student_id = $isLoggedIn ? $_SESSION['student_id'] : null;
$message = $_SESSION['community_message'] ?? '';
$messageType = $_SESSION['community_message_type'] ?? '';
unset($_SESSION['community_message'], $_SESSION['community_message_type']);

function formatReadableDate(?string $date): string
{
    if (empty($date)) {
        return 'Not available';
    }

    return date('F j, Y', strtotime($date));
}

$reservationSql = "SELECT reservation_id, space_type, reservation_date, capacity, status
                   FROM tblreservation
                   WHERE student_id = $1
                   ORDER BY reservation_date DESC";
$reservations = [];

if ($isLoggedIn) {
    $reservationResult = pg_query_params($conn, $reservationSql, [$student_id]);
    if ($reservationResult) {
        while ($reservation = pg_fetch_assoc($reservationResult)) {
            $reservations[] = $reservation;
        }
    }
}

$postSql = "SELECT
                p.post_id,
                p.student_id,
                p.reservation_id,
                p.rating,
                p.content,
                p.created_at,
                r.space_type,
                r.reservation_date,
                r.capacity,
                u.firstname,
                u.lastname
            FROM tblcommunity_post p
            INNER JOIN tblreservation r ON p.reservation_id = r.reservation_id
            INNER JOIN tblstudent s ON p.student_id = s.student_id
            INNER JOIN tbluser u ON s.user_id = u.user_id
            ORDER BY p.created_at DESC";

$postResult = pg_query($conn, $postSql);
$posts = [];
$feedError = false;

if ($postResult) {
    while ($post = pg_fetch_assoc($postResult)) {
        $posts[] = $post;
    }
} else {
    $feedError = true;
}

$commentsByPost = [];
$commentSql = "SELECT
                    c.comment_id,
                    c.post_id,
                    c.student_id,
                    c.comment_text,
                    c.created_at,
                    u.firstname,
                    u.lastname
                FROM tblcommunity_comment c
                INNER JOIN tblstudent s ON c.student_id = s.student_id
                INNER JOIN tbluser u ON s.user_id = u.user_id
                ORDER BY c.created_at ASC";

$commentResult = pg_query($conn, $commentSql);

if ($commentResult) {
    while ($comment = pg_fetch_assoc($commentResult)) {
        $commentsByPost[$comment['post_id']][] = $comment;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Community Threads</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/community.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>
<body>
<?php include __DIR__ . '/popup.php'; ?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <a href="index.php" class="nav-link">Home</a>
            <a href="landingPage.php" class="nav-link">About Us</a>
        </div>
        <div class="logo">
            <h1 class="logo-text">WildSpace</h1>
        </div>
        <div class="nav-right">
            <div class="nav-actions">
                <a href="community.php" class="nav-link">Community</a>

                <?php if ($isLoggedIn) { ?>
                    <a href="student_dashboard.php" class="nav-link">My Dashboard</a>
                <?php } ?>

                <button class="cta-button" onclick="location.href='contact.php'">Contact Us</button>
            </div>
            <div class="account-area">
                <div class="profile-dropdown">
                    <button type="button" class="profile-button">
                        <span class="profile-avatar">
                            <?php if ($isLoggedIn) { echo htmlspecialchars($profileInitial); } else { ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path fill-rule="evenodd" d="M8 9a5 5 0 0 0-5 5v.5A.5.5 0 0 0 3.5 15h9a.5.5 0 0 0 .5-.5V14a5 5 0 0 0-5-5z"/></svg>
                            <?php } ?>
                        </span>
                    </button>
                    <div class="profile-menu">
                        <?php if ($isLoggedIn) { ?>
                            <a href="student_dashboard.php">Dashboard</a>
                            <a href="../actions/logout.php">Log Out</a>
                        <?php } else { ?>
                            <a href="login.php">Log In</a>
                            <a href="register.php">Register</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="community-page">
    <section class="community-hero">
        <div class="community-hero-copy">
            <span class="eyebrow">Cebu Institute of Technology - University</span>
            <h1>Connect with fellow students.</h1>
            <p class="hero-subtitle">Browse community threads, posts, and comments even without an account. Sign in to post or join the discussion.</p>
            <p class="hero-note">Every post must include a booked study space rating before it can be published.</p>
        </div>
    </section>

    <section class="community-grid">
        <div class="community-sidebar">
            <div class="community-card">
                <h2>Community Rules</h2>
                <ul>
                    <li>Must be signed in to post or comment.</li>
                    <li>Every post must select a booked study space.</li>
                    <li>Keep discussions respectful and study-focused.</li>
                </ul>
            </div>

            <div class="community-card">
                <h2>Your Past Reservations</h2>
                <?php if (!$isLoggedIn) { ?>
                    <p class="muted">Log in to see your past bookings and post a study space review.</p>
                <?php } elseif (count($reservations) === 0) { ?>
                    <p class="muted">Make a booking first, then return to rate your study spaces.</p>
                <?php } else { ?>
                    <ul class="reservation-list">
                        <?php foreach ($reservations as $reservation) { ?>
                            <li>
                                <strong><?php echo htmlspecialchars($reservation['space_type']); ?></strong>
                                <span><?php echo htmlspecialchars(formatReadableDate($reservation['reservation_date'])); ?></span>
                                <span><?php echo htmlspecialchars($reservation['capacity']); ?> seats</span>
                                <span class="reservation-status"><?php echo htmlspecialchars($reservation['status']); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>

        <div class="community-content">
            <?php if (!empty($message)) { ?>
                <div class="notification <?php echo $messageType === 'success' ? 'notification-success' : 'notification-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php } ?>

            <section class="post-card">
                <h2>Share a Study Space Review</h2>
                <p>Choose one of your bookings, rate the space, and start a discussion.</p>

                <?php if ($isLoggedIn) { ?>
                    <form action="../actions/community_post_action.php" method="POST" class="community-form">
                        <div class="form-group">
                            <label for="reservationId">Select Reservation</label>
                            <select name="reservation_id" id="reservationId" required>
                                <option value="" disabled selected>Pick a booked study space</option>
                                <?php foreach ($reservations as $reservation) { ?>
                                    <option value="<?php echo htmlspecialchars($reservation['reservation_id']); ?>">
                                        <?php echo htmlspecialchars($reservation['space_type'] . ' - ' . formatReadableDate($reservation['reservation_date']) . ' (' . $reservation['status'] . ')'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="rating">Study Space Rating</label>
                            <select name="rating" id="rating" required>
                                <option value="" disabled selected>Rate your booked space</option>
                                <option value="5">5 – Excellent</option>
                                <option value="4">4 – Very Good</option>
                                <option value="3">3 – Good</option>
                                <option value="2">2 – Fair</option>
                                <option value="1">1 – Needs Improvement</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="content">Post Content</label>
                            <textarea name="content" id="content" rows="5" placeholder="Share your experience, ask questions, or highlight a study tip..." required></textarea>
                        </div>

                        <button type="submit" class="cta-button">Post Review</button>
                    </form>
                <?php } else { ?>
                    <div class="community-alert">
                        <p>Browse all posts and comments for free. You must <a href="login.php">log in</a> or <a href="register.php">register</a> to publish a review.</p>
                    </div>
                <?php } ?>
            </section>

            <section class="feed-card">
                <div class="feed-header">
                    <h2>Latest Community Posts</h2>
                    <p>Students can comment and discuss study spaces after they register and book.</p>
                </div>

                <?php if ($feedError) { ?>
                    <div class="notification notification-error">
                        Community feed is not available at the moment. Please try again later.
                    </div>
                <?php } elseif (count($posts) === 0) { ?>
                    <div class="notification notification-info">
                        No posts yet. Be the first student to share a review from your booking.
                    </div>
                <?php } ?>

                <?php foreach ($posts as $post) { ?>
                    <article class="feed-post">
                        <div class="post-header">
                            <div>
                                <h3><?php echo htmlspecialchars(trim($post['firstname'] . ' ' . $post['lastname'])); ?></h3>
                                <span class="post-meta"><?php echo htmlspecialchars(formatReadableDate($post['reservation_date'])); ?> · <?php echo htmlspecialchars($post['space_type']); ?></span>
                            </div>
                            <div class="post-rating">Rating: <?php echo htmlspecialchars($post['rating']); ?>/5</div>
                        </div>

                        <div class="post-body">
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        </div>

                        <div class="post-footer">
                            <span><?php echo count($commentsByPost[$post['post_id']] ?? []); ?> comments</span>
                        </div>

                        <div class="comments-list">
                            <?php foreach ($commentsByPost[$post['post_id']] ?? [] as $comment) { ?>
                                <div class="comment-item">
                                    <strong><?php echo htmlspecialchars(trim($comment['firstname'] . ' ' . $comment['lastname'])); ?></strong>
                                    <span class="comment-date"><?php echo htmlspecialchars(formatReadableDate($comment['created_at'])); ?></span>
                                    <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                </div>
                            <?php } ?>
                        </div>

                        <?php if ($isLoggedIn) { ?>
                            <form action="../actions/community_comment_action.php" method="POST" class="comment-form">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['post_id']); ?>">
                                <textarea name="comment_text" rows="2" placeholder="Add a comment..." required></textarea>
                                <button type="submit" class="comment-submit">Comment</button>
                            </form>
                        <?php } else { ?>
                            <div class="comment-prompt">
                                <p><a href="login.php">Log in</a> to reply to this post.</p>
                            </div>
                        <?php } ?>
                    </article>
                <?php } ?>
            </section>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <h1 class="logo-text">WildSpace</h1>
            <p>Study without the hassle</p>
        </div>
        <div class="footer-content">
            <p class="footer-contact">Contact: +63 XXX XXX XXXX</p>
            <p class="footer-copyright">&copy; 2024 WildSpace. All rights reserved.</p>
        </div>
    </div>
</footer>
</body>
</html>
<script src="../assets/js/script.js"></script>
