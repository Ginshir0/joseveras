<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /pages/adminSignIn.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../include/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $image_filename = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/';
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            $error = 'Invalid image file type.';
        } else {
            $image_filename = uniqid('proj_', true) . '.' . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_filename)) {
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
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, is_featured) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $image_filename, $is_featured]);
            $success = 'Project added successfully.';
            header('Location: /pages/projects.php');
            exit;
        } catch (PDOException $e) {
            error_log("Add project error: " . $e->getMessage());
            $error = 'Error adding project.';
        }
    }
}
?>

<main>
    <div class="content-wrapper">
        <h1>Add Project</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" class="admin-sign-in-form" autocomplete="off" enctype="multipart/form-data">
            <label for="title">Title*</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="5"></textarea>

            <label for="image">Project Image</label>
            <input type="file" name="image" id="image" accept="image/*">

            <label>
                <input type="checkbox" name="is_featured" value="1"> Feature this project
            </label>

            <button type="submit" class="button">Add Project</button>
        </form>
        <p style="margin-top:1rem;"><a href="/pages/projects.php">&larr; Back to Projects</a></p>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
