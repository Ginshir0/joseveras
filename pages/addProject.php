<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../config/upload.php';

require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $action = $_POST['action'] ?? 'publish';
        $is_draft = $action === 'draft' ? 1 : 0;
        $is_featured = $is_draft ? 0 : (isset($_POST['is_featured']) ? 1 : 0);
        $image_filename = '';

        // Validate input lengths
        if (strlen($title) > 255) {
            $error = 'Title must be 255 characters or less.';
        }
        if (strlen($description) > 65535) {
            $error = 'Description is too long.';
        }

        // Handle image upload with secure validation
        if (isset($_FILES['image'])) {
            $upload_result = handle_image_upload(
                $_FILES['image'],
                __DIR__ . '/../uploads/'
            );
            
            if (!$upload_result['success']) {
                $error = $upload_result['error'];
            } elseif ($upload_result['filename']) {
                $image_filename = $upload_result['filename'];
            }
        }

        if (!$error && $title === '') {
            $error = 'Title is required.';
        } elseif (!$error) {
            if (!$is_draft && !$image_filename) {
                $image_filename = 'Projects Placeholder.png';
            }
            try {
                if ($is_featured) {
                    $pdo->exec("UPDATE projects SET is_featured = 0 WHERE is_featured = 1");
                }
                $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, is_featured, is_draft) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_filename, $is_featured, $is_draft]);
                $flash_message = $is_draft ? 'Project saved as draft.' : 'Project published successfully.';
                set_flash('success', $flash_message);
                header('Location: /pages/projects.php');
                exit;
            } catch (PDOException $e) {
                error_log("Add project error: " . $e->getMessage());
                $error = 'Error adding project.';
            }
        }
    }
}

// SEO: Set page-specific meta tags
$page_title = 'Add Project | Jose Veras - Portfolio';
$page_description = 'Add a new project to Jose Veras\'s System Administrator portfolio.';
$page_keywords = 'Add Project, Portfolio Admin, Jose Veras';

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>Add Project</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" class="admin-sign-in-form" autocomplete="off" enctype="multipart/form-data" id="add-project-form">
            <?php echo csrf_field(); ?>
            <label for="title">Title*</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="5"></textarea>

            <label for="image">Project Image</label>
            <input type="file" name="image" id="image" accept="image/*">
            <small style="display:block; color: var(--text-color); opacity: 0.7; margin-bottom: 0.5rem;">Recommended: 1920×480px or any 4:1 ratio image for best banner display.</small>
            <div id="image-size-error" style="color:#b80000; margin-bottom:0.5rem; display:none;"></div>

            <label>
                <input type="checkbox" name="is_featured" value="1"> Feature this project
            </label>
            <p style="margin:0 0 1rem 0; color: var(--text-color); opacity:0.8;">Drafts stay hidden from the public and cannot be featured until published.</p>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <button type="submit" name="action" value="draft" class="button" style="background:#555; color:white;">Save Draft</button>
                <button type="submit" name="action" value="publish" class="button">Publish</button>
            </div>
        </form>
        <p style="margin-top:1rem;"><a href="/pages/projects.php">&larr; Back to Projects</a></p>
    </div>
</main>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const maxSize = 2 * 1024 * 1024; // 2MB
    const file = this.files[0];
    const errorDiv = document.getElementById('image-size-error');
    if (file && file.size > maxSize) {
        errorDiv.textContent = "Image file size must be 2MB or less.";
        errorDiv.style.display = "block";
        this.value = "";
    } else {
        errorDiv.textContent = "";
        errorDiv.style.display = "none";
    }
});

document.getElementById('add-project-form').addEventListener('submit', function(e) {
    const imageInput = document.getElementById('image');
    const file = imageInput.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file && file.size > maxSize) {
        e.preventDefault();
        document.getElementById('image-size-error').textContent = "Image file size must be 2MB or less.";
        document.getElementById('image-size-error').style.display = "block";
    }
});
</script>

<?php include __DIR__ . '/../include/footer.php'; ?>
