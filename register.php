<?php
session_start();
require_once "db_connect.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $admission_no = trim($_POST["admission_no"]);
    $full_name = trim($_POST["full_name"]);
    $course = trim($_POST["course"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);

    if (
        empty($admission_no) ||
        empty($full_name) ||
        empty($course) ||
        empty($email) ||
        empty($phone_number)
    ) {
        $message = "Please fill in all the fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM students
             WHERE admission_no = ? OR email = ?"
        );

        $check->bind_param("ss", $admission_no, $email);
        $check->execute();
        $existing = $check->get_result();

        if ($existing->num_rows > 0) {

            $message = "Admission number or email already registered.";
            $messageType = "error";

        } else {

            // The student's phone number is their initial password.
            $password_hash = password_hash(
                $phone_number,
                PASSWORD_DEFAULT
            );

            $status = "Not Started";
            $progress = 0;

            $insert = $conn->prepare(
                "INSERT INTO students
                (admission_no, full_name, course, email,
                 phone_number, password_hash, status, progress)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $insert->bind_param(
                "sssssssi",
                $admission_no,
                $full_name,
                $course,
                $email,
                $phone_number,
                $password_hash,
                $status,
                $progress
            );

            if ($insert->execute()) {

                $message = "Registration successful! You can now log in using your admission number or email and your phone number as your initial password.";
                $messageType = "success";

            } else {

                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }

            $insert->close();
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

    <title>Student Registration</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f1f3f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 25px;
        }

        .register-container {
            background: white;
            width: 100%;
            max-width: 520px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        h1 {
            color: #10264d;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #26344d;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 18px;
            border: 1px solid #d5dbe5;
            border-radius: 7px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #c99b32;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #10264d;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #c99b32;
        }

        .message {
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .success {
            background: #e4f7eb;
            color: #176b35;
        }

        .error {
            background: #fde8e8;
            color: #a32121;
        }

        .login-link {
            text-align: center;
            margin-top: 22px;
        }

        a {
            color: #b38722;
            text-decoration: none;
            font-weight: bold;
        }

        .note {
            font-size: 12px;
            color: #777;
            margin-top: 15px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="register-container">

    <h1>Student Registration</h1>

    <p class="subtitle">
        Digital Student Attachment Tracking System
    </p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Admission Number</label>
        <input type="text" name="admission_no"
               placeholder="Enter admission number" required>

        <label>Full Name</label>
        <input type="text" name="full_name"
               placeholder="Enter your full name" required>

        <label>Course</label>
        <input type="text" name="course"
               placeholder="Enter your course" required>

        <label>School Email</label>
        <input type="email" name="email"
               placeholder="Enter your email" required>

        <label>Phone Number</label>
        <input type="tel" name="phone_number"
               placeholder="Enter your phone number" required>

        <button type="submit">Create Account</button>

    </form>

    <p class="note">
        Your registered phone number will be your initial password.
        For security, change it after your first login.
    </p>

    <div class="login-link">
        Already registered?
        <a href="login.php">Login here</a>
    </div>

</div>

</body>
</html>