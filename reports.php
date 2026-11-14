<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student record not found.");
}

$progress = max(0, min(100, (int)$student['progress']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports | Digital Student Attachment Tracking System</title>

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

.report-banner {
    background: linear-gradient(120deg, #142b50, #2865a9);
    color: white;
    padding: 30px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.report-banner h1 {
    margin-bottom: 10px;
}

.report-banner p {
    color: #e1eafa;
}

.report-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.print-btn {
    background: #1d5fa7;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 9px;
    cursor: pointer;
    font-weight: 600;
}

.report-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.report-card {
    background: white;
    padding: 23px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.report-card i {
    font-size: 25px;
    color: #2875c7;
    margin-bottom: 13px;
}

.report-card h3 {
    color: #718096;
    font-size: 14px;
    margin-bottom: 8px;
}

.report-card h2 {
    color: #18365f;
    font-size: 25px;
}

.report-section {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}

.report-section h2 {
    color: #18365f;
    margin-bottom: 22px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
}

.report-table th,
.report-table td {
    padding: 15px;
    border-bottom: 1px solid #edf0f5;
    text-align: left;
}

.report-table th {
    color: #64748b;
    width: 35%;
    background: #f8fafc;
}

.report-table td {
    color: #334155;
}

.progress-track {
    height: 13px;
    background: #e5edf6;
    border-radius: 20px;
    overflow: hidden;
    margin-top: 12px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #2875c7, #38bdf8);
    border-radius: 20px;
}

.status-badge {
    display: inline-block;
    padding: 7px 14px;
    background: #e0edff;
    color: #1d5fa7;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

@media(max-width: 900px) {
    .report-grid {
        grid-template-columns: 1fr;
    }

    .report-section {
        overflow-x: auto;
    }
}

@media print {

    .sidebar,
    .report-banner,
    .report-actions {
        display: none !important;
    }

    body {
        background: white;
    }

    .dashboard-container {
        display: block;
    }

    .main-content {
        padding: 0;
        margin: 0;
    }

    .report-card,
    .report-section {
        box-shadow: none;
        border: 1px solid #ddd;
        break-inside: avoid;
    }

    .report-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

</style>
</head>

<body>

<div class="dashboard-container">

<!-- SIDEBAR -->
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

        <a href="appointment.php">
            <i class="fas fa-calendar-check"></i> Appointments
        </a>

        <a href="reports.php" class="active">
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

<!-- MAIN CONTENT -->
<main class="main-content">

    <div class="report-banner">
        <h1><i class="fas fa-chart-pie"></i> Attachment Reports</h1>
        <p>View your attachment summary, progress and placement information.</p>
    </div>

    <div class="report-actions">
        <button class="print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print / Save as PDF
        </button>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="report-grid">

        <div class="report-card">
            <i class="fas fa-user-graduate"></i>
            <h3>Student Name</h3>
            <h2 style="font-size:19px;">
                <?= htmlspecialchars($student['full_name']) ?>
            </h2>
        </div>

        <div class="report-card">
            <i class="fas fa-chart-line"></i>
            <h3>Attachment Progress</h3>
            <h2><?= $progress ?>%</h2>
        </div>

        <div class="report-card">
            <i class="fas fa-clipboard-check"></i>
            <h3>Attachment Status</h3>
            <h2 style="font-size:19px;">
                <?= htmlspecialchars($student['status'] ?: 'Not Started') ?>
            </h2>
        </div>

    </div>

    <!-- STUDENT REPORT -->
    <section class="report-section">

        <h2><i class="fas fa-file-alt"></i> Student Attachment Report</h2>

        <table class="report-table">

            <tr>
                <th>Admission Number</th>
                <td><?= htmlspecialchars($student['admission_no']) ?></td>
            </tr>

            <tr>
                <th>Student Name</th>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
            </tr>

            <tr>
                <th>Course</th>
                <td><?= htmlspecialchars($student['course']) ?></td>
            </tr>

            <tr>
                <th>Company / Organisation</th>
                <td><?= htmlspecialchars($student['company'] ?: 'Not provided') ?></td>
            </tr>

            <tr>
                <th>Workplace Supervisor</th>
                <td><?= htmlspecialchars($student['workplace_supervisor'] ?: 'Not provided') ?></td>
            </tr>

            <tr>
                <th>Attachment Start Date</th>
                <td><?= htmlspecialchars($student['start_date'] ?: 'Not provided') ?></td>
            </tr>

            <tr>
                <th>Attachment End Date</th>
                <td><?= htmlspecialchars($student['end_date'] ?: 'Not provided') ?></td>
            </tr>

            <tr>
                <th>Attachment Status</th>
                <td>
                    <span class="status-badge">
                        <?= htmlspecialchars($student['status'] ?: 'Not Started') ?>
                    </span>
                </td>
            </tr>

            <tr>
                <th>Progress</th>
                <td>
                    <?= $progress ?>%

                    <div class="progress-track">
                        <div class="progress-fill"
                        style="width: <?= $progress ?>%;"></div>
                    </div>
                </td>
            </tr>

            <tr>
                <th>Remarks</th>
                <td>
                    <?= nl2br(htmlspecialchars($student['remarks'] ?: 'No remarks available')) ?>
                </td>
            </tr>

        </table>

    </section>

</main>

</div>

</body>
</html>