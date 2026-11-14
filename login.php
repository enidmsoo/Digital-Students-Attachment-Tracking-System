<?php
session_start();
require_once "db_connect.php";

if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT id, admission_no, full_name, email, password_hash
         FROM students
         WHERE admission_no = ? OR email = ?"
    );

    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $student = $result->fetch_assoc();

        if (password_verify($password, $student['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['admission_no'] = $student['admission_no'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Incorrect password. Please try again.";
        }

    } else {
        $error = "Account not found. Please register first.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Login | KCA University</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    min-height: 100vh;
    background: #eef2f8;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 25px;
}

/* MAIN CONTAINER */

.login-container {
    width: 100%;
    max-width: 1150px;
    min-height: 650px;
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(16, 35, 70, 0.16);
}

/* LEFT IMAGE SECTION */

.photo-section {
    position: relative;
    background:
        linear-gradient(
            135deg,
            rgba(10, 28, 60, 0.88),
            rgba(21, 52, 96, 0.58)
        ),
        url('assets/images/campus-background.png');

    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
    padding: 55px;
    color: white;
}

.photo-content {
    position: relative;
    z-index: 1;
}

.university-label {
    display: inline-block;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255,255,255,0.3);
    padding: 9px 16px;
    border-radius: 30px;
    font-size: 12px;
    letter-spacing: 2px;
    margin-bottom: 25px;
}

.photo-content h1 {
    font-size: 38px;
    font-weight: 800;
    letter-spacing: 2px;
    margin-bottom: 15px;
}

.photo-content h2 {
    color: #f5c75e;
    font-size: 25px;
    line-height: 1.4;
    margin-bottom: 17px;
}

.photo-content p {
    font-size: 15px;
    line-height: 1.8;
    color: #e4eaf4;
    max-width: 400px;
}

/* RIGHT FORM SECTION */

.form-section {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 45px;
    background: #ffffff;
}

.login-card {
    width: 100%;
    max-width: 390px;
}

.logo-circle {
    width: 65px;
    height: 65px;
    background: #eaf0fa;
    color: #183866;
    border-radius: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 27px;
    font-weight: 800;
    margin-bottom: 25px;
    border: 2px solid #f2ca68;
}

.login-card h2 {
    font-size: 30px;
    color: #172d50;
    margin-bottom: 10px;
}

.subtitle {
    color: #8993a5;
    font-size: 14px;
    margin-bottom: 32px;
    line-height: 1.6;
}

/* ERROR MESSAGE */

.error-message {
    background: #fff0f0;
    color: #bd3030;
    padding: 13px;
    border-radius: 9px;
    font-size: 13px;
    margin-bottom: 20px;
    border-left: 4px solid #dc5555;
}

/* INPUT FIELDS */

.form-group {
    margin-bottom: 21px;
}

.form-group label {
    display: block;
    color: #344563;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 9px;
}

.input-box {
    position: relative;
}

.input-box input {
    width: 100%;
    padding: 15px 16px;
    border: 1px solid #dce3ed;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    background: #f9fbfe;
    transition: 0.3s;
}

.input-box input:focus {
    border-color: #244b83;
    background: white;
    box-shadow: 0 0 0 4px rgba(36, 75, 131, 0.09);
}

.password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #244b83;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
}

/* LOGIN BUTTON */

.login-button {
    width: 100%;
    padding: 16px;
    margin-top: 8px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #173660, #315d96);
    color: white;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(23, 54, 96, 0.2);
    transition: 0.3s;
}

.login-button:hover {
    background: linear-gradient(135deg, #d9a936, #f1c75d);
    color: #172d50;
    transform: translateY(-2px);
}

/* REGISTRATION LINK */

.register-text {
    text-align: center;
    margin-top: 28px;
    color: #8490a3;
    font-size: 14px;
}

.register-text a {
    color: #244b83;
    text-decoration: none;
    font-weight: 800;
}

.register-text a:hover {
    color: #d6a52e;
}

/* FOOTER */

.login-footer {
    text-align: center;
    margin-top: 35px;
    font-size: 11px;
    color: #a0a9b8;
    line-height: 1.6;
}

/* RESPONSIVE DESIGN */

@media(max-width: 850px) {

    .login-container {
        grid-template-columns: 1fr;
    }

    .photo-section {
        min-height: 300px;
        padding: 35px;
    }

    .photo-content h1 {
        font-size: 29px;
    }

    .photo-content h2 {
        font-size: 21px;
    }

    .form-section {
        padding: 40px 25px;
    }
}

@media(max-width: 480px) {

    body {
        padding: 12px;
    }

    .photo-section {
        min-height: 250px;
        padding: 25px;
    }

    .photo-content h1 {
        font-size: 24px;
    }

    .login-card h2 {
        font-size: 25px;
    }

}

</style>

</head>

<body>

<div class="login-container">

    <!-- LEFT IMAGE SECTION -->

    <div class="photo-section">

        <div class="photo-content">

            <span class="university-label">
                KCA UNIVERSITY
            </span>

            <h1>WELCOME TO KCA</h1>

            <h2>Digital Student Attachment Tracking System</h2>

            <p>
                Your academic and professional journey starts here.
                Access your attachment records, submit daily logbooks,
                monitor your progress and stay connected with your
                university supervisor.
            </p>

        </div>

    </div>


    <!-- RIGHT LOGIN SECTION -->

    <div class="form-section">

        <div class="login-card">

            <div class="logo-circle">🎓</div>

            <h2>Welcome Back!</h2>

            <p class="subtitle">
                Sign in to your student account to continue.
            </p>

            <?php if (!empty($error)): ?>

                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <div class="form-group">

                    <label>Admission Number or School Email</label>

                    <div class="input-box">

                        <input
                            type="text"
                            name="login"
                            placeholder="Enter your admission number or email"
                            value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>Password</label>

                    <div class="input-box">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            SHOW
                        </button>

                    </div>

                </div>


                <button type="submit" class="login-button">
                    SIGN IN TO STUDENT PORTAL
                </button>

            </form>


            <p class="register-text">
                Don't have an account?
                <a href="register.php">Create Account</a>
            </p>


            <div class="login-footer">
                KCA UNIVERSITY<br>
                Digital Student Attachment Tracking System
            </div>

        </div>

    </div>

</div>


<script>

function togglePassword() {

    const password = document.getElementById("password");
    const button = document.querySelector(".password-toggle");

    if (password.type === "password") {

        password.type = "text";
        button.textContent = "HIDE";

    } else {

        password.type = "password";
        button.textContent = "SHOW";

    }

}

</script>

</body>
</html>