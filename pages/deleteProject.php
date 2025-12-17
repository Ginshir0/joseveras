<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Verify CSRF token before processing delete
    if (!csrf_verify()) {
        set_flash('error', 'Invalid request. Please try again.');
        header('Location: /pages/projects.php');
        exit;
    }
    
    $project_id = intval($_POST['id']);
    if ($project_id > 0) {
        try {
            // Get project image to delete
            $stmt = $pdo->prepare("SELECT image_url FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete project from database
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            
            // Delete associated image file
            if ($project && !empty($project['image_url'])) {
                $image_path = __DIR__ . '/../uploads/' . $project['image_url'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            
            set_flash('success', 'Project deleted successfully.');
        } catch (PDOException $e) {
            error_log("Delete project error: " . $e->getMessage());
            set_flash('error', 'Error deleting project.');
        }
    }
}
header('Location: /pages/projects.php');
exit;
