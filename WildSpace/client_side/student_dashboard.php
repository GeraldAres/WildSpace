    <?php
    session_start();
    include '../database/connection.php';

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
        header("Location: login.php");
        exit();
    }

    $student_id = $_SESSION['student_id'];
    $message = "";
    $messageType = "";

    /* CREATE RESERVATION */
/* CREATE RESERVATION */
if (isset($_POST['create_reservation'])) {
    $reservation_date = trim($_POST['reservation_date'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $today = date('Y-m-d');

    if (empty($reservation_date) || empty($capacity)) {
        $message = "Please fill out all reservation fields.";
        $messageType = "error";
    } elseif ($reservation_date < $today) {
        $message = "You cannot reserve a past date.";
        $messageType = "error";
    } elseif (!preg_match('/^[0-9]+$/', $capacity) || (int)$capacity <= 0) {
    $message = "Seats must be a valid number.";
    $messageType = "error";
        } elseif ((int)$capacity > 10) {
            $message = "A reservation can only accommodate up to 10 seats.";
            $messageType = "error";
    } else {
        $insertSql = "INSERT INTO tblreservation 
                        (student_id, admin_id, status, reservation_date, date_created, capacity)
                    VALUES 
                        ($1, NULL, 'Pending', $2, NOW(), $3)";

        $insertResult = pg_query_params($conn, $insertSql, [
            $student_id,
            $reservation_date,
            $capacity
        ]);

        if ($insertResult) {
            $message = "Reservation request submitted successfully.";
            $messageType = "success";
        } else {
            $message = "Failed to create reservation: " . pg_last_error($conn);
            $messageType = "error";
        }
    }
}

    /* MONTH FILTER */
    $currentMonth = $_GET['month'] ?? date('Y-m');

    if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
        $currentMonth = date('Y-m');
    }

    $monthStart = $currentMonth . "-01";
    $monthEnd = date("Y-m-t", strtotime($monthStart));

    $prevMonth = date("Y-m", strtotime($monthStart . " -1 month"));
    $nextMonth = date("Y-m", strtotime($monthStart . " +1 month"));

    /* ALL RESERVATIONS FOR CALENDAR */
    $calendarSql = "SELECT 
                    r.reservation_id,
                    r.student_id,
                    r.admin_id,
                    r.status,
                    r.reservation_date,
                    r.capacity,
                    r.date_created,
                    admin_user.firstname AS admin_firstname,
                    admin_user.lastname AS admin_lastname,
                    student_user.firstname AS student_firstname,
                    student_user.lastname AS student_lastname
                FROM tblreservation r
                LEFT JOIN tbladmin a ON r.admin_id = a.admin_id
                LEFT JOIN tbluser admin_user ON a.user_id = admin_user.user_id
                INNER JOIN tblstudent s ON r.student_id = s.student_id
                INNER JOIN tbluser student_user ON s.user_id = student_user.user_id
                WHERE r.reservation_date BETWEEN $1 AND $2
                AND (
                    r.status = 'Approved'
                    OR (r.student_id = $3 AND r.status = 'Pending')
                )
                ORDER BY r.reservation_date ASC";

    $calendarResult = pg_query_params($conn, $calendarSql, [$monthStart, $monthEnd, $student_id]);

    if (!$calendarResult) {
        die("Calendar query failed: " . pg_last_error($conn));
    }

    $calendarReservations = [];

    while ($row = pg_fetch_assoc($calendarResult)) {
        $date = $row['reservation_date'];
        $calendarReservations[$date][] = $row;
    }

    /* STUDENT RESERVATIONS */
    $sql = "SELECT
                r.reservation_id,
                r.status,
                r.reservation_date,
                r.capacity,
                r.date_created,
                u.firstname AS admin_firstname,
                u.lastname AS admin_lastname
            FROM tblreservation r
            LEFT JOIN tbladmin a ON r.admin_id = a.admin_id
            LEFT JOIN tbluser u ON a.user_id = u.user_id
            WHERE r.student_id = $1
            ORDER BY r.date_created DESC";

    $result = pg_query_params($conn, $sql, [$student_id]);

    if (!$result) {
        die("Query failed: " . pg_last_error($conn));
    }

    $reservations = [];
    $total = 0;
    $pending = 0;
    $approved = 0;
    $rejected = 0;

    while ($row = pg_fetch_assoc($result)) {
        $reservations[] = $row;
        $total++;

        if ($row['status'] === 'Pending') {
            $pending++;
        } elseif ($row['status'] === 'Approved') {
            $approved++;
        } elseif ($row['status'] === 'Rejected') {
            $rejected++;
        }
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
    $violationsSql = "SELECT
                    v.violation_id,
                    v.violation_type,
                    v.description,
                    v.violation_date,
                    r.reservation_date,
                    r.capacity,
                    u.firstname AS admin_firstname,
                    u.lastname AS admin_lastname
                  FROM tblviolation v
                  INNER JOIN tblreservation r ON v.reservation_id = r.reservation_id
                  LEFT JOIN tbladmin a ON v.admin_id = a.admin_id
                  LEFT JOIN tbluser u ON a.user_id = u.user_id
                  WHERE v.student_id = $1
                  ORDER BY v.violation_date DESC";

$violationsResult = pg_query_params($conn, $violationsSql, [$student_id]);

if (!$violationsResult) {
    die("Violations query failed: " . pg_last_error($conn));
}

$violations = [];

while ($row = pg_fetch_assoc($violationsResult)) {
    $violations[] = $row;
}

    $firstDayOfMonth = date('N', strtotime($monthStart));
    $daysInMonth = date('t', strtotime($monthStart));
    $monthTitle = date('F Y', strtotime($monthStart));
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>WildSpace - Student Dashboard</title>
        <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        <style>
            .calendar-wrapper {
                background: #fff;
                border: 1px solid #e5e5e5;
                border-radius: 24px;
                padding: 24px;
                margin-bottom: 30px;
            }

            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
            }

            .calendar-header h2 {
                font-size: 28px;
            }

            .calendar-nav a {
                text-decoration: none;
                background: #000;
                color: #fff;
                padding: 10px 18px;
                border-radius: 999px;
                font-weight: 700;
                margin-left: 8px;
            }

            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 10px;
            }

            .calendar-day-name {
                font-weight: 800;
                text-align: center;
                padding: 12px;
                color: #666;
            }

            .calendar-day {
                min-height: 130px;
                border: 1px solid #e5e5e5;
                border-radius: 18px;
                padding: 12px;
                background: #fafafa;
            }

            .calendar-date {
                font-weight: 800;
                margin-bottom: 8px;
            }

            .calendar-reservation {
        display: block;
        font-size: 12px;
        line-height: 1.5;
        padding: 12px 16px;
        border-radius: 20px;
        margin-bottom: 8px;
        width: fit-content;
        max-width: 100%;
        white-space: normal;
        border: none;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
    }

    .calendar-reservation:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }
            .reservation-form-card {
                background: #fff;
                border: 1px solid #e5e5e5;
                border-radius: 24px;
                padding: 24px;
                margin-bottom: 30px;
            }

            .reservation-form {
                display: grid;
                grid-template-columns: 1fr 1fr auto;
                gap: 16px;
                align-items: end;
            }

            .form-group label {
                display: block;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .form-input {
                width: 100%;
                padding: 14px 16px;
                border: 1px solid #ddd;
                border-radius: 14px;
                font-size: 15px;
            }

            .submit-btn {
                background: #000;
                color: #fff;
                border: none;
                padding: 15px 24px;
                border-radius: 999px;
                font-weight: 800;
                cursor: pointer;
            }

            .message {
                padding: 14px 18px;
                border-radius: 14px;
                margin-bottom: 20px;
                font-weight: 700;
            }

            .message.success {
                background: #e8f7ee;
                color: #0f8a3b;
            }

            .message.error {
                background: #fdecec;
                color: #b42318;
            }

            @media (max-width: 900px) {
                .reservation-form {
                    grid-template-columns: 1fr;
                }

                .calendar-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .status-pending,
.calendar-reservation.status-pending {
    background: #fff4d8;
    color: #9a650f;
}
            .status-approved,
            .calendar-reservation.status-approved {
                background: #e8f7ee;
                color: #0f8a3b;
            }

            .status-rejected,
            .calendar-reservation.status-rejected {
                background: #fdecec;
                color: #b42318;
            }

            .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-card {
        background: #fff;
        width: 90%;
        max-width: 420px;
        border-radius: 24px;
        padding: 36px 32px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        text-align: center;
    }

    .modal-card h2 {
        margin-bottom: 24px;
    }

    .modal-card p {
        margin-bottom: 14px;
        text-align: center;
    }

    .modal-close {
        margin: 24px auto 0;
        display: block;
        background: #000;
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
    }
    .calendar-reservation.other-approved {
    background: #e8f7ee;
    color: #0f8a3b;
    cursor: not-allowed;
    opacity: 0.75;
}

.calendar-reservation.other-approved:hover {
    transform: none;
    opacity: 0.75;
    .violations-table {
    table-layout: fixed;
    width: 100%;
}

}

.violations-table th,
.violations-table td {
    font-size: 13px;
    line-height: 1.45;
    padding: 20px 18px;
    vertical-align: middle;
}

.violations-table th {
    font-size: 14px;
    letter-spacing: 0.08em;
}

.violations-table .col-id {
    width: 9%;
}

.violations-table .col-date {
    width: 14%;
}

.violations-table .col-seats {
    width: 9%;
}

.violations-table .col-type {
    width: 13%;
}

.violations-table .col-description {
    width: 27%;
}

.violations-table .col-admin {
    width: 13%;
}

.violations-table .col-marked {
    width: 15%;
}

.action-btn {
    display: inline-block;
    padding: 12px 22px;
    border-radius: 999px;
    font-weight: 800;
    text-decoration: none;
    border: none;
    cursor: pointer;
}

.delete-btn {
    background: #000;
    color: #fff;
}

.approve-btn {
    background: #e8f7ee;
    color: #0f8a3b;
}

.reject-btn {
    background: #fdecec;
    color: #a32618;
}
        </style>
    </head>


    <body>

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

                <a href="student_dashboard.php?view=violations"
   class="sidebar-link <?php echo $view === 'violations' ? 'active' : ''; ?>">
    <i class="fas fa-triangle-exclamation"></i>
    My Violations
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

            <?php if ($message !== "") { ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php } ?>

            <?php if ($view === 'reservations') { ?>

                <section class="admin-panel active">
                    <header class="dashboard-header">
    <div class="dashboard-title">
        <h1>My Reservations</h1>
        <p>View your study space booking requests and approval status.</p>
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
                            <h2>Reservation History</h2>
                            <p>Latest reservations connected to your student account</p>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Seats</th>
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

                                            <!-- ADD THIS ACTION COLUMN -->
                                            <td>
                                                <a class="action-btn delete-btn"
                                                    href="../actions/student/delete_reservation.php?id=<?php echo urlencode($row['reservation_id']); ?>"
                                                    onclick="return confirm('Are you sure you want to delete this reservation?');">
                                                        Delete
                                                    </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                                <input type="number" name="capacity" class="form-input" min="1" max="10" required>
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
                data-status="<?php echo htmlspecialchars($reservation['status']); ?>"
                data-approved-by="<?php
                    if ($reservation['status'] === 'Approved' && !empty($reservation['admin_firstname'])) {
                        echo htmlspecialchars(trim($reservation['admin_firstname'] . ' ' . $reservation['admin_lastname']));
                    } else {
                        echo 'Not yet approved';
                    }
                ?>"
            >
                <?php echo htmlspecialchars($reservation['capacity']); ?> seats -
                <?php echo htmlspecialchars($reservation['status']); ?>
            </button>
        <?php } else { ?>
<div class="calendar-reservation other-approved" title="Approved reservation by another student">
    <?php echo htmlspecialchars(trim($reservation['student_firstname'] . ' ' . $reservation['student_lastname'])); ?><br>
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

            <?php if ($view === 'violations') { ?>

                <section class="admin-panel active">
                    <header class="dashboard-header">
                        <div class="dashboard-title">
                            <h1>My Violations</h1>
                            <p>View violations marked by administrators.</p>
                        </div>
                    </header>

                    <section class="table-card">
                        <div class="table-header">
                            <h2>Violation History</h2>
                            <p>Records of no-show violations from approved reservations</p>
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
            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            <p><strong>Approved By:</strong> <span id="modalApprovedBy"></span></p>

            <button type="button" class="modal-close" id="closeModal">Close</button>
        </div>
    </div>

 <script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("reservationModal");
    const closeModal = document.getElementById("closeModal");
    const message = document.querySelector(".message");

    if (message) {
        setTimeout(() => {
            message.style.display = "none";
        }, 3000);
    }

    document.querySelectorAll("button.calendar-reservation").forEach((pill) => {
        pill.addEventListener("click", () => {
            document.getElementById("modalStudentName").textContent = pill.dataset.studentName;
            document.getElementById("modalReservationId").textContent = pill.dataset.id;
            document.getElementById("modalDate").textContent = pill.dataset.date;
            document.getElementById("modalCapacity").textContent = pill.dataset.capacity;
            document.getElementById("modalStatus").textContent = pill.dataset.status;
            document.getElementById("modalApprovedBy").textContent = pill.dataset.approvedBy;

            modal.classList.add("active");
        });
    });

    closeModal.addEventListener("click", () => {
        modal.classList.remove("active");
    });

    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("active");
        }
    });
});
</script>

    </body>
    </html>