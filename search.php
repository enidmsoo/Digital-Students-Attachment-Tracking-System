<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$search = "";
$students = null;

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    $stmt = $conn->prepare(
        "SELECT admission_no, full_name, course, company,
                workplace_supervisor, start_date, end_date,
                status, progress
         FROM students
         WHERE admission_no LIKE ?
            OR full_name LIKE ?
            OR course LIKE ?
            OR company LIKE ?
         ORDER BY full_name ASC"
    );

    $keyword = "%".$search."%";

    $stmt->bind_param(
        "ssss",
        $keyword,
        $keyword,
        $keyword,
        $keyword
    );

    $stmt->execute();
    $students = $stmt->get_result();

} else {
    $students = $conn->query(
        "SELECT admission_no, full_name, course, company,
                workplace_supervisor, start_date, end_date,
                status, progress
         FROM students ORDER BY full_name ASC"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Search Students | KCA University</title>

<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

.search-header {
    background: linear-gradient(120deg,#17366b,#3478bd);
    padding: 30px;
    border-radius: 16px;
    color: white;
    margin: 28px 0;
}

.search-header h1 {
    font-size: 25px;
    margin-bottom: 10px;
}

.search-header p {
    color: #dce9fa;
    font-size: 13px;
}

.search-box {
    background: white;
    padding: 25px;
    border-radius: 15px;
    display: flex;
    gap: 12px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.04);
}

.search-box input {
    flex: 1;
    padding: 15px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    outline: none;
    font-size: 13px;
}

.search-box input:focus {
    border-color: #3478bd;
}

.search-box button {
    background: #17366b;
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 9px;
    cursor: pointer;
    font-weight: 600;
}

.search-box button:hover {
    background: #e9b332;
    color: #17366b;
}

.results-title {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    align-items: center;
}

.results-title h2 {
    color: #17366b;
    font-size: 17px;
}

.results-title span {
    background: #e9f1fc;
    color: #2875ca;
    padding: 8px 13px;
    border-radius: 20px;
    font-size: 12px;
}

.status-badge {
    padding: 7px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    background: #e9f1fc;
    color: #2875ca;
}

.no-results {
    text-align: center;
    padding: 35px;
    color: #8490a5;
}

.no-results i {
    font-size: 35px;
    margin-bottom: 12px;
    color: #e9b332;
}

@media(max-width:650px) {
    .search-box {
        flex-direction: column;
    }

    .search-box button {
        width: 100%;
    }
}

</style>
</head>

<body>

<div class="dashboard-container">

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="brand">
        <h2>KCA</h2>
        <p>KCA UNIVERSITY</p>
    </div>

    <div class="sidebar-title">MAIN MENU</div>

    <nav>

        <a href="dashboard.php">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="attachment_registration.php">
            <i class="fas fa-file-circle-plus"></i> Register Attachment
        </a>

        <a href="search.php" class="active">
            <i class="fas fa-magnifying-glass"></i> Search Students
        </a>

        <a href="tracking.php">
            <i class="fas fa-chart-line"></i> Tracking
        </a>

        <a href="appointment.php">
            <i class="fas fa-calendar-check"></i> Appointments
        </a>

        <a href="reports.php">
            <i class="fas fa-file-lines"></i> Reports
        </a>

        <a href="settings.php">
            <i class="fas fa-gear"></i> Settings
        </a>

    </nav>

    <div class="sidebar-bottom">
        <a href="logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>
    </div>

</aside>


<!-- MAIN CONTENT -->
<main class="main-content">

    <header class="topbar">

        <div>
            <h2>Search Students</h2>
            <p>Find and view student attachment information</p>
        </div>

        <div class="profile">
            <i class="fas fa-circle-user"></i>
            <span><?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
        </div>

    </header>


    <!-- SEARCH HEADER -->
    <section class="search-header">

        <h1>Student Directory</h1>

        <p>
            Search for students using their admission number,
            name, course or company.
        </p>

    </section>


    <!-- SEARCH FORM -->
    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Enter admission number, student name, course or company..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            <i class="fas fa-search"></i> Search
        </button>

        <a href="search.php" style="padding:14px;text-decoration:none;color:#2875ca;font-size:13px;">
            Reset
        </a>

    </form>


    <!-- SEARCH RESULTS -->
    <section class="content-card">

        <div class="results-title">

            <h2>Student Records</h2>

            <span>
                <?php echo $students ? $students->num_rows : 0; ?> Records Found
            </span>

        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Admission No.</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Company</th>
                        <th>Supervisor</th>
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

                        <td><?php echo htmlspecialchars($row['company'] ?? 'Not assigned'); ?></td>

                        <td><?php echo htmlspecialchars($row['workplace_supervisor'] ?? 'Not assigned'); ?></td>

                        <td><?php echo htmlspecialchars($row['start_date'] ?? '-'); ?></td>

                        <td><?php echo htmlspecialchars($row['end_date'] ?? '-'); ?></td>

                        <td>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>

                        <td><?php echo (int)$row['progress']; ?>%</td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9">
                            <div class="no-results">
                                <i class="fas fa-folder-open"></i>
                                <p>No student records found.</p>
                            </div>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</div>

</body>
</html>