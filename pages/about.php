<?php
require_once __DIR__ . '/../config/auth.php';
init_session();

// SEO: Set page-specific meta tags
$page_title = 'About | Jose Veras - DevOps Portfolio';
$page_description = 'Learn about Jose Veras, a dedicated IT professional transitioning from System Administration to DevOps, with a passion for continuous learning and technical excellence.';
$page_keywords = 'About Jose Veras, DevOps Engineer, System Administrator, Career Transition, IT Professional, Technical Background';

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>About Me</h1>
        <!-- About content will go here -->
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
