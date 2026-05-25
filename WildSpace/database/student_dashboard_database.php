<?php
/* CREATE RESERVATION */
if (isset($_POST['create_reservation'])) {
    $reservation_date = trim($_POST['reservation_date'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $space_type = trim($_POST['space_type'] ?? '');
    $today = date('Y-m-d');

    if (empty($reservation_date) || empty($capacity) || empty($space_type)) {
        $message = "Please fill out all reservation fields.";
        $messageType = "error";
    } elseif ($reservation_date < $today) {
        $message = "You cannot reserve a past date.";
        $messageType = "error";
    } elseif (!preg_match('/^[0-9]+$/', $capacity) || (int)$capacity <= 0) {
        $message = "Seats must be a valid number.";
        $messageType = "error";
    } elseif ((int)$capacity > 1 && $space_type === 'Solo Table') {
        $message = "Solo Table can only be selected for 1 seat.";
        $messageType = "error";
    } else {
        $insertSql = "INSERT INTO tblreservation 
                (student_id, admin_id, status, reservation_date, date_created, capacity, space_type)
            VALUES 
                ($1, NULL, 'Pending', $2, NOW(), $3, $4)";

        $insertResult = pg_query_params($conn, $insertSql, [
    $student_id,
    $reservation_date,
    $capacity,
    $space_type
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
                r.space_type,
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
/* STUDENT RESERVATION HISTORY CALENDAR */
$historyCalendarSql = "SELECT 
                r.reservation_id,
                r.student_id,
                r.admin_id,
                r.status,
                r.reservation_date,
                r.capacity,
                r.space_type,
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
            AND r.student_id = $3
            ORDER BY r.reservation_date ASC";

$historyCalendarResult = pg_query_params($conn, $historyCalendarSql, [$monthStart, $monthEnd, $student_id]);

if (!$historyCalendarResult) {
    die("History calendar query failed: " . pg_last_error($conn));
}

$historyCalendarReservations = [];

while ($row = pg_fetch_assoc($historyCalendarResult)) {
    $date = $row['reservation_date'];
    $historyCalendarReservations[$date][] = $row;
}

/* STUDENT RESERVATIONS */
$reservationsPerPage = 10;
$reservationPage = isset($_GET['reservation_page']) && is_numeric($_GET['reservation_page']) && (int)$_GET['reservation_page'] > 0
    ? (int)$_GET['reservation_page']
    : 1;

$countSql = "SELECT
                COUNT(*) AS total_reservations,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_reservations,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved_reservations,
                SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_reservations
            FROM tblreservation
            WHERE student_id = $1";

$countResult = pg_query_params($conn, $countSql, [$student_id]);

if (!$countResult) {
    die("Count query failed: " . pg_last_error($conn));
}

$countRow = pg_fetch_assoc($countResult);

$total = (int)($countRow['total_reservations'] ?? 0);
$pending = (int)($countRow['pending_reservations'] ?? 0);
$approved = (int)($countRow['approved_reservations'] ?? 0);
$rejected = (int)($countRow['rejected_reservations'] ?? 0);

$reservationPageCount = max(1, (int)ceil($total / $reservationsPerPage));
$reservationPage = min($reservationPage, $reservationPageCount);
$reservationOffset = ($reservationPage - 1) * $reservationsPerPage;

$sql = "SELECT
            r.reservation_id,
            r.status,
            r.reservation_date,
            r.capacity,
            r.space_type,
            r.date_created,
            u.firstname AS admin_firstname,
            u.lastname AS admin_lastname
        FROM tblreservation r
        LEFT JOIN tbladmin a ON r.admin_id = a.admin_id
        LEFT JOIN tbluser u ON a.user_id = u.user_id
        WHERE r.student_id = $1
        ORDER BY r.date_created DESC
        LIMIT $2 OFFSET $3";

$result = pg_query_params($conn, $sql, [$student_id, $reservationsPerPage, $reservationOffset]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

$reservations = [];

while ($row = pg_fetch_assoc($result)) {
    $reservations[] = $row;
}

/* VIOLATIONS */
$violationsPerPage = 10;
$violationPage = isset($_GET['violation_page']) && is_numeric($_GET['violation_page']) && (int)$_GET['violation_page'] > 0
    ? (int)$_GET['violation_page']
    : 1;

$violationsCountSql = "SELECT COUNT(*) AS total_violations
                       FROM tblviolation
                       WHERE student_id = $1";

$violationsCountResult = pg_query_params($conn, $violationsCountSql, [$student_id]);

if (!$violationsCountResult) {
    die("Violations count query failed: " . pg_last_error($conn));
}

$violationsCountRow = pg_fetch_assoc($violationsCountResult);
$violationsTotal = (int)($violationsCountRow['total_violations'] ?? 0);

$violationPageCount = max(1, (int)ceil($violationsTotal / $violationsPerPage));
$violationPage = min($violationPage, $violationPageCount);
$violationOffset = ($violationPage - 1) * $violationsPerPage;

$violationsSql = "SELECT
                    v.violation_id,
                    v.violation_type,
                    v.description,
                    v.violation_date,
                    r.reservation_date,
                    r.capacity,
                    r.space_type,
                    u.firstname AS admin_firstname,
                    u.lastname AS admin_lastname
                  FROM tblviolation v
                  INNER JOIN tblreservation r ON v.reservation_id = r.reservation_id
                  LEFT JOIN tbladmin a ON v.admin_id = a.admin_id
                  LEFT JOIN tbluser u ON a.user_id = u.user_id
                  WHERE v.student_id = $1
                  ORDER BY v.violation_date DESC
                  LIMIT $2 OFFSET $3";

$violationsResult = pg_query_params($conn, $violationsSql, [$student_id, $violationsPerPage, $violationOffset]);

if (!$violationsResult) {
    die("Violations query failed: " . pg_last_error($conn));
}

$violations = [];

while ($row = pg_fetch_assoc($violationsResult)) {
    $violations[] = $row;
}

/* LIVE SPACE TRACKER COUNTS */
$spaceTrackerSql = "SELECT
                        r.space_type,
                        COUNT(*) AS reservation_count,
                        COALESCE(SUM(r.capacity), 0) AS people_count,
                        SUM(CASE WHEN r.status = 'Approved' THEN 1 ELSE 0 END) AS approved_count,
                        SUM(CASE WHEN r.status = 'Pending' THEN 1 ELSE 0 END) AS pending_count
                    FROM tblreservation r
                    WHERE r.reservation_date >= $1
                      AND r.status IN ('Approved', 'Pending')
                    GROUP BY r.space_type
                    ORDER BY r.space_type";

$spaceTrackerResult = pg_query_params($conn, $spaceTrackerSql, [date('Y-m-d')]);

$spaceTracker = [
    'Solo Table' => ['reservation_count' => 0, 'people_count' => 0, 'approved_count' => 0, 'pending_count' => 0],
    'Quiet Room' => ['reservation_count' => 0, 'people_count' => 0, 'approved_count' => 0, 'pending_count' => 0],
    'Group Table' => ['reservation_count' => 0, 'people_count' => 0, 'approved_count' => 0, 'pending_count' => 0],
    'Discussion Room' => ['reservation_count' => 0, 'people_count' => 0, 'approved_count' => 0, 'pending_count' => 0],
];

if ($spaceTrackerResult) {
    while ($row = pg_fetch_assoc($spaceTrackerResult)) {
        $spaceType = $row['space_type'] ?? null;
        if ($spaceType && array_key_exists($spaceType, $spaceTracker)) {
            $spaceTracker[$spaceType] = [
                'reservation_count' => (int)$row['reservation_count'],
                'people_count' => (int)$row['people_count'],
                'approved_count' => (int)$row['approved_count'],
                'pending_count' => (int)$row['pending_count'],
            ];
        }
    }
}

$firstDayOfMonth = date('N', strtotime($monthStart));
$daysInMonth = date('t', strtotime($monthStart));
$monthTitle = date('F Y', strtotime($monthStart));
?>