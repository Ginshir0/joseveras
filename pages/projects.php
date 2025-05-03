<?php
session_start();
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../include/header.php';

// Fetch all projects
$projects = [];
$db_error = '';
try {
    $stmt = $pdo->query("SELECT id, title, description, image_url FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Projects page - Error fetching projects: " . $e->getMessage());
    $db_error = "Could not load projects at this time.";
}
?>

<main>
    <div class="content-wrapper">
        <h1>My Projects</h1>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
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
                    <div class="project-card-wrapper">
                        <a href="/pages/projectDetail.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="project-card">
                            <?php if (!empty($project['image_url'])): ?>
                                <img src="/uploads/<?php echo htmlspecialchars($project['image_url']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <?php if (!empty($project['description'])): ?>
                                <p><?php echo nl2br(htmlspecialchars(substr($project['description'], 0, 120))); ?><?php if (strlen($project['description']) > 120) echo '...'; ?></p>
                            <?php endif; ?>
                        </a>
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                            <div class="project-admin-actions">
                                <a href="/pages/editProject.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="button" style="margin-right: 0.5rem;">Edit</a>
                                <form method="post" action="/pages/deleteProject.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($project['id']); ?>">
                                    <button type="submit" class="button" style="background:#b80000; color:white;">Delete</button>
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
