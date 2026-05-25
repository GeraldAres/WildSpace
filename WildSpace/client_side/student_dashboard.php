<?php
include '../actions/student/student_dashboard_logic.php';
if (!empty($message)) {
    if ($messageType === 'success') {
        $_SESSION['popup_success'] = $message;
    } else {
        $_SESSION['popup_error'] = $message;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WildSpace - Student Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../assets/css/student-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>

<body>
<?php include __DIR__ . '/popup.php'; ?>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="logo-text">WildSpace</div>
            <p>Student Dashboard</p>
        </div>

        <nav class="sidebar-nav">
            <a href="student_dashboard.php?view=reservations"
               class="sidebar-link <?php echo $view === 'reservations' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                My Reservations
            </a>

            <a href="student_dashboard.php?view=book"
               class="sidebar-link <?php echo $view === 'book' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i>
                Book Study Space
            </a>

            <a href="student_dashboard.php?view=reservation_history"
                class="sidebar-link <?php echo $view === 'reservation_history' ? 'active' : ''; ?>">
                <i class="fas fa-clock-rotate-left"></i>
                Reservation Calendar
            </a>

            <a href="student_dashboard.php?view=violations"
               class="sidebar-link <?php echo $view === 'violations' ? 'active' : ''; ?>">
                <i class="fas fa-triangle-exclamation"></i>
                Violation History
            </a>

            <a href="student_dashboard.php?view=tracker"
               class="sidebar-link <?php echo $view === 'tracker' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                Live Space Tracker
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

        <?php if ($view === 'reservations') { ?>

            <section class="admin-panel active">
                <header class="dashboard-header">
                    <div class="dashboard-title">
                        <h1>My Reservations</h1>
                        <p>Manage your reservation requests, check approval status, and delete requests if needed.</p>
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
                        <h2>Reservation Requests</h2>
                        <p>A list of your submitted booking requests and their current review status.</p>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                    <th>Study Space</th>
                                    <th>Status</th>
                                    <th>Approved/Rejected By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($reservations) === 0) { ?>
                                    <tr>
                                        <td colspan="6" class="muted">You have no reservations yet.</td>
                                    </tr>
                                <?php } ?>

                                <?php foreach ($reservations as $row) { ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($row['reservation_id']); ?></td>
                                        <td><?php echo htmlspecialchars(formatReadableDate($row['reservation_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['capacity']); ?> seats</td>
<td><?php echo htmlspecialchars($row['space_type'] ?? 'Not specified'); ?></td>
<td>
    <span class="status-badge <?php echo reservationStatusClass($row['status']); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td class="muted">
                                            <?php
                                            if (
                                                ($row['status'] === 'Approved' || $row['status'] === 'Rejected') &&
                                                !empty($row['admin_firstname'])
                                            ) {
                                                echo htmlspecialchars(trim($row['admin_firstname'] . ' ' . $row['admin_lastname']));
                                            } else {
                                                echo "Not yet reviewed";
                                            }
                                            ?>
                                        </td>

                                        <td>
                                        <button type="button"
                                                class="action-btn delete-btn"
                                                onclick="openDeleteReservationPopup('../actions/student/delete_reservation.php?id=<?php echo urlencode($row['reservation_id']); ?>')">
                                            Delete
                                        </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-controls">
                        <a class="pagination-btn<?php echo $reservationPage <= 1 ? ' disabled' : ''; ?>"
                           href="student_dashboard.php?view=reservations&reservation_page=<?php echo max(1, $reservationPage - 1); ?>"
                           <?php if ($reservationPage <= 1) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                            &laquo; Previous
                        </a>

                        <span class="pagination-info">
                            Page <?php echo $reservationPage; ?> of <?php echo $reservationPageCount; ?>
                        </span>

                        <a class="pagination-btn<?php echo $reservationPage >= $reservationPageCount ? ' disabled' : ''; ?>"
                           href="student_dashboard.php?view=reservations&reservation_page=<?php echo min($reservationPageCount, $reservationPage + 1); ?>"
                           <?php if ($reservationPage >= $reservationPageCount) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                            Next &raquo;
                        </a>
                    </div>
                </section>
            </section>

        <?php } ?>

        <?php if ($view === 'book') { ?>

            <section class="admin-panel active">
                <header class="dashboard-header">
                    <div class="dashboard-title">
                        <h1>Book Study Space</h1>
                        <p>View existing reservations on the calendar and submit a new booking request.</p>
                    </div>
                </header>

                <section class="reservation-form-card">
                    <h2>Create New Reservation</h2>
                    <br>

                    <form method="POST" class="reservation-form">
                        <div class="form-group">
                            <label>Reservation Date</label>
                            <input type="date" name="reservation_date" class="form-input" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Seats</label>
                            <input type="number" id="capacityInput" name="capacity" class="form-input" min="1" max="10" required>
                        </div>
                        <div class="form-group">
    <label>Study Space Type</label>
<select name="space_type" id="spaceTypeSelect" class="form-input" required>
    <option value="" disabled selected hidden>Select study space</option>
    <option value="Solo Table" id="soloTableOption">Solo Table</option>
    <option value="Quiet Room">Quiet Room</option>
    <option value="Group Table">Group Table</option>
    <option value="Discussion Room">Discussion Room</option>
</select>
</div>

                        <button type="submit" name="create_reservation" class="submit-btn">
                            Submit Request
                        </button>
                    </form>
                </section>

                <section class="calendar-wrapper">
                    <div class="calendar-header">
                        <h2><?php echo htmlspecialchars($monthTitle); ?></h2>

                        <div class="calendar-nav">
                            <a href="student_dashboard.php?view=book&month=<?php echo $prevMonth; ?>">Prev</a>
                            <a href="student_dashboard.php?view=book&month=<?php echo $nextMonth; ?>">Next</a>
                        </div>
                    </div>

                    <div class="calendar-grid">
                        <div class="calendar-day-name">Mon</div>
                        <div class="calendar-day-name">Tue</div>
                        <div class="calendar-day-name">Wed</div>
                        <div class="calendar-day-name">Thu</div>
                        <div class="calendar-day-name">Fri</div>
                        <div class="calendar-day-name">Sat</div>
                        <div class="calendar-day-name">Sun</div>

                        <?php for ($blank = 1; $blank < $firstDayOfMonth; $blank++) { ?>
                            <div class="calendar-day"></div>
                        <?php } ?>

                        <?php for ($day = 1; $day <= $daysInMonth; $day++) {
                            $date = $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                        ?>
                            <div class="calendar-day">
                                <div class="calendar-date"><?php echo $day; ?></div>

                                <?php if (isset($calendarReservations[$date])) { ?>

                                    <?php foreach ($calendarReservations[$date] as $reservation) { ?>
                                        <?php
                                            $isOwnReservation = (int)$reservation['student_id'] === (int)$student_id;
                                        ?>

<?php if ($isOwnReservation) { ?>
    <button
        type="button"
        class="calendar-reservation <?php echo reservationStatusClass($reservation['status']); ?>"
        data-id="<?php echo htmlspecialchars($reservation['reservation_id']); ?>"
        data-student-name="<?php echo htmlspecialchars(trim($reservation['student_firstname'] . ' ' . $reservation['student_lastname'])); ?>"
        data-date="<?php echo htmlspecialchars(formatReadableDate($reservation['reservation_date'])); ?>"
        data-capacity="<?php echo htmlspecialchars($reservation['capacity']); ?>"
        data-space-type="<?php echo htmlspecialchars($reservation['space_type'] ?? 'Not specified'); ?>"
        data-status="<?php echo htmlspecialchars($reservation['status']); ?>"
        data-approved-by="<?php
            if ($reservation['status'] === 'Approved' && !empty($reservation['admin_firstname'])) {
                echo htmlspecialchars(trim($reservation['admin_firstname'] . ' ' . $reservation['admin_lastname']));
            } else {
                echo 'Not yet approved';
            }
        ?>"
    >
        <strong>
    <?php echo htmlspecialchars(trim($reservation['student_firstname'] . ' ' . $reservation['student_lastname'])); ?>
    </strong><br>
    <?php echo htmlspecialchars($reservation['capacity']); ?> seats -
    <?php echo htmlspecialchars($reservation['status']); ?>
    </button>
<?php } else { ?>
    <div class="calendar-reservation other-approved" title="Approved reservation by another student">
        <strong>
    <?php echo htmlspecialchars(trim($reservation['student_firstname'] . ' ' . $reservation['student_lastname'])); ?>
</strong><br>
<?php echo htmlspecialchars($reservation['capacity']); ?> seats
    </div>
<?php } ?>

                                    <?php } ?>

                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            </section>

        <?php } ?>
        <?php if ($view === 'reservation_history') { ?>

    <section class="admin-panel active">
        <header class="dashboard-header">
            <div class="dashboard-title">
                <h1>Reservation Calendar History</h1>
                <p>View your submitted reservations by date in a read-only calendar.</p>
            </div>
        </header>

        <section class="calendar-wrapper">
            <div class="calendar-header">
                <h2><?php echo htmlspecialchars($monthTitle); ?></h2>

                <div class="calendar-nav">
                    <a href="student_dashboard.php?view=reservation_history&month=<?php echo $prevMonth; ?>">Prev</a>
                    <a href="student_dashboard.php?view=reservation_history&month=<?php echo $nextMonth; ?>">Next</a>
                </div>
            </div>

            <div class="calendar-grid">
                <div class="calendar-day-name">Mon</div>
                <div class="calendar-day-name">Tue</div>
                <div class="calendar-day-name">Wed</div>
                <div class="calendar-day-name">Thu</div>
                <div class="calendar-day-name">Fri</div>
                <div class="calendar-day-name">Sat</div>
                <div class="calendar-day-name">Sun</div>

                <?php for ($blank = 1; $blank < $firstDayOfMonth; $blank++) { ?>
                    <div class="calendar-day"></div>
                <?php } ?>

                <?php for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                ?>
                    <div class="calendar-day">
                        <div class="calendar-date"><?php echo $day; ?></div>

                        <?php if (isset($historyCalendarReservations[$date])) { ?>
                            <?php foreach ($historyCalendarReservations[$date] as $reservation) { ?>
                                <?php if ((int)$reservation['student_id'] === (int)$student_id) { ?>
                                    <div class="calendar-reservation <?php echo reservationStatusClass($reservation['status']); ?> history-pill">
                                        <?php echo htmlspecialchars(trim($reservation['student_firstname'] . ' ' . $reservation['student_lastname'])); ?><br>
                                        <?php echo htmlspecialchars($reservation['capacity']); ?> seats<br>
                                        <?php echo htmlspecialchars($reservation['status']); ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </section>
    </section>

<?php } ?>

        <?php if ($view === 'violations') { ?>

            <section class="admin-panel active">
                <header class="dashboard-header">
                    <div class="dashboard-title">
                        <h1>Violation Records</h1>
                        <p>View violation records of filed approved reservations.</p>
                    </div>
                </header>

                <section class="table-card">
                    <div class="table-header">
                        <h2>Filed Reservation Violations</h2>
                        <p>Review violations recorded against your approved study space reservations.</p>
                    </div>

                    <div class="table-container">
                        <table class="violations-table">
                            <thead>
                                <tr>
                                    <th class="col-id">Violation ID</th>
                                    <th class="col-date">Reservation Date</th>
                                    <th class="col-seats">Seats</th>
                                    <th class="col-type">Violation Type</th>
                                    <th class="col-description">Description</th>
                                    <th class="col-admin">Marked By</th>
                                    <th class="col-marked">Date Marked</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (count($violations) === 0) { ?>
                                    <tr>
                                        <td colspan="7" class="muted">You have no violations.</td>
                                    </tr>
                                <?php } ?>

                                <?php foreach ($violations as $violation) { ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($violation['violation_id']); ?></td>
                                        <td><?php echo htmlspecialchars(formatReadableDate($violation['reservation_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($violation['capacity']); ?> seats</td>
                                        <td><?php echo htmlspecialchars($violation['violation_type']); ?></td>
                                        <td><?php echo htmlspecialchars($violation['description']); ?></td>
                                        <td>
                                            <?php
                                            if (!empty($violation['admin_firstname'])) {
                                                echo htmlspecialchars(trim($violation['admin_firstname'] . ' ' . $violation['admin_lastname']));
                                            } else {
                                                echo "Unknown admin";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $violationDate = new DateTime($violation['violation_date'], new DateTimeZone('UTC'));
                                            $violationDate->setTimezone(new DateTimeZone('Asia/Manila'));
                                            echo htmlspecialchars($violationDate->format("F d, Y"));
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-controls">
                        <a class="pagination-btn<?php echo $violationPage <= 1 ? ' disabled' : ''; ?>"
                           href="student_dashboard.php?view=violations&violation_page=<?php echo max(1, $violationPage - 1); ?>"
                           <?php if ($violationPage <= 1) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                            &laquo; Previous
                        </a>

                        <span class="pagination-info">
                            Page <?php echo $violationPage; ?> of <?php echo $violationPageCount; ?>
                        </span>

                        <a class="pagination-btn<?php echo $violationPage >= $violationPageCount ? ' disabled' : ''; ?>"
                           href="student_dashboard.php?view=violations&violation_page=<?php echo min($violationPageCount, $violationPage + 1); ?>"
                           <?php if ($violationPage >= $violationPageCount) echo 'aria-disabled="true" tabindex="-1"'; ?>>
                            Next &raquo;
                        </a>
                    </div>
                </section>
            </section>

        <?php } ?>

        <?php if ($view === 'tracker') { ?>

            <section class="admin-panel active">
                <header class="dashboard-header">
                    <div class="dashboard-title">
                        <h1>Live Space Tracker</h1>
                        <p>Non-editable booking counts for each study space type, updated from current reservations.</p>
                    </div>
                </header>

                <section class="summary-cards cols-4">
                    <?php foreach ($spaceTracker as $type => $stats) { ?>
                        <div class="summary-card">
                            <span><?php echo htmlspecialchars($type); ?></span>
                            <strong><?php echo htmlspecialchars($stats['people_count']); ?> people</strong>
                            <p><?php echo htmlspecialchars($stats['reservation_count']); ?> reservations</p>
                        </div>
                    <?php } ?>
                </section>

                <section class="table-card">
                    <div class="table-header">
                        <h2>Booking Overview</h2>
                        <p>Counts reflect pending and approved future reservations to help you see current usage at a glance.</p>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Space Type</th>
                                    <th>Reservations</th>
                                    <th>People Booked</th>
                                    <th>Approved</th>
                                    <th>Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($spaceTracker as $type => $stats) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($type); ?></td>
                                        <td><?php echo htmlspecialchars($stats['reservation_count']); ?></td>
                                        <td><?php echo htmlspecialchars($stats['people_count']); ?></td>
                                        <td><?php echo htmlspecialchars($stats['approved_count']); ?></td>
                                        <td><?php echo htmlspecialchars($stats['pending_count']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

        <?php } ?>

    </main>
</div>

<div class="modal-overlay" id="reservationModal">
    <div class="modal-card">
        <h2>Reservation Details</h2>
        <p><strong>Student Name:</strong> <span id="modalStudentName"></span></p>
        <p><strong>Reservation ID:</strong> <span id="modalReservationId"></span></p>
        <p><strong>Date:</strong> <span id="modalDate"></span></p>
        <p><strong>Seats:</strong> <span id="modalCapacity"></span> seats</p>
        <p><strong>Study Space:</strong> <span id="modalSpaceType"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <p><strong>Approved By:</strong> <span id="modalApprovedBy"></span></p>

        <button type="button" class="modal-close" id="closeModal">Close</button>
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
        <p>Are you sure you want to delete this reservation?<br>This action cannot be undone.</p>

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
<script src="../assets/js/student-dashboard.js"></script>
</body>
</html>