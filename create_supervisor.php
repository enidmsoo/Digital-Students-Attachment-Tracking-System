<?php
require_once "db_connect.php";

$message = "";
$error = "";

// Temporary setup code. Change this before opening the page.
$setup_code = "KCASetup2026!";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $entered_code = $_POST['setup_code'] ?? "";
    $full_name = trim($_POST['full_name'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $phone = trim($_POST['phone_number'] ?? "");
    $password = $_POST['password'] ?? "";
    $role = $_POST['role'] ?? "";

    if ($entered_code !== $setup_code) {
        $error = "Invalid setup code.";

    } elseif (
        $full_name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === "" ||
        !in_array($role, ["Supervisor", "Assessor"], true)
    ) {
        $error = "Please complete all required fields correctly.";

    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM supervisors WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email already exists.";

        } else {

            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO supervisors
                (full_name, email, phone_number, password_hash, role)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $full_name,
                $email,
                $phone,
                $password_hash,
                $role
            );

            if ($stmt->execute()) {
                $message = "Account created successfully. You can now log in.";
            } else {
                $error = "Account creation failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Create Staff Account</title>

<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background: #102b55;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    background: white;
    width: 100%;
    max-width: 480px;
    padding: 35px;
    border-radius: 15px;
}

h2 {
    color: #102b55;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    text-align: center;
    color: #777;
    margin-bottom: 25px;
}

label {
    display: block;
    margin: 15px 0 7px;
    font-weight: bold;
    color: #333;
}

input, select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 7px;
}

button {
    width: 100%;
    padding: 14px;
    margin-top: 25px;
    background: #d5a529;
    color: #102b55;
    border: none;
    border-radius: 7px;
    font-weight: bold;
    cursor: pointer;
}

.message {
    background: #e4f8e9;
    color: #17652d;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 15px;
}

.error {
    background: #ffe5e5;
    color: #b00020;
    padding: 12px;
    border-radius: 7px;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<div class="container">

    <h2>Create Staff Account</h2>

    <p class="subtitle">
        Register a university supervisor or assessor.
    </p>

    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Authorised Setup Code</label>
        <input type="password" name="setup_code" required>

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Email Address</label>
        <input type="email" name="email" required>

        <label>Phone Number</label>
        <input type="text" name="phone_number">

        <label>Account Type</label>
        <select name="role" required>
            <option value="">Select Account Type</option>
            <option value="Supervisor">University Supervisor</option>
            <option value="Assessor">Assessor</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" minlength="8" required>

        <button type="submit">Create Staff Account</button>

    </form>

</div>

</body>
</html>