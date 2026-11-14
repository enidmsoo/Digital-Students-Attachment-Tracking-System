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

// Retrieve student information
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student account not found.");
}

// Update account information
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $phone_number = trim($_POST['phone_number'] ?? "");
    $current_password = $_POST['current_password'] ?? "";
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";

    if ($full_name === "" || $email === "" || $phone_number === "") {
        $error = "Please fill in all required account details.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";

    } else {

        // Check whether another student uses this email
        $check = $conn->prepare(
            "SELECT id FROM students WHERE email = ? AND id != ?"
        );

        $check->bind_param("si", $email, $student_id);
        $check->execute();
        $duplicate = $check->get_result()->num_rows > 0;
        $check->close();

        if ($duplicate) {
            $error = "This email address is already being used by another account.";

        } else {

            $change_password = (
                $current_password !== "" ||
                $new_password !== "" ||
                $confirm_password !== ""
            );

            if ($change_password) {

                if (
                    $current_password === "" ||
                    $new_password === "" ||
                    $confirm_password === ""
                ) {
                    $error = "Complete all three password fields to change your password.";

                } elseif (!password_verify($current_password, $student['password_hash'])) {
                    $error = "Your current password is incorrect.";

                } elseif (strlen($new_password) < 8) {
                    $error = "Your new password must have at least 8 characters.";

                } elseif ($new_password !== $confirm_password) {
                    $error = "The new passwords do not match.";

                } else {

                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $update = $conn->prepare(
                        "UPDATE students
                         SET full_name = ?, email = ?, phone_number = ?,
                             password_hash = ?
                         WHERE id = ?"
                    );

                    $update->bind_param(
                        "ssssi",
                        $full_name,
                        $email,
                        $phone_number,
                        $password_hash,
                        $student_id
                    );

                    if ($update->execute()) {
                        $message = "Your account and password have been updated successfully!";
                    } else {
                        $error = "Unable to update your account.";
                    }

                    $update->close();
                }

            } else {

                $update = $conn->prepare(
                    "UPDATE students
                     SET full_name = ?, email = ?, phone_number = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "sssi",
                    $full_name,
                    $email,
                    $phone_number,
                    $student_id
                );

                if ($update->execute()) {
                    $message = "Your account information has been updated successfully!";
                } else {
                    $error = "Unable to update your account.";
                }

                $update->close();
            }
        }
    }

    // Refresh displayed account details
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $_SESSION['student_name'] = $student['full_name'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Settings | Digital Student Attachment Tracking System</title>

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

.settings-banner {
    background: linear-gradient(120deg, #142b50, #2865a9);
    color: white;
    padding: 30px;
    border-radius: 18px;
    margin-bottom: 25px;
}

.settings-banner h1 {
    margin-bottom: 10px;
}

.settings-banner p {
    color: #e1eafa;
}

.settings-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    align-items: start;
}

.settings-card {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.settings-card h2 {
    color: #18365f;
    margin-bottom: 22px;
    font-size: 20px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #34445c;
    font-weight: 600;
}

.form-group input {
    width: 100%;
    padding: 13px;
    border: 1px solid #dce3ed;
    border-radius: 9px;
    font-size: 14px;
}

.form-group input:focus {
    outline: none;
    border-color: #2875c7;
    box-shadow: 0 0 0 3px rgba(40,117,199,0.1);
}

.form-group small {
    display: block;
    color: #8491a5;
    margin-top: 6px;
}

.save-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 9px;
    color: white;
    background: linear-gradient(120deg, #1d4f91, #3285d5);
    font-weight: 600;
    cursor: pointer;
    font-size: 15px;
}

.save-btn:hover {
    opacity: 0.9;
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

.account-info {
    background: #f4f7fc;
    padding: 18px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.account-info p {
    margin: 10px 0;
    color: #475569;
}

.account-info strong {
    color: #18365f;
}

@media(max-width: 900px) {
    .settings-container {
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

        <a href="reports.php">
            <i class="fas fa-file-alt"></i> Reports
        </a>

        <a href="settings.php" class="active">
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

    <div class="settings-banner">
        <h1><i class="fas fa-cog"></i> Account Settings</h1>
        <p>Manage your personal information and account security.</p>
    </div>

    <?php if ($message): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>


    <div class="settings-container">

        <!-- PERSONAL INFORMATION -->
        <section class="settings-card">

            <h2><i class="fas fa-user-edit"></i> Personal Information</h2>

            <div class="account-info">

                <p>
                    <strong>Admission Number:</strong>
                    <?= htmlspecialchars($student['admission_no']) ?>
                </p>

                <p>
                    <strong>Course:</strong>
                    <?= htmlspecialchars($student['course']) ?>
                </p>

            </div>

            <form method="POST" action="settings.php">

                <div class="form-group">
                    <label>Full Name</label>
                    <input
                        type="text"
                        name="full_name"
                        value="<?= htmlspecialchars($student['full_name']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($student['email']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input
                        type="tel"
                        name="phone_number"
                        value="<?= htmlspecialchars($student['phone_number']) ?>"
                        required>
                </div>

                <button type="submit" class="save-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>

            </form>

        </section>


        <!-- PASSWORD SETTINGS -->
        <section class="settings-card">

            <h2><i class="fas fa-lock"></i> Change Password</h2>

            <p style="color:#718096;margin-bottom:22px;">
                Update your password regularly to keep your account secure.
            </p>

            <form method="POST" action="settings.php">

                <input type="hidden" name="full_name"
                value="<?= htmlspecialchars($student['full_name']) ?>">

                <input type="hidden" name="email"
                value="<?= htmlspecialchars($student['email']) ?>">

                <input type="hidden" name="phone_number"
                value="<?= htmlspecialchars($student['phone_number']) ?>">

                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" minlength="8" required>
                    <small>Use at least 8 characters.</small>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" minlength="8" required>
                </div>

                <button type="submit" class="save-btn">
                    <i class="fas fa-key"></i> Update Password
                </button>

            </form>

        </section>

    </div>

</main>

</div>

</body>
</html>