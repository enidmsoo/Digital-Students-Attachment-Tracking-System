<?php

$host = "localhost";
$username = "YOUR_DATABASE_USERNAME";
$password = "YOUR_DATABASE_PASSWORD";
$database = "digital_student_attachment_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed. Please check your configuration.");
}

$conn->set_charset("utf8mb4");

?>