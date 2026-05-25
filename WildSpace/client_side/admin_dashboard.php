<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$view = $_GET['view'] ?? 'students';
if (!in_array($view, ['students', 'requests', 'file_violation', 'violation_form', 'summary'], true)) {
    $view = 'students';
}

include __DIR__ . '/../database/admin_dashboard_database.php';

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
    <link rel="stylesheet" href="../assets/css/popup.css">
    
</head>
<body>
<?php include __DIR__ . '/popup.php'; ?>
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
            <a href="?view=file_violation"
            class="sidebar-link <?php echo $view === 'file_violation' || $view === 'violation_form' ? 'active' : ''; ?>"
            data-panel="file_violation">
                <i class="fas fa-triangle-exclamation"></i>
                File a Violation
            </a>

            <a href="?view=summary"
               class="sidebar-link <?php echo $view === 'summary' ? 'active' : ''; ?>"
               data-panel="summary">
                <i class="fas fa-chart-bar"></i>
                Student Summary
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="edit_profile.php" class="sidebar-link">
                <i class="fas fa-user-pen"></i>
                Edit Profile
            </a>

            <button type="button"
                    class="sidebar-link logout-link"
                    onclick="openLogoutPopup()">
                <i class="fas fa-right-from-bracket"></i>
                Log Out
            </button>
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
                                            <button type="button"
                                                    class="action-btn delete-btn"
                                                    onclick="openDeleteStudentPopup('../actions/admin/admin_delete_student.php?id=<?php echo urlencode($student['student_id']); ?>')">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-controls">
                    <a class="pagination-btn<?php echo $studentPage <= 1 ? ' disabled' : ''; ?>"
                       href="?view=students&page=<?php echo max(1, $studentPage - 1); ?>"
                       <?php if ($studentPage <= 1) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                        &laquo; Previous
                    </a>

                    <span class="pagination-info">
                        Page <?php echo $studentPage; ?> of <?php echo $studentPageCount; ?>
                    </span>

                    <a class="pagination-btn<?php echo $studentPage >= $studentPageCount ? ' disabled' : ''; ?>"
                       href="?view=students&page=<?php echo min($studentPageCount, $studentPage + 1); ?>"
                       <?php if ($studentPage >= $studentPageCount) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                        Next &raquo;
                    </a>
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
                                <th>Reservation ID</th>
                                <th>Student Name</th>
                                <th>Booking Date</th>
                                <th>Study Space Type</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($requests) === 0) { ?>
                                <tr>
                                    <td colspan="7" class="muted">No pending requests at this time.</td>
                                </tr>
                            <?php } ?>

                            <?php foreach ($requests as $request) { ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($request['reservation_id']); ?></td>
                                    <td>
                                        <div class="name-text">
                                            <?php echo htmlspecialchars(trim($request['firstname'] . ' ' . $request['lastname'])); ?>
                                        </div>
                                        <div class="muted">User ID: <?php echo htmlspecialchars($request['user_id']); ?></div>
                                    </td>
                                    <td><?php echo formatReadableDate($request['reservation_date']); ?></td>
                                    <td><?php echo htmlspecialchars($request['space_type'] ?? 'Not specified'); ?></td>
                                    <td><?php echo htmlspecialchars($request['capacity']); ?> people</td>
                                    <td>
                                        <span class="<?php echo reservationStatusClass($request['status']); ?>">
                                            <?php echo htmlspecialchars($request['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button type="button"
                                                    class="action-btn approve-btn"
                                                    onclick="openStatusPopup('../actions/admin/update_reservation_status.php?id=<?php echo urlencode($request['reservation_id']); ?>&status=Approved', 'Approve this reservation?')">
                                                Approve
                                            </button>

                                            <button type="button"
                                                    class="action-btn reject-btn"
                                                    onclick="openStatusPopup('../actions/admin/update_reservation_status.php?id=<?php echo urlencode($request['reservation_id']); ?>&status=Rejected', 'Reject this reservation?')">
                                                Reject
                                            </button>
                                            <button type="button"
                                                    class="action-btn delete-btn"
                                                    onclick="openDeleteReservationPopup('../actions/admin/admin_delete_reservation.php?id=<?php echo urlencode($request['reservation_id']); ?>')">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-controls">
                    <a class="pagination-btn<?php echo $requestPage <= 1 ? ' disabled' : ''; ?>"
                       href="?view=requests&request_page=<?php echo max(1, $requestPage - 1); ?>"
                       <?php if ($requestPage <= 1) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                        &laquo; Previous
                    </a>

                    <span class="pagination-info">
                        Page <?php echo $requestPage; ?> of <?php echo $requestPageCount; ?>
                    </span>

                    <a class="pagination-btn<?php echo $requestPage >= $requestPageCount ? ' disabled' : ''; ?>"
                       href="?view=requests&request_page=<?php echo min($requestPageCount, $requestPage + 1); ?>"
                       <?php if ($requestPage >= $requestPageCount) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                        Next &raquo;
                    </a>
                </div>
            </section>
        </section>

        <section id="panel-file_violation"
         class="admin-panel <?php echo $view === 'file_violation' ? 'active' : ''; ?>">
    <header class="dashboard-header">
        <div class="dashboard-title">
            <h1>File a Violation</h1>
            <p>Select an approved reservation and file a violation record.</p>
        </div>
    </header>

    <section class="table-card">
        <div class="table-header">
            <h2>Approved Reservations</h2>
            <p>Only approved reservations without existing violations are shown.</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Student Name</th>
                        <th>Study Space Type</th>
                        <th>Booking Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($approvedReservations) === 0) { ?>
                        <tr>
                            <td colspan="5" class="muted">No approved reservations available for violation filing.</td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($approvedReservations as $reservation) { ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($reservation['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars(trim($reservation['firstname'] . ' ' . $reservation['lastname'])); ?></td>
                            <td><?php echo htmlspecialchars($reservation['space_type'] ?? 'Not specified'); ?></td>
                            <td><?php echo formatReadableDate($reservation['reservation_date']); ?></td>
                            <td>
                                <a class="action-btn reject-btn"
                                   href="?view=violation_form&id=<?php echo urlencode($reservation['reservation_id']); ?>">
                                    File Violation
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<?php if ($view === 'violation_form' && isset($_GET['id'])) { ?>
<section id="panel-violation_form" class="admin-panel active">

    <header class="dashboard-header">
        <div class="dashboard-title">
            <h1>Violation Form</h1>
            <p>Record student violations related to approved reservations.</p>
        </div>
    </header>

    <div class="violation-form-card">

        <div class="violation-form-header">
            <h2>File Student Violation</h2>
            <p>
                Complete the form below to document violations committed during the reservation period.
            </p>
        </div>

        <form method="POST"
              action="../actions/admin/file_violation.php"
              class="violation-form-body">

            <input type="hidden"
                   name="reservation_id"
                   value="<?php echo htmlspecialchars($_GET['id']); ?>">

            <div class="violation-info-grid">

                <div class="violation-info-box">
                    <span>Reservation ID</span>
                    <strong>
                        #<?php echo htmlspecialchars($_GET['id']); ?>
                    </strong>
                </div>

                <div class="violation-info-box">
                    <span>Status</span>
                    <strong>Approved Reservation</strong>
                </div>

            </div>

            <div class="violation-field">
                <label>Violation Type</label>

                <select name="violation_type" required>
                    <option value="" disabled selected>
                        Select violation type
                    </option>

                    <option value="Noise Complaint">
                        Noise Complaint
                    </option>

                    <option value="Misuse of Study Space">
                        Misuse of Study Space
                    </option>

                    <option value="Damage to Property">
                        Damage to Property
                    </option>

                    <option value="Leaving Area Unclean">
                        Leaving Area Unclean
                    </option>
                </select>
            </div>

            <div class="violation-field">
                <label>Description</label>

                <textarea
                    name="description"
                    placeholder="Provide a detailed explanation of the violation..."
                    required></textarea>
            </div>

            <button type="submit"
                    name="file_violation"
                    class="violation-submit">
                Submit Violation Report
            </button>

        </form>
    </div>
</section>
<?php } ?>


        <!-- STUDENT SUMMARY (analytics and overview) -->
        <section id="panel-summary"
                 class="admin-panel <?php echo $view === 'summary' ? 'active' : ''; ?>">
            <header class="dashboard-header">
                <div class="dashboard-title">
                    <h1>Student Summary</h1>
                    <p>Live analytics and reservation overview for all students.</p>
                </div>
            </header>

            <section class="summary-cards cols-4">
                <div class="summary-card">
                    <span>Total Reservations</span>
                    <strong id="summary-total-main"><?php echo $requestTotal; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Pending Requests</span>
                    <strong id="summary-pending-main"><?php echo $pending; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Approved Requests</span>
                    <strong id="summary-approved-main"><?php echo $approved; ?></strong>
                </div>
                <div class="summary-card">
                    <span>Rejected Requests</span>
                    <strong id="summary-rejected-main"><?php echo $rejected; ?></strong>
                </div>
            </section>

            <section class="table-card analytics-card" aria-label="Reservation Analytics">
                <div class="table-header">
                    <h2>Reservation Analytics</h2>
                    <p>Detailed breakdown of all reservations and student violations.</p>
                </div>

                <div class="analytics-content">
                    <div class="analytics-panel">
                        <h3>Status Distribution</h3>
                        <div class="distribution-row">
                            <span>Approved</span>
                            <strong id="summary-approved-percent-main"><?php echo $approvedPercent; ?>%</strong>
                        </div>
                        <div class="distribution-bar-bg">
                            <div id="bar-approved-main" class="distribution-bar approved"
                                 style="width: <?php echo $approvedPercent; ?>%;"></div>
                        </div>

                        <div class="distribution-row">
                            <span>Pending</span>
                            <strong id="summary-pending-percent-main"><?php echo $pendingPercent; ?>%</strong>
                        </div>
                        <div class="distribution-bar-bg">
                            <div id="bar-pending-main" class="distribution-bar pending"
                                 style="width: <?php echo $pendingPercent; ?>%;"></div>
                        </div>

                        <div class="distribution-row">
                            <span>Rejected</span>
                            <strong id="summary-rejected-percent-main"><?php echo $rejectedPercent; ?>%</strong>
                        </div>
                        <div class="distribution-bar-bg">
                            <div id="bar-rejected-main" class="distribution-bar rejected"
                                 style="width: <?php echo $rejectedPercent; ?>%;"></div>
                        </div>
                    </div>

                    <div class="analytics-panel violations-panel">
                        <h3>Violation Overview</h3>
                        <div class="violations-stat">
                            <span class="violations-count" id="summary-violations-main"><?php echo $violationStudents; ?></span>
                            <span class="violations-label">Students with recorded violations</span>
                        </div>
                        <p class="violations-note">This number updates automatically as reservation data changes.</p>
                    </div>
                </div>
            </section>

            <?php
            $spaceTrackerMaxCount = 0;
            foreach ($spaceTracker as $trackerStats) {
                if ($trackerStats['people_count'] > $spaceTrackerMaxCount) {
                    $spaceTrackerMaxCount = $trackerStats['people_count'];
                }
            }
            if ($spaceTrackerMaxCount === 0) {
                $spaceTrackerMaxCount = 1;
            }
            ?>

            <section class="table-card analytics-card tracker-card" aria-label="Live Space Tracker">
                <div class="table-header">
                    <h2>Live Space Tracker</h2>
                    <p>Non-editable booking usage graph for each study space type.</p>
                </div>

                <div class="tracker-chart">
                    <?php foreach ($spaceTracker as $type => $stats) {
                        $width = (int) round(($stats['people_count'] / $spaceTrackerMaxCount) * 100);
                        $safeId = 'tracker-bar-' . strtolower(str_replace([' ', '/'], '-', $type));
                    ?>
                        <div class="tracker-bar-row">
                            <span class="tracker-label"><?php echo htmlspecialchars($type); ?></span>
                            <div class="tracker-bar-bg">
                                <div id="<?php echo $safeId; ?>" class="tracker-bar <?php echo htmlspecialchars(str_replace(' ', '-', strtolower($type))); ?>" style="width: <?php echo $width; ?>%;"></div>
                            </div>
                            <div class="tracker-meta">
                                <span class="tracker-value"><?php echo htmlspecialchars($stats['people_count']); ?> people</span>
                                <span><?php echo htmlspecialchars($stats['reservation_count']); ?> reservations</span>
                            </div>
                        </div>
                    <?php } ?>
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

function updateAllSummaryViews(data) {
    // Update Student Summary panel
    document.getElementById('summary-total-main').textContent = data.total;
    document.getElementById('summary-pending-main').textContent = data.pending;
    document.getElementById('summary-approved-main').textContent = data.approved;
    document.getElementById('summary-rejected-main').textContent = data.rejected;
    document.getElementById('summary-violations-main').textContent = data.violations;

    document.getElementById('summary-approved-percent-main').textContent = data.approvedPercent + '%';
    document.getElementById('summary-pending-percent-main').textContent = data.pendingPercent + '%';
    document.getElementById('summary-rejected-percent-main').textContent = data.rejectedPercent + '%';

    document.getElementById('bar-approved-main').style.width = data.approvedPercent + '%';
    document.getElementById('bar-pending-main').style.width = data.pendingPercent + '%';
    document.getElementById('bar-rejected-main').style.width = data.rejectedPercent + '%';

    if (data.spaceTracker && typeof data.spaceTracker === 'object') {
        const trackerEntries = Object.entries(data.spaceTracker);
        const maxCount = Math.max(...trackerEntries.map(([, stat]) => stat.people_count || 0), 1);

        trackerEntries.forEach(([type, stat]) => {
            const safeId = 'tracker-bar-' + type.replace(/[^a-z0-9]+/gi, '-').toLowerCase();
            const bar = document.getElementById(safeId);
            if (!bar) return;

            bar.style.width = Math.round(((stat.people_count || 0) / maxCount) * 100) + '%';
            const row = bar.closest('.tracker-bar-row');
            if (row) {
                const countLabel = row.querySelector('.tracker-value');
                const metaLabel = row.querySelector('.tracker-meta');
                if (countLabel) {
                    countLabel.textContent = `${stat.people_count || 0} people`;
                }
                if (metaLabel) {
                    metaLabel.querySelector('span:last-child').textContent = `${stat.reservation_count || 0} reservations`;
                }
            }
        });
    }

    // Update Student Request panel summary
    if (document.getElementById('summary-total')) {
        document.getElementById('summary-total').textContent = data.total;
        document.getElementById('summary-pending').textContent = data.pending;
        document.getElementById('summary-approved').textContent = data.approved;
        document.getElementById('summary-rejected').textContent = data.rejected;
        document.getElementById('summary-violations').textContent = data.violations;

        document.getElementById('summary-approved-percent').textContent = data.approvedPercent + '%';
        document.getElementById('summary-pending-percent').textContent = data.pendingPercent + '%';
        document.getElementById('summary-rejected-percent').textContent = data.rejectedPercent + '%';

        document.getElementById('bar-approved').style.width = data.approvedPercent + '%';
        document.getElementById('bar-pending').style.width = data.pendingPercent + '%';
        document.getElementById('bar-rejected').style.width = data.rejectedPercent + '%';
    }
}

function fetchSummaryData() {
    fetch('?summary_data=1')
        .then((response) => response.json())
        .then((data) => updateAllSummaryViews(data))
        .catch((error) => console.error('Summary fetch failed:', error));
}

// Fetch summary data on page load and refresh every 10 seconds
if (document.getElementById('summary-total-main')) {
    fetchSummaryData();
    setInterval(fetchSummaryData, 10000);
}
</script>
<script>
function closePopup() {
    const popup = document.getElementById("systemPopup");
    if (popup) {
        popup.classList.remove("active");
        popup.style.display = "none";
    }
}
let deleteStudentUrl = "";

function openDeleteStudentPopup(url) {
    deleteStudentUrl = url;
    document.getElementById("deleteStudentPopup").classList.add("active");
}

function closeDeleteStudentPopup() {
    document.getElementById("deleteStudentPopup").classList.remove("active");
    deleteStudentUrl = "";
}

function confirmDeleteStudent() {
    if (deleteStudentUrl !== "") {
        window.location.href = deleteStudentUrl;
    }
}

let deleteReservationUrl = "";

function openDeleteReservationPopup(url) {
    deleteReservationUrl = url;
    document.getElementById("deleteReservationPopup").classList.add("active");
}

function closeDeleteReservationPopup() {
    document.getElementById("deleteReservationPopup").classList.remove("active");
    deleteReservationUrl = "";
}

function confirmDeleteReservation() {
    if (deleteReservationUrl !== "") {
        window.location.href = deleteReservationUrl;
    }
}
let statusUrl = "";

function openStatusPopup(url, message) {
    statusUrl = url;
    document.getElementById("statusPopupMessage").innerHTML = message + "<br>This action will update the reservation status.";
    document.getElementById("statusConfirmPopup").classList.add("active");
}

function closeStatusPopup() {
    document.getElementById("statusConfirmPopup").classList.remove("active");
    statusUrl = "";
}

function confirmStatusUpdate() {
    if (statusUrl !== "") {
        window.location.href = statusUrl;
    }
}
function openLogoutPopup() {
    document.getElementById("logoutConfirmPopup").classList.add("active");
}

function closeLogoutPopup() {
    document.getElementById("logoutConfirmPopup").classList.remove("active");
}

function confirmLogout() {
    window.location.href = "../actions/admin/logout.php";
}
</script>
<div class="confirm-overlay" id="deleteStudentPopup">
    <div class="confirm-card">
        <button type="button" class="confirm-x" onclick="closeDeleteStudentPopup()">
            &times;
        </button>

        <div class="confirm-icon">
            <i class="fas fa-exclamation"></i>
        </div>

        <h2>Are you sure?</h2>

        <p>
            Delete this student and all of their reservations?
            <br>
            This action cannot be undone.
        </p>

        <div class="confirm-actions">
            <button type="button" class="confirm-delete" onclick="confirmDeleteStudent()">
                Delete
            </button>

            <button type="button" class="confirm-cancel" onclick="closeDeleteStudentPopup()">
                Cancel
            </button>
        </div>
    </div>
</div>
<div class="confirm-overlay" id="deleteReservationPopup">
    <div class="confirm-card">
        <button type="button" class="confirm-x" onclick="closeDeleteReservationPopup()">
            &times;
        </button>

        <div class="confirm-icon">
            <i class="fas fa-exclamation"></i>
        </div>

        <h2>Are you sure?</h2>

        <p>
            Delete this reservation?
            <br>
            This action cannot be undone.
        </p>

        <div class="confirm-actions">
            <button type="button" class="confirm-delete" onclick="confirmDeleteReservation()">
                Delete
            </button>

            <button type="button" class="confirm-cancel" onclick="closeDeleteReservationPopup()">
                Cancel
            </button>
        </div>
    </div>
</div>
<div class="confirm-overlay" id="statusConfirmPopup">
    <div class="confirm-card">
        <button type="button" class="confirm-x" onclick="closeStatusPopup()">
            &times;
        </button>

        <div class="confirm-icon">
            <i class="fas fa-exclamation"></i>
        </div>

        <h2>Are you sure?</h2>

        <p id="statusPopupMessage"></p>

        <div class="confirm-actions">
            <button type="button" class="confirm-delete" onclick="confirmStatusUpdate()">
                Confirm
            </button>

            <button type="button" class="confirm-cancel" onclick="closeStatusPopup()">
                Cancel
            </button>
        </div>
    </div>
</div>
<div class="confirm-overlay" id="logoutConfirmPopup">
    <div class="confirm-card">
        <button type="button" class="confirm-x" onclick="closeLogoutPopup()">
            &times;
        </button>

        <div class="confirm-icon">
            <i class="fas fa-exclamation"></i>
        </div>

        <h2>Are you sure?</h2>

        <p>Are you sure you want to log out?</p>

        <div class="confirm-actions">
            <button type="button" class="confirm-delete" onclick="confirmLogout()">
                Log Out
            </button>

            <button type="button" class="confirm-cancel" onclick="closeLogoutPopup()">
                Cancel
            </button>
        </div>
    </div>
</div>
</body>
</html>
