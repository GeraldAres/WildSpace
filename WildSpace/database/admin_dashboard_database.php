<?php
/* ================= STUDENTS ================= */
$studentsPerPage = 10;
$studentPage = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0
    ? (int)$_GET['page']
    : 1;

$studentCountSql = "SELECT COUNT(*) AS total_students FROM tblstudent";
$studentCountResult = pg_query($conn, $studentCountSql);

if (!$studentCountResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$studentCountRow = pg_fetch_assoc($studentCountResult);
$studentTotal = (int)($studentCountRow['total_students'] ?? 0);
$studentPageCount = max(1, (int)ceil($studentTotal / $studentsPerPage));
$studentPage = min($studentPage, $studentPageCount);
$studentOffset = ($studentPage - 1) * $studentsPerPage;

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
    ORDER BY u.firstname ASC, u.lastname ASC
    LIMIT $studentsPerPage OFFSET $studentOffset";

$studentsResult = pg_query($conn, $studentsSql);

if (!$studentsResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$students = [];

while ($row = pg_fetch_assoc($studentsResult)) {
    $students[] = $row;
}

/* ================= RESERVATION REQUESTS ================= */
$requestsPerPage = 10;
$requestPage = isset($_GET['request_page']) && is_numeric($_GET['request_page']) && (int)$_GET['request_page'] > 0
    ? (int)$_GET['request_page']
    : 1;

$requestCountSql = "SELECT
        COUNT(*) AS total_requests,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_requests,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved_requests,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_requests
    FROM tblreservation";
$requestCountResult = pg_query($conn, $requestCountSql);

if (!$requestCountResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$requestCountRow = pg_fetch_assoc($requestCountResult);
$requestTotal = (int)($requestCountRow['total_requests'] ?? 0);
$pending = (int)($requestCountRow['pending_requests'] ?? 0);
$approved = (int)($requestCountRow['approved_requests'] ?? 0);
$rejected = (int)($requestCountRow['rejected_requests'] ?? 0);
$total = $requestTotal;

$violationCountSql = "SELECT COUNT(DISTINCT s.student_id) AS violation_students
    FROM tblviolation v
    INNER JOIN tblreservation r ON v.reservation_id = r.reservation_id
    INNER JOIN tblstudent s ON r.student_id = s.student_id";
$violationCountResult = pg_query($conn, $violationCountSql);

if (!$violationCountResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$violationCountRow = pg_fetch_assoc($violationCountResult);
$violationStudents = (int)($violationCountRow['violation_students'] ?? 0);

$requestPageCount = max(1, (int)ceil($requestTotal / $requestsPerPage));
$requestPage = min($requestPage, $requestPageCount);
$requestOffset = ($requestPage - 1) * $requestsPerPage;

$requestsSql = "SELECT
        r.reservation_id,
        r.student_id,
        r.admin_id,
        r.status,
        r.reservation_date,
        r.capacity,
        r.space_type,
        r.date_created,
        s.user_id,
        u.firstname,
        u.lastname,
        u.email,
        u.mobile_number,
        v.violation_id
    FROM tblreservation r
    INNER JOIN tblstudent s ON r.student_id = s.student_id
    INNER JOIN tbluser u ON s.user_id = u.user_id
    LEFT JOIN tblviolation v ON r.reservation_id = v.reservation_id
    WHERE r.status = 'Pending'
    ORDER BY r.date_created DESC
    LIMIT $requestsPerPage OFFSET $requestOffset";

$approvedPercent = $requestTotal > 0 ? round(($approved / $requestTotal) * 100, 1) : 0;
$pendingPercent = $requestTotal > 0 ? round(($pending / $requestTotal) * 100, 1) : 0;
$rejectedPercent = $requestTotal > 0 ? round(($rejected / $requestTotal) * 100, 1) : 0;

$summaryData = [
    'total' => $requestTotal,
    'pending' => $pending,
    'approved' => $approved,
    'rejected' => $rejected,
    'violations' => $violationStudents,
    'approvedPercent' => $approvedPercent,
    'pendingPercent' => $pendingPercent,
    'rejectedPercent' => $rejectedPercent,
];

if (isset($_GET['summary_data'])) {
    header('Content-Type: application/json');
    echo json_encode($summaryData);
    exit();
}

$requestsResult = pg_query($conn, $requestsSql);

if (!$requestsResult) {
    die('Query failed: ' . pg_last_error($conn));
}

$requests = [];

while ($row = pg_fetch_assoc($requestsResult)) {
    $requests[] = $row;
}

/* ================= APPROVED RESERVATIONS FOR VIOLATIONS ================= */
$approvedReservationsSql = "SELECT
        r.reservation_id,
        r.student_id,
        r.reservation_date,
        r.capacity,
        r.space_type,
        u.firstname,
        u.lastname
    FROM tblreservation r
    INNER JOIN tblstudent s ON r.student_id = s.student_id
    INNER JOIN tbluser u ON s.user_id = u.user_id
    LEFT JOIN tblviolation v ON r.reservation_id = v.reservation_id
    WHERE r.status = 'Approved'
    AND v.violation_id IS NULL
    ORDER BY r.reservation_date DESC";

$approvedReservationsResult = pg_query($conn, $approvedReservationsSql);

if (!$approvedReservationsResult) {
    die('Approved reservations query failed: ' . pg_last_error($conn));
}

$approvedReservations = [];

while ($row = pg_fetch_assoc($approvedReservationsResult)) {
    $approvedReservations[] = $row;
}
?>
