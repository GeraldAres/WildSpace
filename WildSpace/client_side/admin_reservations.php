<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$view = $_GET['view'] ?? 'students';
if (!in_array($view, ['students', 'requests'], true)) {
    $view = 'students';
}

/* ================= STUDENTS ================= */
$studentsSql = "SELECT
        s.student_id,
        s.user_id,
        u.firstname,
        u.lastname,
        u.email,
        u.mobile_number,
        u.gender,
        (SELECT COUNT(*) FROM tblreservation r WHERE r.student_id = s.student_id) AS reservation_count
    FROM tblstudent s
    INNER JOIN tbluser u ON s.user_id = u.user_id
    ORDER BY u.firstname ASC, u.lastname ASC";

$studentsResult = pg_query($conn, $studentsSql);

if (!$studentsResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$students = [];
$studentTotal = 0;

while ($row = pg_fetch_assoc($studentsResult)) {
    $students[] = $row;
    $studentTotal++;
}

/* ================= RESERVATION REQUESTS ================= */
$requestsSql = "SELECT
        r.reservation_id,
        r.student_id,
        r.admin_id,
        r.status,
        r.reservation_date,
        r.capacity,
        r.date_created,
        s.user_id,
        u.firstname,
        u.lastname,
        u.email,
        u.mobile_number
    FROM tblreservation r
    INNER JOIN tblstudent s ON r.student_id = s.student_id
    INNER JOIN tbluser u ON s.user_id = u.user_id
    ORDER BY r.date_created DESC";

$requestsResult = pg_query($conn, $requestsSql);

if (!$requestsResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$requests = [];
$total = 0;
$pending = 0;
$approved = 0;
$rejected = 0;

while ($row = pg_fetch_assoc($requestsResult)) {
    $requests[] = $row;
    $total++;

    if ($row['status'] === 'Pending') {
        $pending++;
    } elseif ($row['status'] === 'Approved') {
        $approved++;
    } elseif ($row['status'] === 'Rejected') {
        $rejected++;
    }
}

function formatReadableDate(?string $date): string
{
    if (empty($date)) {
        return "Not available";
    }

    return date("F j, Y", strtotime($date));
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">

    
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="logo-text">WildSpace</div>
            <p>Admin Dashboard</p>
        </div>

        <nav class="sidebar-nav" aria-label="Admin navigation">
            <a href="?view=students"
               class="sidebar-link <?php echo $view === 'students' ? 'active' : ''; ?>"
               data-panel="students">
                <i class="fas fa-users"></i>
                Student Reservation
            </a>

            <a href="?view=requests"
               class="sidebar-link <?php echo $view === 'requests' ? 'active' : ''; ?>"
               data-panel="requests">
                <i class="fas fa-calendar-check"></i>
                Student Request
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="edit_profile.php" class="sidebar-link">
                <i class="fas fa-user-pen"></i>
                Edit Profile
            </a>

            <a href="../actions/admin/logout.php"
               class="sidebar-link logout-link"
               onclick="return confirm('Are you sure you want to log out?');">
                <i class="fas fa-right-from-bracket"></i>
                Log Out
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <!-- STUDENT RESERVATION (registered students) -->
        <section id="panel-students"
                 class="admin-panel <?php echo $view === 'students' ? 'active' : ''; ?>">
            <header class="dashboard-header">
                <div class="dashboard-title">
                    <h1>Student Reservation</h1>
                    <p>View registered students and remove accounts from the system.</p>
                </div>
            </header>

            <section class="summary-cards cols-3">
                <div class="summary-card">
                    <span>Total Students</span>
                    <strong><?php echo $studentTotal; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Total Booking Records</span>
                    <strong><?php echo $total; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Pending Requests</span>
                    <strong><?php echo $pending; ?></strong>
                </div>
            </section>

            <section class="table-card">
                <div class="table-header">
                    <h2>Registered Students</h2>
                    <p>Delete a student to remove their account and reservations</p>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Bookings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) === 0) { ?>
                                <tr>
                                    <td colspan="6" class="muted">No students registered yet.</td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($students as $student) { ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td>
                                        <div class="name-text">
                                            <?php echo htmlspecialchars(trim($student['firstname'] . ' ' . $student['lastname'])); ?>
                                        </div>
                                        <div class="muted">User ID: <?php echo htmlspecialchars($student['user_id']); ?></div>
                                    </td>
                                    <td class="email-text"><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['reservation_count']); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a class="action-btn delete-btn"
                                               href="../actions/admin/admin_delete_student.php?id=<?php echo urlencode($student['student_id']); ?>"
                                               onclick="return confirm('Delete this student and all of their reservations?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>

        <!-- STUDENT REQUEST (booking approvals) -->
        <section id="panel-requests"
                 class="admin-panel <?php echo $view === 'requests' ? 'active' : ''; ?>">
            <header class="dashboard-header">
                <div class="dashboard-title">
                    <h1>Student Request</h1>
                    <p>Review booking requests from students. Approve or reject pending reservations.</p>
                </div>

            </header>

            <section class="summary-cards">
                <div class="summary-card">
                    <span>Total Reservations</span>
                    <strong><?php echo $total; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Pending Requests</span>
                    <strong><?php echo $pending; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Approved</span>
                    <strong><?php echo $approved; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Rejected</span>
                    <strong><?php echo $rejected; ?></strong>
                </div>
            </section>

            <section class="table-card">
                <div class="table-header">
                    <h2>Booking Requests</h2>
                    <p>Student booking details and approval actions</p>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($requests) === 0) { ?>
                                <tr>
                                    <td colspan="8" class="muted">No booking requests yet.</td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($requests as $row) {
                                $statusClass = reservationStatusClass($row['status']);
                                $fullName = trim($row['firstname'] . ' ' . $row['lastname']);
                                ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($row['reservation_id']); ?></td>
                                    <td>
                                        <div class="name-text"><?php echo htmlspecialchars($fullName); ?></div>
                                        <div class="muted"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td class="muted"><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                                    <td><?php echo htmlspecialchars(formatReadableDate($row['reservation_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['capacity']); ?> seats</td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="muted"><?php echo htmlspecialchars(formatReadableDate($row['date_created'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <?php if ($row['status'] === 'Pending') { ?>
                                                <a class="action-btn approve-btn"
                                                   href="../actions/admin/update_reservation_status.php?id=<?php echo urlencode($row['reservation_id']); ?>&status=Approved"
                                                   onclick="return confirm('Approve this reservation?');">
                                                    Approve
                                                </a>
                                                <a class="action-btn reject-btn"
                                                   href="../actions/admin/update_reservation_status.php?id=<?php echo urlencode($row['reservation_id']); ?>&status=Rejected"
                                                   onclick="return confirm('Reject this reservation?');">
                                                    Reject
                                                </a>
                                            <?php } else { ?>
                                                <span class="empty-action">Reviewed</span>
                                            <?php } ?>
                                            <?php if ($row['status'] === 'Approved') { ?>
                                                <a class="action-btn reject-btn"
                                                href="../actions/admin/add_violation.php?id=<?php echo urlencode($row['reservation_id']); ?>"
                                                onclick="return confirm('Mark this student as no-show and add a violation?');">
                                                    No Show
                                                </a>
                                            <?php } ?>

                                            <a class="action-btn delete-btn"
                                               href="../actions/admin/admin_delete_reservation.php?id=<?php echo urlencode($row['reservation_id']); ?>"
                                               onclick="return confirm('Delete this reservation permanently?');">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </main>
</div>

<script>
document.querySelectorAll('.sidebar-link[data-panel]').forEach((link) => {
    link.addEventListener('click', (e) => {
        const panel = link.getAttribute('data-panel');
        if (!panel) return;

        document.querySelectorAll('.admin-panel').forEach((el) => {
            el.classList.remove('active');
        });

        const target = document.getElementById('panel-' + panel);
        if (target) {
            target.classList.add('active');
        }

        document.querySelectorAll('.sidebar-link[data-panel]').forEach((el) => {
            el.classList.remove('active');
        });
        link.classList.add('active');
    });
});
</script>

</body>
</html>
