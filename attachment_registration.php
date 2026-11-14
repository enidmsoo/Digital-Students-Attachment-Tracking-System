<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company = trim($_POST['company']);
    $supervisor = trim($_POST['workplace_supervisor']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if ($company && $supervisor && $start_date && $end_date) {

        if ($end_date < $start_date) {
            $message = "The end date cannot be earlier than the start date.";
        } else {
            $stmt = $conn->prepare(
                "UPDATE students
                 SET company = ?, workplace_supervisor = ?,
                     start_date = ?, end_date = ?,
                     status = 'Not Started', progress = 0
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "ssssi",
                $company,
                $supervisor,
                $start_date,
                $end_date,
                $_SESSION['student_id']
            );

            if ($stmt->execute() && $stmt->affected_rows >= 0) {
                $message = "Your attachment registration was submitted successfully!";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

    } else {
        $message = "Please complete all the required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attachment Registration | KCA University</title>

<link rel="stylesheet" href="dashboard.css">

<style>
* {
    box-sizing: border-box;
}

body {
    background: #f3f6fc;
}

.form-page {
    max-width: 950px;
    margin: 35px auto;
    padding: 0 20px;
}

.form-banner {
    background: linear-gradient(120deg, #142c59, #3478bd);
    color: white;
    padding: 35px;
    border-radius: 18px 18px 0 0;
}

.form-banner h1 {
    font-size: 25px;
    margin-bottom: 10px;
}

.form-banner p {
    color: #dbe8fa;
    font-size: 13px;
}

.form-card {
    background: white;
    padding: 35px;
    border-radius: 0 0 18px 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.student-info {
    background: #f0f5fc;
    border-left: 4px solid #e9b332;
    padding: 18px;
    border-radius: 9px;
    margin-bottom: 28px;
}

.student-info h3 {
    color: #17335f;
    margin-bottom: 7px;
    font-size: 15px;
}

.student-info p {
    color: #718096;
    font-size: 13px;
}

.form-section-title {
    color: #17335f;
    font-size: 16px;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 23px;
}

.input-group {
    display: flex;
    flex-direction: column;
}

.input-group label {
    color: #344563;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 9px;
}

.input-group input {
    padding: 14px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    outline: none;
    font-size: 13px;
    transition: 0.3s;
    width: 100%;
}

.input-group input:focus {
    border-color: #3478bd;
    box-shadow: 0 0 0 3px rgba(52,120,189,0.12);
}

.full-width {
    grid-column: span 2;
}

.form-note {
    background: #fff8e8;
    color: #88651c;
    padding: 14px;
    border-radius: 9px;
    margin-top: 25px;
    font-size: 12px;
    line-height: 1.6;
}

.submit-btn {
    background: linear-gradient(120deg, #17366b, #3478bd);
    color: white;
    border: none;
    padding: 15px 25px;
    border-radius: 9px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    margin-top: 25px;
    transition: 0.3s;
}

.submit-btn:hover {
    background: #e5ad28;
    color: #142448;
    transform: translateY(-2px);
}

.message {
    background: #e8f7ee;
    color: #207344;
    padding: 14px;
    border-radius: 9px;
    margin-bottom: 22px;
    font-size: 13px;
}

.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #2875ca;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

@media(max-width: 650px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .full-width {
        grid-column: span 1;
    }

    .form-card {
        padding: 22px;
    }
}
</style>
</head>

<body>

<div class="form-page">

    <div class="form-banner">
        <h1>Student Attachment Registration</h1>
        <p>Complete the form below to register your industrial attachment details.</p>
    </div>

    <div class="form-card">

        <div class="student-info">
            <h3>
                Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?>
            </h3>
            <p>Enter your placement details accurately.</p>
        </div>

        <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h3 class="form-section-title">Attachment Placement Details</h3>

        <form method="POST">

            <div class="form-grid">

                <div class="input-group full-width">
                    <label>Company / Organization Name</label>
                    <input type="text" name="company"
                    placeholder="Enter company name" required>
                </div>

                <div class="input-group">
                    <label>Workplace Supervisor</label>
                    <input type="text" name="workplace_supervisor"
                    placeholder="Enter supervisor's name" required>
                </div>

                <div class="input-group">
                    <label>Attachment Start Date</label>
                    <input type="date" name="start_date" required>
                </div>

                <div class="input-group">
                    <label>Attachment End Date</label>
                    <input type="date" name="end_date" required>
                </div>

            </div>

            <div class="form-note">
                Please ensure that the company details and attachment dates
                are correct before submitting your registration.
            </div>

            <button type="submit" class="submit-btn">
                SUBMIT ATTACHMENT REGISTRATION
            </button>

        </form>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>