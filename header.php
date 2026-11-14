<?php
if (!isset($pageTitle)) { $pageTitle = "Student Attachment System"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Digital Student Attachment Tracking System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">
        <span class="brand-mark">SA</span>
        <span><strong>AttachmentTrack</strong><small>Digital Student Attachment Tracking System</small></span>
    </a>
    <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle navigation">☰</button>
    <nav class="main-nav" id="mainNav">
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="register.php">Register</a>
        <a href="search.php">Search</a>
        <a href="tracking.php">Tracking</a>
        <a href="reports.php">Reports</a>
    </nav>
</header>
<main class="page-shell">
