<?php
session_start();
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../include/header.php';

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
                <img src="/uploads/<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" style="max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 1.5rem;">
            <?php endif; ?>
            <?php if (!empty($project['description'])): ?>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            <?php endif; ?>
            <p><a href="/pages/projects.php">&larr; Back to Projects</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
