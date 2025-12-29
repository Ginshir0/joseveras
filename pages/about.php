<?php
// TODO: If dynamic content is added, ensure proper input/output sanitization.
require_once __DIR__ . '/../config/auth.php';
init_session();
include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>About Me</h1>
        <!-- About content will go here -->
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
