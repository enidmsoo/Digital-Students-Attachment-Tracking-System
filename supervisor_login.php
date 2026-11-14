<?php
session_start();
require_once "db_connect.php";

$error = "";

if (isset($_SESSION['supervisor_id'])) {
    header("Location: supervisor/dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password_hash, role
             FROM supervisors
             WHERE email = ?"
        );

        if (!$stmt) {
            $error = "Database error: " . $conn->error;

        } else {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                $supervisor = $result->fetch_assoc();

                if (password_verify($password, $supervisor['password_hash'])) {

                    session_regenerate_id(true);

                    $_SESSION['supervisor_id'] = $supervisor['id'];
                    $_SESSION['supervisor_name'] = $supervisor['full_name'];
                    $_SESSION['supervisor_email'] = $supervisor['email'];
                    $_SESSION['supervisor_role'] = $supervisor['role'];

                    $stmt->close();

                    header("Location: supervisor/dashboard.php");
                    exit;

                } else {
                    $error = "Incorrect email or password.";
                }

            } else {
                $error = "No supervisor account found with that email.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>University Supervisor Login</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;

    background:
    linear-gradient(rgba(8,25,55,.88),rgba(8,25,55,.90)),
    url('assets/images/campus-background.png');

    background-size: cover;
    background-position: center;
}

.login-container {
    width: 100%;
    max-width: 460px;
    background: white;
    padding: 42px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,.25);
}

.icon {
    width: 85px;
    height: 85px;
    background: #fff1c7;
    color: #102b55;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    margin: 0 auto 22px;
    font-size: 35px;
    border: 3px solid #d5a529;
}

h2 {
    text-align: center;
    color: #102b55;
    margin-bottom: 10px;
}

.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 30px;
    line-height: 1.6;
    font-size: 14px;
}

label {
    display: block;
    margin-bottom: 9px;
    font-weight: bold;
    color: #333;
}

input {
    width: 100%;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 9px;
    margin-bottom: 22px;
    outline: none;
    font-size: 14px;
}

input:focus {
    border-color: #d5a529;
    box-shadow: 0 0 5px rgba(213,165,41,.2);
}

button {
    width: 100%;
    padding: 15px;
    background: #102b55;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: .3s;
}

button:hover {
    background: #d5a529;
    color: #102b55;
}

.error {
    background: #ffe5e5;
    color: #b00020;
    padding: 13px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
}

.back {
    display: block;
    text-align: center;
    margin-top: 25px;
    text-decoration: none;
    color: #102b55;
    font-size: 14px;
}

.back:hover {
    color: #d5a529;
}

.footer {
    text-align: center;
    margin-top: 25px;
    color: #999;
    font-size: 12px;
}

</style>
</head>

<body>

<div class="login-container">

    <div class="icon">
        <i class="fas fa-user-tie"></i>
    </div>

    <h2>University Supervisor Portal</h2>

    <p class="subtitle">
        Welcome back! Sign in to review student
        attachment activities and monitor progress.
    </p>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your registered email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            LOGIN 
            <i class="fas fa-arrow-right"></i>
        </button>

    </form>

    <a href="index.php" class="back">
        <i class="fas fa-arrow-left"></i>
        Back to Main Portal
    </a>

    <div class="footer">
        Digital Student Attachment Tracking System
    </div>

</div>

</body>
</html>