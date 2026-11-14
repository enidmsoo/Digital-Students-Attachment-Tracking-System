<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = (int) $_SESSION['student_id'];

// Mark a notification as read when the student opens it.
if (isset($_GET['read'])) {
    $notification_id = (int) $_GET['read'];

    $stmt = $conn->prepare(
        "UPDATE student_notifications
         SET is_read = 1
         WHERE id = ? AND student_id = ?"
    );
    $stmt->bind_param("ii", $notification_id, $student_id);
    $stmt->execute();
    $stmt->close();

    header("Location: notifications.php");
    exit();
}

// Get notifications belonging to this student only.
$stmt = $conn->prepare(
    "SELECT id, logbook_entry_id, message, is_read, created_at
     FROM student_notifications
     WHERE student_id = ?
     ORDER BY created_at DESC"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$notifications = $stmt->get_result();

$unread_count = 0;
$notification_rows = [];

while ($row = $notifications->fetch_assoc()) {
    $notification_rows[] = $row;

    if ((int)$row['is_read'] === 0) {
        $unread_count++;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Notifications</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f3f5fb;
    color: #26324a;
}

.sidebar {
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #101d3b, #263e68);
    position: fixed;
    left: 0;
    top: 0;
    padding: 30px 18px;
    color: white;
}

.sidebar h2 {
    color: #f4c65e;
    margin-bottom: 35px;
    font-size: 21px;
}

.sidebar a {
    display: block;
    color: #e3e9f5;
    text-decoration: none;
    padding: 14px;
    margin-bottom: 9px;
    border-radius: 9px;
}

.sidebar a:hover,
.sidebar a.active {
    background: #f4c65e;
    color: #172442;
}

.main {
    margin-left: 250px;
    padding: 35px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.topbar h1 {
    font-size: 28px;
}

.subtitle {
    color: #78849a;
    margin-top: 8px;
}

.count-card {
    background: white;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(25,42,75,0.06);
    margin-bottom: 25px;
}

.count-card strong {
    color: #244b83;
    font-size: 26px;
}

.notification {
    background: white;
    padding: 22px;
    border-radius: 13px;
    margin-bottom: 15px;
    display: flex;
    gap: 18px;
    align-items: flex-start;
    border-left: 5px solid #d4dbe8;
    box-shadow: 0 4px 15px rgba(25,42,75,0.04);
}

.notification.unread {
    border-left-color: #e8b844;
    background: #fffdf7;
}

.icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: #e8efff;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 21px;
    flex-shrink: 0;
}

.details {
    flex: 1;
}

.details p {
    margin: 9px 0;
    line-height: 1.6;
    color: #56647b;
}

.details small {
    color: #8791a3;
}

.read-link {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    color: #244b83;
    font-weight: bold;
    font-size: 13px;
}

.empty {
    background: white;
    padding: 55px 20px;
    text-align: center;
    border-radius: 13px;
    color: #778399;
}

.empty h2 {
    color: #263b61;
    margin-bottom: 10px;
}

@media(max-width: 800px) {
    .sidebar {
        width: 190px;
    }

    .main {
        margin-left: 190px;
        padding: 20px;
    }
}
</style>
</head>

<body>

<aside class="sidebar">
    <h2>Student Portal</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="attachment_registration.php">Register Attachment</a>
    <a href="search.php">Search Students</a>
    <a href="tracking.php">Tracking</a>
    <a href="appointment.php">Appointments</a>
    <a href="logbook.php">Digital Logbook</a>
    <a href="notifications.php" class="active">Notifications</a>
    <a href="reports.php">Reports</a>
    <a href="settings.php">Settings</a>
    <a href="logout.php">Logout</a>
</aside>

<main class="main">

    <div class="topbar">
        <div>
            <h1>My Notifications</h1>
            <p class="subtitle">
                Updates and feedback from your University Supervisor and Assessor.
            </p>
        </div>
    </div>

    <div class="count-card">
        <strong><?php echo $unread_count; ?></strong>
        <p>Unread Notifications</p>
    </div>

    <?php if (count($notification_rows) > 0): ?>

        <?php foreach ($notification_rows as $notice): ?>

            <div class="notification <?php echo $notice['is_read'] ? '' : 'unread'; ?>">

                <div class="icon">🔔</div>

                <div class="details">

                    <strong>
                        <?php echo $notice['is_read'] ? 'Notification' : 'New Notification'; ?>
                    </strong>

                    <p>
                        <?php echo nl2br(htmlspecialchars($notice['message'])); ?>
                    </p>

                    <small>
                        Received:
                        <?php echo htmlspecialchars($notice['created_at']); ?>
                    </small>

                    <?php if (!(int)$notice['is_read']): ?>
                        <br>
                        <a class="read-link"
                           href="notifications.php?read=<?php echo (int)$notice['id']; ?>">
                            Mark as Read
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty">
            <h2>You're All Caught Up!</h2>
            <p>You don't have any notifications yet.</p>
            <p>Supervisor feedback and logbook updates will appear here.</p>
        </div>

    <?php endif; ?>

</main>

</body>
</html>