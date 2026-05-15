<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT 
            r.reservation_id,
            r.student_id,
            r.admin_id,
            r.status,
            r.reservation_date,
            r.capacity,
            r.date_created,
            s.user_id,
            u.email
        FROM tblreservation r
        INNER JOIN tblstudent s ON r.student_id = s.student_id
        INNER JOIN tbluser u ON s.user_id = u.user_id
        ORDER BY r.date_created DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

$total = 0;
$pending = 0;
$approved = 0;
$rejected = 0;
$rows = [];

while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
    $total++;

    if ($row['status'] == 'Pending') {
        $pending++;
    } elseif ($row['status'] == 'Approved') {
        $approved++;
    } elseif ($row['status'] == 'Rejected') {
        $rejected++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildSpace - Admin Reservation Dashboard</title>

    <style>
        :root {
            --black: #000000;
            --dark: #1f1f1f;
            --white: #ffffff;
            --gray: #f5f5f5;
            --text-gray: #666666;
            --border: #e5e5e5;
            --approved: #0f8a3b;
            --approved-bg: #e8f7ee;
            --rejected: #b42318;
            --rejected-bg: #fdecec;
            --pending: #9a6700;
            --pending-bg: #fff4d6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--white);
            color: var(--black);
            min-height: 100vh;
        }

        .navbar {
            width: 100%;
            padding: 24px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background: var(--white);
        }

        .nav-left,
        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-link {
            text-decoration: none;
            color: var(--black);
            font-size: 15px;
            font-weight: 600;
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .delete-account-btn {
            background: var(--black);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: 28px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .logout-btn {
            background: var(--gray);
            color: var(--black);
            border: 1px solid var(--border);
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 28px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.10);
        }

        .delete-account-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .dashboard-wrapper {
            padding: 50px 70px;
            max-width: 1450px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            margin-bottom: 35px;
        }

        .dashboard-title h1 {
            font-size: 44px;
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 10px;
        }

        .dashboard-title p {
            color: var(--text-gray);
            font-size: 16px;
            max-width: 700px;
        }

        .create-btn {
            background: var(--black);
            color: var(--white);
            text-decoration: none;
            padding: 14px 26px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            transition: 0.2s ease;
        }

        .create-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.18);
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: var(--gray);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 22px;
        }

        .summary-card span {
            display: block;
            font-size: 13px;
            color: var(--text-gray);
            margin-bottom: 8px;
        }

        .summary-card strong {
            font-size: 28px;
            letter-spacing: -1px;
        }

        .table-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0,0,0,0.06);
        }

        .table-header {
            padding: 24px 28px;
            background: var(--black);
            color: var(--white);
        }

        .table-header h2 {
            font-size: 22px;
            letter-spacing: -0.5px;
        }

        .table-header p {
            color: #cccccc;
            font-size: 14px;
            margin-top: 4px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            background: #fafafa;
            text-align: left;
            padding: 18px 20px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-gray);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            font-size: 15px;
            vertical-align: middle;
        }

        tr:hover {
            background: #fafafa;
        }

        .email-text {
            font-weight: 600;
        }

        .muted {
            color: var(--text-gray);
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-pending {
            background: var(--pending-bg);
            color: var(--pending);
        }

        .status-approved {
            background: var(--approved-bg);
            color: var(--approved);
        }

        .status-rejected {
            background: var(--rejected-bg);
            color: var(--rejected);
        }

        .status-cancelled {
            background: #eeeeee;
            color: #555555;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            transition: 0.2s ease;
            border: 1px solid transparent;
        }

        .approve-btn {
            background: var(--approved-bg);
            color: var(--approved);
        }

        .reject-btn {
            background: var(--rejected-bg);
            color: var(--rejected);
        }

        .delete-btn {
            background: var(--black);
            color: var(--white);
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .empty-action {
            color: var(--text-gray);
            font-size: 13px;
            margin-right: 6px;
        }

        @media (max-width: 900px) {
            .navbar {
                padding: 20px;
                flex-direction: column;
                gap: 16px;
            }

            .nav-left,
            .nav-right {
                gap: 20px;
            }

            .dashboard-wrapper {
                padding: 35px 20px;
            }

            .dashboard-header {
                flex-direction: column;
            }

            .dashboard-title h1 {
                font-size: 34px;
            }

            .summary-cards {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 520px) {
            .summary-cards {
                grid-template-columns: 1fr;
            }

            .dashboard-title h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="admin_reservations.php" class="nav-link">Admin Dashboard</a>
    </div>

    <div class="logo">
        <div class="logo-text">WildSpace</div>
    </div>

    <div class="nav-right">
        <a href="edit_profile.php" class="nav-link">Edit Profile</a>

        <a href="../actions/admin/logout.php" class="logout-btn"
        onclick="return confirm('Are you sure you want to log out?');">
            Logout
        </a>

        <form action="../actions/admin/delete_account.php" method="POST" style="display:inline;"
            onsubmit="return confirm('Are you sure you want to delete your admin account? This action cannot be undone.');">
            <button type="submit" name="delete_account" class="delete-account-btn">
                Delete Account
            </button>
        </form>
    </div>
</nav>

<main class="dashboard-wrapper">
    <section class="dashboard-header">
        <div class="dashboard-title">
            <h1>Reservation Dashboard</h1>
        </div>

        <a href="admin_create_reservation.php" class="create-btn">+ Create Reservation</a>
    </section>

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
            <p>Latest reservations from the WildSpace database</p>
        </div>

        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Student/User Email</th>
                    <th>Date</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($rows as $row) { ?>
                    <?php
                        $statusClass = 'status-pending';

                        if ($row['status'] == 'Approved') {
                            $statusClass = 'status-approved';
                        } elseif ($row['status'] == 'Rejected') {
                            $statusClass = 'status-rejected';
                        } elseif ($row['status'] == 'Cancelled') {
                            $statusClass = 'status-cancelled';
                        }
                    ?>

                    <tr>
                        <td>#<?php echo htmlspecialchars($row['reservation_id']); ?></td>

                        <td>
                            <div class="email-text"><?php echo htmlspecialchars($row['email']); ?></div>
                            <div class="muted">User ID: <?php echo htmlspecialchars($row['user_id']); ?></div>
                        </td>

                        <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>

                        <td><?php echo htmlspecialchars($row['capacity']); ?> seats</td>

                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>

                        <td>
                            <span class="muted"><?php echo htmlspecialchars($row['date_created']); ?></span>
                        </td>

                        <td>
                            <div class="actions">
                                <?php if ($row['status'] == 'Pending') { ?>
                                    <a class="action-btn approve-btn"
                                       href="../actions/admin/update_reservation_status.php?id=<?php echo $row['reservation_id']; ?>&status=Approved"
                                       onclick="return confirm('Approve this reservation?');">
                                       Approve
                                    </a>

                                    <a class="action-btn reject-btn"
                                       href="../actions/admin/update_reservation_status.php?id=<?php echo $row['reservation_id']; ?>&status=Rejected"
                                       onclick="return confirm('Reject this reservation?');">
                                       Reject
                                    </a>
                                <?php } else { ?>
                                    <span class="empty-action">Reviewed</span>
                                <?php } ?>

                                <a class="action-btn delete-btn"
                                   href="../actions/admin/admin_delete_reservation.php?id=<?php echo $row['reservation_id']; ?>"
                                   onclick="return confirm('Delete this reservation permanently?');">
                                   Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>
</main>

</body>
</html>