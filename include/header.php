<?php
// Initialize session with secure settings before output
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
init_session();

// Check if initial setup is required (no admin users exist)
// Redirect to setup page if needed (but not if already on setup page)

$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'setup.php' && is_setup_required($pdo)) {
    header('Location: /pages/setup.php');
    exit;
}

// SEO Defaults - Pages can override these before including header.php
$site_name = 'Jose Veras';
$default_title = 'Jose Veras - DevOps Portfolio';
$default_description = 'Jose Veras - System Administrator transitioning to DevOps. Explore my portfolio of projects featuring Docker, CI/CD, and cloud infrastructure as I document my learning journey.';
$default_keywords = 'Jose Veras, DevOps, System Administrator, Docker, CI/CD, Linux, Cloud Infrastructure, Portfolio, IT Professional';

// Use page-specific values if set, otherwise use defaults
$page_title = isset($page_title) ? $page_title : $default_title;
$page_description = isset($page_description) ? $page_description : $default_description;
$page_keywords = isset($page_keywords) ? $page_keywords : $default_keywords;
$page_robots = isset($page_robots) ? $page_robots : 'index, follow';

// Build canonical URL
$canonical_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// Remove query string for cleaner canonical (except for detail pages)
if (strpos($current_page, 'Detail') === false) {
    $canonical_url = strtok($canonical_url, '?');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="Jose Veras">
    <meta name="robots" content="<?php echo htmlspecialchars($page_robots); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="theme-color" content="#7FDCD6">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($site_name); ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:site" content="@josever65725881">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "Jose Veras",
        "url": "<?php echo htmlspecialchars($canonical_url); ?>",
        "jobTitle": "System Administrator / DevOps Engineer",
        "description": "<?php echo htmlspecialchars($page_description); ?>",
        "sameAs": [
            "https://github.com/Ginshir0",
            "https://www.linkedin.com/in/jose-veras-b1a089ab/",
            "https://twitter.com/josever65725881"
        ],
        "knowsAbout": ["DevOps", "Docker", "CI/CD", "Linux", "System Administration", "Cloud Infrastructure"]
    }
    </script>

    <link rel="stylesheet" href="/css/style.css">
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
