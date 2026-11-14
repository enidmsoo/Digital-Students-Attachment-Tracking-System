<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $activity_date = $_POST['activity_date'];
    $activities = trim($_POST['activities']);
    $skills = trim($_POST['skills_acquired']);
    $challenges = trim($_POST['challenges']);
    $lessons = trim($_POST['lessons_learned']);

    if (
        $activity_date === "" ||
        $activities === "" ||
        $skills === "" ||
        $challenges === "" ||
        $lessons === ""
    ) {
        $message = "Please complete all the logbook fields.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO logbook_entries
            (student_id, activity_date, activities,
             skills_acquired, challenges, lessons_learned)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "isssss",
            $student_id,
            $activity_date,
            $activities,
            $skills,
            $challenges,
            $lessons
        );

        if ($stmt->execute()) {
            $message = "Your logbook entry has been saved successfully.";
            $message_type = "success";
        } else {
            $message = "Unable to save your entry. Please try again.";
            $message_type = "error";
        }

        $stmt->close();
    }
}

$stmt = $conn->prepare(
    "SELECT * FROM logbook_entries
     WHERE student_id = ?
     ORDER BY activity_date DESC, id DESC"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$entries = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Logbook | KCA University</title>

    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .logbook-banner {
            background: linear-gradient(120deg, #142b50, #2865a9);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .logbook-banner h1 {
            margin-bottom: 10px;
        }

        .logbook-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .logbook-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .logbook-form .full {
            grid-column: 1 / -1;
        }

        .logbook-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #18365b;
        }

        .logbook-form input,
        .logbook-form textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #dce3ed;
            border-radius: 9px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .logbook-form textarea {
            min-height: 90px;
            resize: vertical;
        }

        .save-logbook {
            background: linear-gradient(135deg, #f3c75e, #e8a92d);
            color: #17365d;
            border: none;
            padding: 14px 24px;
            border-radius: 9px;
            font-weight: 700;
            cursor: pointer;
        }

        .logbook-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .logbook-table th,
        .logbook-table td {
            padding: 14px;
            border-bottom: 1px solid #edf0f5;
            text-align: left;
            font-size: 13px;
        }

        .logbook-table th {
            background: #f3f6fb;
            color: #18365b;
        }

        .pending {
            background: #fff0cf;
            color: #986500;
            padding: 6px 10px;
            border-radius: 20px;
        }

        .success-message {
            background: #e4f8ed;
            color: #187747;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #ffe8e8;
            color: #b42323;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table-scroll {
            overflow-x: auto;
        }

        @media(max-width: 700px) {
            .logbook-form {
                grid-template-columns: 1fr;
            }

            .logbook-form .full {
                grid-column: auto;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-container">

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

            <a href="tracking.php">
                <i class="fas fa-chart-line"></i> Tracking
            </a>

            <a href="logbook.php" class="active">
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

    <main class="main-content">

        <div class="logbook-banner">
            <h1><i class="fas fa-book-open"></i> Digital Logbook</h1>
            <p>Record your daily attachment activities and monitor your learning progress.</p>
        </div>

        <div class="logbook-card">

            <h2>New Logbook Entry</h2>
            <p>Complete the form below to record your daily activities.</p>

            <?php if ($message): ?>
                <div class="<?= $message_type ?>-message">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="logbook-form">

                <div class="full">
                    <label>Activity Date</label>
                    <input type="date" name="activity_date"
                    value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="full">
                    <label>Activities Performed</label>
                    <textarea name="activities"
                    placeholder="Describe the activities you performed..."
                    required></textarea>
                </div>

                <div>
                    <label>Skills Acquired</label>
                    <textarea name="skills_acquired"
                    placeholder="What skills did you learn?"
                    required></textarea>
                </div>

                <div>
                    <label>Challenges Encountered</label>
                    <textarea name="challenges"
                    placeholder="Describe any challenges..."
                    required></textarea>
                </div>

                <div class="full">
                    <label>Lessons Learnt</label>
                    <textarea name="lessons_learned"
                    placeholder="What lessons did you learn?"
                    required></textarea>
                </div>

                <div class="full">
                    <button type="submit" class="save-logbook">
                        <i class="fas fa-save"></i> Save Logbook Entry
                    </button>
                </div>

            </form>

        </div>

        <div class="logbook-card">

            <h2>My Previous Entries</h2>
            <p>View your previously recorded attachment activities.</p>

            <div class="table-scroll">

                <table class="logbook-table">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activities</th>
                            <th>Skills Acquired</th>
                            <th>Supervisor Feedback</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($entries->num_rows > 0): ?>

                        <?php while ($entry = $entries->fetch_assoc()): ?>

                            <tr>
                                <td><?= htmlspecialchars($entry['activity_date']) ?></td>

                                <td><?= nl2br(htmlspecialchars($entry['activities'])) ?></td>

                                <td><?= nl2br(htmlspecialchars($entry['skills_acquired'])) ?></td>

                                <td>
                                    <?= $entry['supervisor_comment']
                                        ? nl2br(htmlspecialchars($entry['supervisor_comment']))
                                        : 'Awaiting feedback' ?>
                                </td>

                                <td>
                                    <span class="pending">
                                        <?= htmlspecialchars($entry['approval_status']) ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5">
                                No logbook entries yet. Add your first entry above.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>