<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare(
    "SELECT id, admission_no, full_name, course, company,
            workplace_supervisor, start_date, end_date,
            status, progress, remarks
     FROM students
     WHERE id = ?"
);

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

    <title>Attachment Tracking | KCA University</title>

    <link rel="stylesheet" href="dashboard.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .tracking-banner {
            background: linear-gradient(120deg, #142b50, #2865a9);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .tracking-banner h1 {
            margin-bottom: 10px;
        }

        .tracking-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .tracking-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-box {
            padding: 17px;
            background: #f5f8fc;
            border-radius: 10px;
            border-left: 4px solid #e8ad35;
        }

        .detail-box small {
            display: block;
            color: #718096;
            margin-bottom: 7px;
        }

        .detail-box strong {
            color: #18365b;
        }

        .progress-track {
            background: #e8edf5;
            height: 15px;
            border-radius: 20px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #e8ad35, #f4ce70);
            border-radius: 20px;
            transition: width 0.4s ease;
        }

        .tracking-button {
            display: inline-block;
            background: linear-gradient(135deg, #f3c75e, #e8a92d);
            color: #17365d;
            padding: 13px 22px;
            border-radius: 9px;
            text-decoration: none;
            font-weight: 700;
            margin-top: 15px;
        }

        .status-label {
            display: inline-block;
            background: #e5efff;
            color: #24548c;
            padding: 7px 13px;
            border-radius: 20px;
            font-weight: 600;
        }

        .tracking-note {
            background: #fff7e5;
            border-left: 4px solid #e8ad35;
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
            color: #70531b;
        }

        @media(max-width: 700px) {
            .tracking-grid {
                grid-template-columns: 1fr;
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
            <span>Student Attachment System</span>
        </div>

        <nav class="sidebar-nav">

            <a href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>

            <a href="attachment_registration.php">
                <i class="fas fa-file-signature"></i> Register Attachment
            </a>

            <a href="search.php">
                <i class="fas fa-search"></i> Search Students
            </a>

            <a href="tracking.php" class="active">
                <i class="fas fa-chart-line"></i> Tracking
            </a>

            <a href="logbook.php">
                <i class="fas fa-book"></i> Digital Logbook
            </a>

            <a href="appointment.php">
                <i class="fas fa-calendar-check"></i> Appointments
            </a>

            <a href="reports.php">
                <i class="fas fa-chart-bar"></i> Reports
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

        <div class="tracking-banner">

            <h1>
                <i class="fas fa-chart-line"></i>
                Attachment Tracking
            </h1>

            <p>
                Monitor your industrial attachment placement
                and progress throughout your attachment period.
            </p>

        </div>

        <?php if (empty($student['company'])): ?>

            <div class="tracking-card">

                <h2>Attachment Not Registered</h2>

                <p>
                    You have not registered your attachment
                    placement details yet.
                </p>

                <a href="attachment_registration.php"
                   class="tracking-button">
                    Register Attachment
                </a>

            </div>

        <?php else: ?>

            <div class="tracking-card">

                <h2>Student Information</h2>

                <div class="tracking-grid">

                    <div class="detail-box">
                        <small>Student Name</small>
                        <strong>
                            <?= htmlspecialchars($student['full_name']) ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Admission Number</small>
                        <strong>
                            <?= htmlspecialchars($student['admission_no']) ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Course</small>
                        <strong>
                            <?= htmlspecialchars($student['course']) ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Attachment Company</small>
                        <strong>
                            <?= htmlspecialchars($student['company']) ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Workplace Supervisor</small>
                        <strong>
                            <?= htmlspecialchars($student['workplace_supervisor'] ?? 'Not provided') ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Attachment Status</small>
                        <span class="status-label">
                            <?= htmlspecialchars($student['status']) ?>
                        </span>
                    </div>

                    <div class="detail-box">
                        <small>Starting Date</small>
                        <strong>
                            <?= htmlspecialchars($student['start_date'] ?? 'Not provided') ?>
                        </strong>
                    </div>

                    <div class="detail-box">
                        <small>Ending Date</small>
                        <strong>
                            <?= htmlspecialchars($student['end_date'] ?? 'Not provided') ?>
                        </strong>
                    </div>

                </div>

            </div>

            <div class="tracking-card">

                <h2>Attachment Progress</h2>

                <p>
                    Your current recorded attachment progress:
                    <strong><?= $progress ?>%</strong>
                </p>

                <div class="progress-track">

                    <div class="progress-fill"
                         style="width: <?= $progress ?>%;">
                    </div>

                </div>

                <p><?= $progress ?>% Completed</p>

                <div class="tracking-note">

                    <i class="fas fa-info-circle"></i>

                    Keep your digital logbook updated
                    with your daily activities to maintain
                    a record of your attachment experience.

                </div>

                <a href="logbook.php" class="tracking-button">
                    <i class="fas fa-book"></i>
                    Open Digital Logbook
                </a>

            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>