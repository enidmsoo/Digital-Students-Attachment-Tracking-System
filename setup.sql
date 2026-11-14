CREATE DATABASE IF NOT EXISTS student_attachment_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE student_attachment_db;

CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admission_no VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    course VARCHAR(120) NOT NULL,
    company VARCHAR(180) NOT NULL,
    workplace_supervisor VARCHAR(150) DEFAULT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Not Started', 'Ongoing', 'Completed') NOT NULL DEFAULT 'Not Started',
    progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
    remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
