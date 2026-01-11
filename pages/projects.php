<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

init_session();

// SEO: Set page-specific meta tags
$page_title = 'Projects | Jose Veras - Portfolio';
$page_description = 'Browse Jose Veras\'s System Administration projects. Explore hands-on work with Artificial Intelligence, Linux, Cloud Infrastructure, and automation.';
$page_keywords = 'System Administrator, Projects, Artificial Intelligence, Linux, Cloud Infrastructure, IT Projects, Automation, Jose Veras Portfolio';

include __DIR__ . '/../include/header.php';

// Fetch all projects
$projects = [];
$db_error = '';
try {
    if (is_admin()) {
        $stmt = $pdo->query("SELECT id, title, description, image_url, is_draft FROM projects ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT id, title, description, image_url FROM projects WHERE is_draft = 0 ORDER BY created_at DESC");
    }
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Projects page - Error fetching projects: " . $e->getMessage());
    $db_error = "Could not load projects at this time.";
}
?>

<main>
    <div class="content-wrapper">
        <h1>My Projects</h1>
        <?php echo render_flash(); ?>
        <?php if (is_admin()): ?>
            <div style="margin-bottom: 2rem;">
                <a href="/pages/addProject.php" class="button">Add Project</a>
            </div>
        <?php endif; ?>
        <?php if ($db_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($db_error); ?></p>
        <?php elseif (empty($projects)): ?>
            <p class="info-message">No projects found yet.</p>
        <?php else: ?>
            <section class="projects-grid">
                <?php foreach ($projects as $project): ?>
                    <?php
                        $isDraft = !empty($project['is_draft']);
                        $imageFilename = $project['image_url'] ?? '';
                        $displayImage = $isDraft ? '' : ($imageFilename ?: 'Projects Placeholder.png');
                        $isPlaceholder = ($displayImage === 'Projects Placeholder.png');
                    ?>
                    <div class="project-card-wrapper">
                        <?php if (is_admin() && $isDraft): ?>
                            <span class="badge badge-draft">Draft</span>
                        <?php endif; ?>
                        <a href="/pages/projectDetail.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="project-card">
                            <?php if ($displayImage): ?>
                                <img src="<?php echo $isPlaceholder ? '/images/Projects%20Placeholder.png' : '/uploads/' . htmlspecialchars($displayImage); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <?php if (!empty($project['description'])): ?>
                                <p><?php echo nl2br(htmlspecialchars(substr($project['description'], 0, 120))); ?><?php if (strlen($project['description']) > 120) echo '...'; ?></p>
                            <?php endif; ?>
                        </a>
                        <?php if (is_admin()): ?>
                            <div class="project-admin-actions">
                                <a href="/pages/editProject.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="button">Edit</a>
                                <form method="post" action="/pages/deleteProject.php" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($project['id']); ?>">
                                    <button type="submit" class="button button--danger">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
