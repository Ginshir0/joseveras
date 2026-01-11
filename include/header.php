<?php
// Initialize session with secure settings before output
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
init_session();

// Production HTTPS redirect using X-Forwarded-Proto header (Railway/proxy)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https') {
    $redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect_url, true, 301);
    exit;
}

// Check if initial setup is required (no admin users exist)
// Redirect to setup page if needed (but not if already on setup page)

$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'setup.php' && is_setup_required($pdo)) {
    header('Location: /pages/setup.php');
    exit;
}

// SEO Defaults - Pages can override these before including header.php
$site_name = 'Jose Veras';
$default_title = 'Jose Veras - Portfolio | System Administrator';
$default_description = 'Welcome to Jose Veras\'s portfolio. A System Administrator passionate about Automation, Problem-Solving, and Continuous Learning in IT. Explore projects involving Artificial Intelligence, Linux, Cloud Infrastructure, and more.';
$default_keywords = 'Jose Veras, Portfolio, System Administrator, Artificial Intelligence, Linux, Cloud Infrastructure, IT Projects';

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

// Security Headers - must be sent before any output
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- TODO: Replace favicon.ico with a transparent PNG and update to: -->
    <!-- <link rel="icon" type="image/png" href="/images/favicon.png"> -->
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
        "jobTitle": "System Administrator",
        "description": "<?php echo htmlspecialchars($page_description); ?>",
        "sameAs": [
            "https://github.com/Ginshir0",
            "https://www.linkedin.com/in/jose-veras-b1a089ab/",
            "https://twitter.com/josever65725881"
        ],
        "knowsAbout": ["System Administration", "Artificial Intelligence", "Linux", "Cloud Infrastructure", "Automation", "IT Projects"]
    }
    </script>

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/login.css">
    
    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#article-content',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | link image media table | alignleft aligncenter alignright | bullist numlist | removeformat',
            height: 400,
            menubar: false,
            setup: function(editor) {
                // Add custom styles or initialization if needed
            }
        });
    </script>
</head>
<body>

    <header>
        <nav id="main-nav">
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/pages/projects.php">Projects</a></li>
                <li><a href="/pages/blog.php">Blog</a></li>
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
