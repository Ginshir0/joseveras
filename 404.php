<?php
// 404.php - Custom 404 error page

// Include auth config for session initialization
require_once __DIR__ . '/config/auth.php';
init_session();

// Set HTTP 404 status code
http_response_code(404);

// SEO: Set page-specific meta tags (noindex to prevent search engines from indexing)
$page_title = '404 - Page Not Found | Jose Veras';
$page_description = 'The page you are looking for could not be found.';
$page_keywords = '404, page not found, error';
$page_robots = 'noindex, nofollow';

// Header - includes the opening HTML, head section, and navigation
include __DIR__ . '/include/header.php';
?>

<main>
    <div class="content-wrapper error-page">
        <h1 class="error-code">404</h1>
        <p class="error-text">Page not found</p>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <div class="error-actions">
            <a href="/" class="button">Home</a>
            <a href="/pages/projects.php" class="button">Projects</a>
        </div>
    </div>
</main>

<?php
// Include the footer
include __DIR__ . '/include/footer.php';
?>
