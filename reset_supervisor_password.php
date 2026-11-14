<?php
require_once "db_connect.php";

$email = "johnkamau@example.com";
$new_password = "12345678";

$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE supervisors SET password_hash = ? WHERE email = ?"
);

$stmt->bind_param("ss", $password_hash, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "SUCCESS! Supervisor password has been updated.";
    echo "<br>Email: " . htmlspecialchars($email);
    echo "<br>Initial password: 12345678";
} else {
    echo "No account was updated. Check whether this email exists in the supervisors table.";
}

$stmt->close();
$conn->close();
?>