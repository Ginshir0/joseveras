<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jose Veras - Portfolio</title> <?php // Consider making the title dynamic based on the page ?>

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/featured.css">
    <link rel="stylesheet" href="/css/login.css"> <?php // Added login CSS just in case, remove if not needed globally ?>

    </head>
<body> <?php // The opening body tag is here ?>

    <header>
        <nav id="main-nav">
            <ul>
                <?php // Navigation links using root-relative paths ?>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/pages/projects.php">Projects</a></li>
                <li><a href="https://resume.joseveras.com" target="_blank" rel="noopener noreferrer">Resume</a></li> <?php // External link ?>
                <li><a href="/pages/about.php">About</a></li>
                 <?php // Optional: Add Admin link if needed, maybe conditionally shown
                 /*
                 session_start(); // Ensure session is started if checking login status
                 if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                     echo '<li><a href="/admin/admin_dashboard.php">Admin</a></li>'; // Example admin link
                     echo '<li><a href="/admin/logout.php">Logout</a></li>'; // Example logout link
                 } else {
                     // Maybe show login link if not logged in
                     // echo '<li><a href="/admin/adminLogin.php">Admin Login</a></li>';
                 }
                 */
                 ?>
            </ul>
        </nav>
        <?php // You can add other header content here if needed, like a banner heading ?>
    </header>

    <?php // The main content of each page will start after this include ?>
