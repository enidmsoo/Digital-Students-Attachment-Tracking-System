<?php
require_once "db_connect.php";

$updated = 0;
$skipped = 0;

$sql = "SELECT id, phone_number
        FROM students
        WHERE password_hash IS NULL
        OR password_hash = ''";

$result = $conn->query($sql);

if (!$result) {
    die("Error finding student records: " . $conn->error);
}

while ($student = $result->fetch_assoc()) {

    $phone = trim($student['phone_number']);

    // Skip students without a registered phone number.
    if ($phone === '') {
        $skipped++;
        continue;
    }

    // Hash the phone number instead of storing it as plain text.
    $hashedPassword = password_hash($phone, PASSWORD_DEFAULT);

    $update = $conn->prepare(
        "UPDATE students SET password_hash = ? WHERE id = ?"
    );

    $update->bind_param("si", $hashedPassword, $student['id']);

    if ($update->execute()) {
        $updated++;
    }

    $update->close();
}

echo "<h2>Initial Password Setup Completed</h2>";
echo "<p>Student accounts updated: " . $updated . "</p>";
echo "<p>Student accounts skipped: " . $skipped . "</p>";
echo "<p>Initial passwords are the students' registered phone numbers.</p>";

$conn->close();
?>