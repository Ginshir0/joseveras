<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../config/helpers.php';

require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $action = $_POST['action'] ?? 'publish';
        $is_draft = $action === 'draft' ? 1 : 0;

        // Validate input
        if (strlen($title) > 255) {
            $error = 'Title must be 255 characters or less.';
        }
        if (strlen($content) > 16777215) { // LONGTEXT max size
            $error = 'Article content is too long.';
        }

        if (!$error && $title === '') {
            $error = 'Title is required.';
        } elseif (!$error && $content === '') {
            $error = 'Article content is required.';
        } elseif (!$error) {
            try {
                // Generate slug from title
                $base_slug = generate_slug($title);
                $slug = get_unique_slug($pdo, $base_slug);

                // Insert article into database
                $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, is_draft) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $is_draft]);

                $flash_message = $is_draft ? 'Article saved as draft.' : 'Article published successfully.';
                set_flash('success', $flash_message);
                header('Location: /pages/blog.php');
                exit;
            } catch (PDOException $e) {
                error_log("Add article error: " . $e->getMessage());
                $error = 'Error adding article.';
            }
        }
    }
}

// SEO: Set page-specific meta tags
$page_title = 'Add Article | Jose Veras - Blog';
$page_description = 'Create a new blog article for Jose Veras\'s System Administrator blog.';
$page_keywords = 'Add Article, Blog Admin, Jose Veras';
$page_robots = 'noindex, follow';

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>Add Article</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" class="admin-sign-in-form" id="add-article-form">
            <?php echo csrf_field(); ?>
            <label for="title">Title*</label>
            <input type="text" name="title" id="title" required>

            <label for="article-content">Article Content*</label>
            <textarea name="content" id="article-content" required></textarea>

            <label>
                <input type="checkbox" name="is_draft" value="1"> Save as draft
            </label>
            <p style="margin:0 0 1rem 0; color: var(--text-color); opacity:0.8;">Drafts stay hidden from the public until published.</p>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <button type="submit" name="action" value="draft" class="button" style="background:#555; color:white;">Save Draft</button>
                <button type="submit" name="action" value="publish" class="button">Publish</button>
            </div>
        </form>
        <p style="margin-top:1rem;"><a href="/pages/blog.php">&larr; Back to Blog</a></p>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
