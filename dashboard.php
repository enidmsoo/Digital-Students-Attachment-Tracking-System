<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? 'Student';
$admission_no = $_SESSION['admission_no'] ?? '';

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Get logbook statistics
$stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(approval_status = 'Approved') AS approved
    FROM logbook_entries
    WHERE student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$logbook_stats = $stmt->get_result()->fetch_assoc();

$total_logbooks = $logbook_stats['total'] ?? 0;
$approved_logbooks = $logbook_stats['approved'] ?? 0;

// Get unread notifications
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM student_notifications
    WHERE student_id = ? AND is_read = 0
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$notification_count = $stmt->get_result()->fetch_assoc()['total'];

// Get recent logbook activities
$stmt = $conn->prepare("
    SELECT activity_date, activities, approval_status
    FROM logbook_entries
    WHERE student_id = ?
    ORDER BY activity_date DESC, created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$recent_activities = $stmt->get_result();

// Attachment progress
$progress = (int)($student['progress'] ?? 0);
$progress = max(0, min(100, $progress));

$status = $student['status'] ?? 'Not Started';

function statusClass($status) {
    if ($status === 'Completed') return 'completed';
    if ($status === 'Ongoing') return 'ongoing';
    return 'not-started';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Dashboard | KCA University</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #f4f6fb;
    color: #263247;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #102b50, #071a33);
    position: fixed;
    left: 0;
    top: 0;
    padding: 25px 18px;
    color: white;
    overflow-y: auto;
}

.logo {
    text-align: center;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.logo-icon {
    width: 58px;
    height: 58px;
    background: #dba928;
    color: #102b50;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    margin: 0 auto 12px;
}

.logo h2 {
    font-size: 19px;
    margin-bottom: 6px;
}

.logo p {
    font-size: 10px;
    color: #d9e1ec;
    line-height: 1.5;
}

.menu {
    margin-top: 28px;
}

.menu a {
    display: block;
    text-decoration: none;
    color: #dce5f1;
    padding: 13px 14px;
    margin-bottom: 7px;
    border-radius: 9px;
    font-size: 13px;
    transition: 0.3s;
}

.menu a:hover,
.menu a.active {
    background: #dba928;
    color: #102b50;
    font-weight: bold;
}

.menu .logout {
    margin-top: 20px;
    color: #ffb7b7;
}

/* MAIN CONTENT */
.main {
    margin-left: 260px;
    padding: 30px;
}

.topbar {
    background: white;
    padding: 20px 25px;
    border-radius: 13px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    margin-bottom: 28px;
}

.topbar h2 {
    color: #102b50;
    font-size: 22px;
}

.profile {
    text-align: right;
}

.profile strong {
    color: #102b50;
}

.profile p {
    color: #788397;
    font-size: 12px;
    margin-top: 4px;
}

/* WELCOME */
.welcome {
    background: linear-gradient(110deg, #102b50, #24558a);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 28px;
}

.welcome h1 {
    font-size: 25px;
    margin-bottom: 10px;
}

.welcome p {
    color: #e0e9f4;
    font-size: 14px;
    line-height: 1.6;
}

/* PROGRESS */
.progress-panel {
    background: white;
    padding: 24px;
    border-radius: 13px;
    margin-bottom: 28px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
}

.progress-heading {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    gap: 10px;
}

.progress-heading h3 {
    color: #102b50;
}

.progress-heading strong {
    color: #c18b12;
}

.progress-bar {
    height: 12px;
    background: #e9edf3;
    border-radius: 20px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #dba928, #f0c75e);
    border-radius: 20px;
}

/* STATISTICS */
.section-title {
    color: #102b50;
    margin-bottom: 18px;
    font-size: 19px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}

.card {
    background: white;
    padding: 23px;
    border-radius: 13px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    border-left: 5px solid;
}

.card .icon {
    font-size: 27px;
    margin-bottom: 14px;
}

.card h3 {
    font-size: 26px;
    margin-bottom: 7px;
    color: #102b50;
}

.card p {
    font-size: 13px;
    color: #778397;
}

.blue { border-color: #3478d4; }
.orange { border-color: #ed9c32; }
.green { border-color: #28a66a; }
.red { border-color: #dc5b5b; }

/* PANELS */
.panel {
    background: white;
    padding: 24px;
    border-radius: 13px;
    margin-bottom: 25px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid #edf0f5;
    font-size: 13px;
}

th {
    color: #52647c;
    background: #f7f9fc;
}

.status {
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.Pending {
    background: #fff1d6;
    color: #a66a00;
}

.Approved {
    background: #dff5e9;
    color: #16834d;
}

.Correction {
    background: #ffe1e1;
    color: #bd3535;
}

/* QUICK ACTIONS */
.actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.action {
    display: block;
    padding: 20px;
    background: #f5f8fc;
    border-radius: 11px;
    text-decoration: none;
    color: #102b50;
    border: 1px solid #e8edf5;
    transition: 0.3s;
}

.action:hover {
    background: #eaf1fa;
    transform: translateY(-3px);
}

.action span {
    display: block;
    font-size: 27px;
    margin-bottom: 10px;
}

.action strong {
    font-size: 13px;
}

/* RESPONSIVE */
@media (max-width: 1100px) {
    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .actions {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .cards, .actions {
        grid-template-columns: 1fr;
    }

    .topbar {
        flex-wrap: wrap;
        gap: 15px;
    }

    .panel {
        overflow-x: auto;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="logo">
        <div class="logo-icon">📚</div>
        <h2>KCA UNIVERSITY</h2>
        <p>DIGITAL STUDENT ATTACHMENT TRACKING SYSTEM</p>
    </div>

    <nav class="menu">

        <a href="dashboard.php" class="active">🏠 Dashboard</a>

        <a href="attachment_registration.php">📝 Register Attachment</a>

        <a href="search.php">🔍 Search Students</a>

        <a href="tracking.php">📈 Attachment Tracking</a>

        <a href="logbook.php">📖 Digital Logbook</a>

        <a href="appointment.php">📅 Appointments</a>

        <a href="reports.php">📊 Reports</a>

        <a href="notifications.php">🔔 Notifications
            (<?php echo $notification_count; ?>)
        </a>

        <a href="settings.php">⚙️ Settings</a>

        <a href="logout.php" class="logout">🚪 Logout</a>

    </nav>

</aside>

<!-- MAIN CONTENT -->
<main class="main">

    <header class="topbar">

        <h2>Student Dashboard</h2>

        <div class="profile">
            <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>
            <p><?php echo htmlspecialchars($student['admission_no']); ?></p>
        </div>

    </header>

    <section class="welcome">

        <h1>Welcome, <?php echo htmlspecialchars($student['full_name']); ?>! 👋</h1>

        <p>
            Welcome to your Digital Student Attachment Tracking System.
            Monitor your attachment progress, update your logbook and
            stay connected with your university supervisor.
        </p>

    </section>

    <!-- ATTACHMENT PROGRESS -->

    <section class="progress-panel">

        <div class="progress-heading">
            <h3>My Attachment Progress</h3>
            <strong><?php echo $progress; ?>%</strong>
        </div>

        <div class="progress-bar">
            <div class="progress-fill"
                 style="width: <?php echo $progress; ?>%;">
            </div>
        </div>

    </section>

    <h3 class="section-title">My Attachment Overview</h3>

    <section class="cards">

        <div class="card blue">
            <div class="icon">📈</div>
            <h3><?php echo $progress; ?>%</h3>
            <p>Attachment Progress</p>
        </div>

        <div class="card orange">
            <div class="icon">📌</div>
            <h3><?php echo htmlspecialchars($status); ?></h3>
            <p>Attachment Status</p>
        </div>

        <div class="card green">
            <div class="icon">📖</div>
            <h3><?php echo $total_logbooks; ?></h3>
            <p>Logbook Entries</p>
        </div>

        <div class="card red">
            <div class="icon">🔔</div>
            <h3><?php echo $notification_count; ?></h3>
            <p>Unread Notifications</p>
        </div>

    </section>

    <!-- RECENT ACTIVITIES -->

    <section class="panel">

        <h3 class="section-title">My Recent Logbook Activities</h3>

        <table>

            <thead>
                <tr>
                    <th>Activity Date</th>
                    <th>Activities</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($recent_activities->num_rows > 0): ?>

                <?php while ($row = $recent_activities->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row['activity_date']); ?>
                        </td>

                        <td>
                            <?php
                            $activity = $row['activities'];
                            echo htmlspecialchars(
                                strlen($activity) > 65
                                ? substr($activity, 0, 65) . '...'
                                : $activity
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            $activity_status = $row['approval_status'];

                            if ($activity_status === 'Correction Required') {
                                echo '<span class="status Correction">Correction Required</span>';
                            } else {
                                echo '<span class="status ' .
                                    htmlspecialchars($activity_status) . '">' .
                                    htmlspecialchars($activity_status) .
                                    '</span>';
                            }
                            ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="3">You have not submitted any logbook entries yet.</td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </section>

    <!-- QUICK ACTIONS -->

    <section class="panel">

        <h3 class="section-title">Quick Actions</h3>

        <div class="actions">

            <a href="attachment_registration.php" class="action">
                <span>📝</span>
                <strong>Register Attachment</strong>
            </a>

            <a href="logbook.php" class="action">
                <span>📖</span>
                <strong>Update Logbook</strong>
            </a>

            <a href="appointment.php" class="action">
                <span>📅</span>
                <strong>Book Appointment</strong>
            </a>

            <a href="reports.php" class="action">
                <span>📊</span>
                <strong>View Reports</strong>
            </a>

        </div>

    </section>

</main>

</body>
</html>