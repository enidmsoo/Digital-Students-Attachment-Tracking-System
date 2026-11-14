<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$message = "";
$error = "";

// Get student's attachment period
$stmt = $conn->prepare(
    "SELECT start_date, end_date FROM students WHERE id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

$start_date = $student['start_date'] ?? "";
$end_date = $student['end_date'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $appointment_date = $_POST['appointment_date'] ?? "";
    $appointment_time = $_POST['appointment_time'] ?? "";
    $purpose = trim($_POST['purpose'] ?? "");

    if (!$start_date || !$end_date) {
        $error = "Please register your attachment details before booking an appointment.";
    } elseif (
        empty($appointment_date) ||
        empty($appointment_time) ||
        empty($purpose)
    ) {
        $error = "Please fill in all the appointment details.";
    } elseif (
        $appointment_date < $start_date ||
        $appointment_date > $end_date
    ) {
        $error = "Choose a date within your attachment period.";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO appointments
            (student_id, appointment_date, appointment_time, purpose)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "isss",
            $student_id,
            $appointment_date,
            $appointment_time,
            $purpose
        );

        if ($stmt->execute()) {
            $message = "Your appointment request has been submitted successfully!";
        } else {
            $error = "Unable to submit your appointment. Please try again.";
        }

        $stmt->close();
    }
}

// Get student's appointments
$stmt = $conn->prepare(
    "SELECT * FROM appointments
     WHERE student_id = ?
     ORDER BY appointment_date DESC"
);

$stmt->bind_param("i", $student_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Appointments | Student Attachment System</title>

<link rel="stylesheet" href="dashboard.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* {
    box-sizing: border-box;
}

body {
    background: #f4f7fc;
}

.main-content {
    padding: 30px;
}

.appointment-banner {
    background: linear-gradient(120deg, #142b50, #2865a9);
    color: white;
    padding: 30px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.appointment-banner h1 {
    margin-bottom: 10px;
}

.appointment-banner p {
    color: #e1eafa;
}

.appointment-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    align-items: start;
}

.appointment-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.appointment-card h2 {
    color: #18365f;
    font-size: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #34445c;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 13px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    font-size: 14px;
}

.form-group textarea {
    height: 110px;
    resize: vertical;
}

.submit-btn {
    background: linear-gradient(120deg, #1d4f91, #3285d5);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 9px;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
}

.success-message {
    background: #e1f8eb;
    color: #167a43;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 18px;
}

.error-message {
    background: #ffe7e7;
    color: #b42323;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 18px;
}

.appointment-item {
    border: 1px solid #e5eaf2;
    padding: 17px;
    border-radius: 12px;
    margin-bottom: 15px;
}

.appointment-item h3 {
    color: #18365f;
    margin-bottom: 10px;
}

.appointment-item p {
    color: #64748b;
    margin: 7px 0;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    background: #fff1cc;
    color: #956400;
}

.status.Approved {
    background: #dcfce7;
    color: #15803d;
}

.status.Rejected {
    background: #fee2e2;
    color: #b91c1c;
}

.empty-message {
    text-align: center;
    padding: 35px 10px;
    color: #8995a7;
}

@media(max-width: 900px) {
    .appointment-container {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="dashboard-container">

<aside class="sidebar">

    <div class="sidebar-brand">
        <h2>KCA</h2>
        <span>Student Portal</span>
    </div>

    <nav class="sidebar-nav">

        <a href="dashboard.php">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="attachment_registration.php">
            <i class="fas fa-file-circle-plus"></i> Register Attachment
        </a>

        <a href="search.php">
            <i class="fas fa-search"></i> Search Students
        </a>

        <a href="tracking.php">
            <i class="fas fa-chart-line"></i> Tracking
        </a>

        <a href="appointment.php" class="active">
            <i class="fas fa-calendar-check"></i> Appointments
        </a>

        <a href="reports.php">
            <i class="fas fa-file-alt"></i> Reports
        </a>

        <a href="settings.php">
            <i class="fas fa-cog"></i> Settings
        </a>

    </nav>

    <div class="sidebar-bottom">
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

</aside>

<main class="main-content">

    <div class="appointment-banner">
        <h1><i class="fas fa-calendar-check"></i> Appointments</h1>
        <p>Schedule and manage your attachment supervision appointments.</p>
    </div>

    <div class="appointment-container">

        <section class="appointment-card">

            <h2><i class="fas fa-calendar-plus"></i> Request an Appointment</h2>

            <?php if ($message): ?>
                <div class="success-message">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($start_date && $end_date): ?>

                <p style="margin-bottom:18px;color:#64748b;">
                    Your attachment period:
                    <strong><?= htmlspecialchars($start_date) ?></strong>
                    to
                    <strong><?= htmlspecialchars($end_date) ?></strong>
                </p>

                <form method="POST" action="appointment.php">

                    <div class="form-group">
                        <label>Preferred Appointment Date</label>
                        <input
                            type="date"
                            name="appointment_date"
                            min="<?= htmlspecialchars($start_date) ?>"
                            max="<?= htmlspecialchars($end_date) ?>"
                            value="2026-09-16"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Preferred Appointment Time</label>
                        <input type="time" name="appointment_time" required>
                    </div>

                    <div class="form-group">
                        <label>Purpose of Appointment</label>
                        <textarea name="purpose" required placeholder="Explain why you need this appointment..."></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Submit Appointment Request
                    </button>

                </form>

            <?php else: ?>

                <div class="error-message">
                    Please register your attachment start date and end date first.
                </div>

                <a href="attachment_registration.php">
                    Register Attachment Details
                </a>

            <?php endif; ?>

        </section>

        <section class="appointment-card">

            <h2><i class="fas fa-history"></i> My Appointments</h2>

            <?php if ($appointments->num_rows > 0): ?>

                <?php while ($appointment = $appointments->fetch_assoc()): ?>

                    <div class="appointment-item">

                        <h3>
                            <i class="fas fa-calendar-day"></i>
                            <?= htmlspecialchars($appointment['appointment_date']) ?>
                        </h3>

                        <p>
                            <i class="fas fa-clock"></i>
                            <?= htmlspecialchars(date("h:i A", strtotime($appointment['appointment_time']))) ?>
                        </p>

                        <p>
                            <strong>Purpose:</strong>
                            <?= nl2br(htmlspecialchars($appointment['purpose'])) ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <span class="status <?= htmlspecialchars($appointment['status']) ?>">
                                <?= htmlspecialchars($appointment['status']) ?>
                            </span>
                        </p>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-message">
                    <i class="fas fa-calendar-xmark"></i>
                    <p>You have not requested any appointments yet.</p>
                </div>

            <?php endif; ?>

        </section>

    </div>

</main>
</div>

<?php
$stmt->close();
?>

</body>
</html>