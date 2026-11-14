<?php
include 'db_connect.php';

// Get total number of students
$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $totalQuery->fetch_assoc()['total'];

// Get ongoing students
$ongoingQuery = $conn->query("SELECT COUNT(*) AS total FROM students WHERE status = 'Ongoing'");
$ongoingStudents = $ongoingQuery->fetch_assoc()['total'];

// Get completed students
$completedQuery = $conn->query("SELECT COUNT(*) AS total FROM students WHERE status = 'Completed'");
$completedStudents = $completedQuery->fetch_assoc()['total'];

// Get students not started
$notStartedQuery = $conn->query("SELECT COUNT(*) AS total FROM students WHERE status = 'Not Started'");
$notStartedStudents = $notStartedQuery->fetch_assoc()['total'];

// Get all student records
$students = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attachment Reports</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Digital Student Attachment Tracking System</h1>
    <p>Attachment Reports</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="register.php">Register Student</a>
    <a href="search.php">Search Student</a>
    <a href="tracking.php">Track Attachment</a>
    <a href="reports.php">Reports</a>
</nav>

<main class="container">

    <h2>Attachment Summary Report</h2>

    <div class="report-cards">

        <div class="report-card">
            <h3>Total Students</h3>
            <p><?php echo $totalStudents; ?></p>
        </div>

        <div class="report-card">
            <h3>Ongoing Attachments</h3>
            <p><?php echo $ongoingStudents; ?></p>
        </div>

        <div class="report-card">
            <h3>Completed Attachments</h3>
            <p><?php echo $completedStudents; ?></p>
        </div>

        <div class="report-card">
            <h3>Not Started</h3>
            <p><?php echo $notStartedStudents; ?></p>
        </div>

    </div>

    <h2>Student Attachment Records</h2>

    <button onclick="window.print()">Print Report</button>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Admission Number</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Company</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Progress</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($students && $students->num_rows > 0): ?>

                    <?php while ($row = $students->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($row['admission_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td><?php echo htmlspecialchars($row['company']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['progress']); ?>%</td>
                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8">No student records available.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Digital Student Attachment Tracking System</p>
</footer>

</body>
</html>