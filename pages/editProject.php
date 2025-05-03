<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /pages/adminSignIn.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../include/header.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';
$project = null;

if ($project_id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // Fetch current project for image info
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        $image_filename = $project ? $project['image_url'] : '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/';
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed)) {
                $error = 'Invalid image file type.';
            } else {
                $new_filename = uniqid('proj_', true) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                    // Optionally delete old image
                    if ($image_filename && file_exists($upload_dir . $image_filename)) {
                        @unlink($upload_dir . $image_filename);
                    }
                    $image_filename = $new_filename;
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }

        if ($title === '') {
            $error = 'Title is required.';
        } elseif (!$error) {
            try {
                if ($is_featured) {
                    $pdo->exec("UPDATE projects SET is_featured = 0 WHERE is_featured = 1");
                }
                $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, image_url=?, is_featured=? WHERE id=?");
                $stmt->execute([$title, $description, $image_filename, $is_featured, $project_id]);
                $success = 'Project updated successfully.';
                header('Location: /pages/projects.php');
                exit;
            } catch (PDOException $e) {
                error_log("Edit project error: " . $e->getMessage());
                $error = 'Error updating project.';
            }
        }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$project) {
            $error = 'Project not found.';
        }
    }
} else {
    $error = 'Invalid project ID.';
}
?>

<main>
    <div class="content-wrapper">
        <h1>Edit Project</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($project): ?>
            <form method="post" class="admin-sign-in-form" autocomplete="off" enctype="multipart/form-data">
                <label for="title">Title*</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>

                <label for="description">Description</label>
                <textarea name="description" id="description" rows="5"><?php echo htmlspecialchars($project['description']); ?></textarea>

                <label for="image">Project Image</label>
                <?php if (!empty($project['image_url'])): ?>
                    <div style="margin-bottom:0.5rem;">
                        <img src="/uploads/<?php echo htmlspecialchars($project['image_url']); ?>" alt="Current Image" style="max-width:180px; border-radius:6px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" id="image" accept="image/*">

                <label>
                    <input type="checkbox" name="is_featured" value="1" <?php if ($project['is_featured']) echo 'checked'; ?>> Feature this project
                </label>

                <button type="submit" class="button">Update Project</button>
            </form>
        <?php endif; ?>
        <p style="margin-top:1rem;"><a href="/pages/projects.php">&larr; Back to Projects</a></p>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
