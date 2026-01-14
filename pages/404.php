<?php
// 404.php - Page Not Found

// SEO: Set page-specific meta tags (before including header)
$page_title = 'Page Not Found | Jose Veras Portfolio';
$page_description = 'The page you are looking for could not be found.';
$page_keywords = 'Jose Veras, Portfolio, 404, Page Not Found';
$page_robots = 'noindex, nofollow';

// Header - includes the opening HTML, head section, and navigation
include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper" style="text-align: center; padding: 4rem 2rem;">
        <h1 style="font-size: 6rem; margin: 0; color: var(--accent-color);">404</h1>
        <h2 style="margin: 1rem 0;">Page Not Found</h2>
        <p style="font-size: 1.2rem; color: var(--text-secondary); margin: 2rem 0;">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <p>
            <a href="/home" style="display: inline-block; padding: 1rem 2rem; background: var(--accent-color); color: var(--background-color); text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 1rem;">
                &larr; Go Back Home
            </a>
        </p>
    </div>
</main>

<?php
// Include the footer - includes the closing body and html tags
include __DIR__ . '/../include/footer.php';
?>
