<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Digital Student Attachment Tracking System</title>

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
            background:
                linear-gradient(rgba(9, 25, 55, 0.88),
                rgba(9, 25, 55, 0.92)),
                url('assets/images/campus-background.png');

            background-size: cover;
            background-position: center;

            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .container {
            width: 100%;
            max-width: 1050px;
            text-align: center;
        }

        .logo {
            color: #f5c542;
            font-size: 38px;
            margin-bottom: 15px;
        }

        h1 {
            color: white;
            font-size: 32px;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #dce5f5;
            font-size: 16px;
            margin-bottom: 45px;
        }

        .portal-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .portal-card {
            background: white;
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.25);
            transition: 0.3s;
        }

        .portal-card:hover {
            transform: translateY(-8px);
        }

        .portal-icon {
            width: 85px;
            height: 85px;
            margin: auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: white;
            background: #102b55;
            margin-bottom: 25px;
        }

        .supervisor .portal-icon {
            background: #d5a529;
        }

        .portal-card h2 {
            color: #102b55;
            margin-bottom: 15px;
        }

        .portal-card p {
            color: #666;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .portal-button {
            display: inline-block;
            padding: 14px 30px;
            background: #102b55;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .supervisor .portal-button {
            background: #d5a529;
            color: #102b55;
        }

        .portal-button:hover {
            opacity: 0.85;
        }

        .footer {
            color: #dce5f5;
            margin-top: 40px;
            font-size: 13px;
        }

        @media(max-width: 700px) {
            .portal-container {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="logo">
        <i class="fas fa-graduation-cap"></i>
    </div>

    <h1>Digital Student Attachment Tracking System</h1>

    <p class="subtitle">
        Welcome! Select your portal to access the system.
    </p>

    <div class="portal-container">

        <!-- STUDENT PORTAL -->

        <div class="portal-card">

            <div class="portal-icon">
                <i class="fas fa-user-graduate"></i>
            </div>

            <h2>Student Portal</h2>

            <p>
                Access your attachment registration,
                digital logbook, progress tracking,
                appointments and reports.
            </p>

            <a href="login.php" class="portal-button">
                Student Access
                <i class="fas fa-arrow-right"></i>
            </a>

        </div>


        <!-- SUPERVISOR PORTAL -->

        <div class="portal-card supervisor">

            <div class="portal-icon">
                <i class="fas fa-user-tie"></i>
            </div>

            <h2>University Supervisor Portal</h2>

            <p>
                Access assigned students, review digital
                logbooks, provide feedback and monitor
                attachment progress.
            </p>

            <a href="supervisor_login.php" class="portal-button">
                Supervisor Access
                <i class="fas fa-arrow-right"></i>
            </a>

        </div>

    </div>

    <div class="footer">
        &copy; 2026 Digital Student Attachment Tracking System.
        All Rights Reserved.
    </div>

</div>

</body>
</html>