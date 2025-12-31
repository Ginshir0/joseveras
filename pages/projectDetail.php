<?php
require_once __DIR__ . '/../config/auth.php';
init_session();
require_once __DIR__ . '/../config/db.php';

// Get project ID from query string
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project = null;
$db_error = '';

if ($project_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Project detail - Error fetching project: " . $e->getMessage());
        $db_error = "Could not load project details.";
    }
} else {
    $db_error = "Invalid project ID.";
}

// SEO: Set dynamic page-specific meta tags based on project data
if ($project) {
    $page_title = htmlspecialchars($project['title']) . ' | Jose Veras Portfolio';
    $page_description = !empty($project['description']) 
        ? substr(strip_tags($project['description']), 0, 160) . (strlen($project['description']) > 160 ? '...' : '')
        : 'View details about ' . htmlspecialchars($project['title']) . ' - a project by Jose Veras.';
    $page_keywords = 'Jose Veras, ' . htmlspecialchars($project['title']) . ', DevOps Project, Portfolio';
} else {
    $page_title = 'Project Not Found | Jose Veras Portfolio';
    $page_description = 'The requested project could not be found.';
    $page_keywords = 'Jose Veras, Project, Portfolio';
}

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <?php if ($db_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($db_error); ?></p>
        <?php elseif (!$project): ?>
            <p class="info-message">Project not found.</p>
        <?php else: ?>
            <h1><?php echo htmlspecialchars($project['title']); ?></h1>
            <?php if (!empty($project['image_url'])): ?>
                <img src="/uploads/<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-banner">
            <?php endif; ?>
            <?php if (!empty($project['description'])): ?>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            <?php endif; ?>
            <p><a href="/pages/projects.php">&larr; Back to Projects</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
