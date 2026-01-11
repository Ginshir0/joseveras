<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Verify CSRF token before processing delete
    if (!csrf_verify()) {
        set_flash('error', 'Invalid request. Please try again.');
        header('Location: /pages/blog.php');
        exit;
    }
    
    $article_id = intval($_POST['id']);
    if ($article_id > 0) {
        try {
            // Delete article from database
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);
            
            set_flash('success', 'Article deleted successfully.');
        } catch (PDOException $e) {
            error_log("Delete article error: " . $e->getMessage());
            set_flash('error', 'Error deleting article.');
        }
    }
}
header('Location: /pages/blog.php');
exit;
