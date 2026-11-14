<?php
session_start();
require_once "db_connect.php";

$error = "";

if (isset($_SESSION['supervisor_id'])) {
    header("Location: supervisor/dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password_hash, role
             FROM supervisors
             WHERE email = ?"
        );

        if (!$stmt) {
            $error = "Database query error: " . $conn->error;

        } else {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $stmt->bind_result(
                $id,
                $full_name,
                $stored_email,
                $password_hash,
                $role
            );

            if ($stmt->fetch()) {

                if (password_verify($password, $password_hash)) {

                    session_regenerate_id(true);

                    $_SESSION['supervisor_id'] = $id;
                    $_SESSION['supervisor_name'] = $full_name;
                    $_SESSION['supervisor_email'] = $stored_email;
                    $_SESSION['supervisor_role'] = $role;

                    $stmt->close();

                    header("Location: supervisor/dashboard.php");
                    exit;

                } else {
                    $error = "Incorrect password. Please try again.";
                }

            } else {
                $error = "No supervisor account was found with that email.";
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
    max-width: 450px;
    background: white;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 12px 35px rgba(0,0,0,.25);
}

.icon {
    width: 80px;
    height: 80px;
    background: #d5a529;
    color: #102b55;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    margin: 0 auto 20px;
    font-size: 32px;
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
    line-height: 1.5;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

input {
    width: 100%;
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 20px;
    outline: none;
}

input:focus {
    border-color: #d5a529;
}

button {
    width: 100%;
    padding: 14px;
    background: #102b55;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
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
}

.back {
    display: block;
    text-align: center;
    margin-top: 25px;
    text-decoration: none;
    color: #102b55;
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
        Sign in to monitor students, review logbooks
        and provide attachment feedback.
    </p>

    <?php if (!empty($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your registered email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
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
            LOGIN TO SUPERVISOR PORTAL
            <i class="fas fa-arrow-right"></i>
        </button>

    </form>

    <a href="index.php" class="back">
        <i class="fas fa-arrow-left"></i>
        Back to Main Portal
    </a>

</div>

</body>
</html>