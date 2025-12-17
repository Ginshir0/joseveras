<?php
// Start session at the very beginning, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if initial setup is required (no admin users exist)
// Redirect to setup page if needed (but not if already on setup page)
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'setup.php' && is_setup_required($pdo)) {
    header('Location: /pages/setup.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jose Veras - Portfolio</title>

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/featured.css">
    <link rel="stylesheet" href="/css/login.css">

    </head>
<body>

    <header>
        <nav id="main-nav">
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/pages/projects.php">Projects</a></li>
                <li><a href="https://resume.joseveras.com" target="_blank" rel="noopener noreferrer">Resume</a></li>
                <li><a href="/pages/about.php">About</a></li>
                <?php
                // Show logout link if admin is logged in
                if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                    echo '<li><a href="/pages/logout.php">Logout</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <?php // The main content of each page will start after this include ?>
