<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /pages/adminSignIn.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $project_id = intval($_POST['id']);
    if ($project_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
        } catch (PDOException $e) {
            error_log("Delete project error: " . $e->getMessage());
            // Optionally, set an error message in session and show on projects.php
        }
    }
}
header('Location: /pages/projects.php');
exit;
