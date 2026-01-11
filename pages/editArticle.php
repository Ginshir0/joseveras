<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../config/helpers.php';

require_admin();

$article_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$error = '';
$article = null;

if ($article_slug) {
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
                    // Fetch current article for ID
                    $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ?");
                    $stmt->execute([$article_slug]);
                    $current = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($current) {
                        $article_id = $current['id'];

                        // Generate new slug if title changed
                        $base_slug = generate_slug($title);
                        $slug = get_unique_slug($pdo, $base_slug, $article_id);

                        // Update article
                        $stmt = $pdo->prepare("UPDATE articles SET title=?, slug=?, content=?, is_draft=? WHERE id=?");
                        $stmt->execute([$title, $slug, $content, $is_draft, $article_id]);

                        $flash_message = $is_draft ? 'Article saved as draft.' : 'Article updated and published.';
                        set_flash('success', $flash_message);
                        header('Location: /pages/articleDetail.php?slug=' . urlencode($slug));
                        exit;
                    } else {
                        $error = 'Article not found.';
                    }
                } catch (PDOException $e) {
                    error_log("Edit article error: " . $e->getMessage());
                    $error = 'Error updating article.';
                }
            }
        }
    } else {
        // Fetch article for editing
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ?");
        $stmt->execute([$article_slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$article) {
            $error = 'Article not found.';
        }
    }
} else {
    $error = 'Invalid article slug.';
}

// SEO: Set page-specific meta tags
$page_title = 'Edit Article | Jose Veras - Blog';
$page_description = 'Edit an existing blog article on Jose Veras\'s System Administrator blog.';
$page_keywords = 'Edit Article, Blog Admin, Jose Veras';
$page_robots = 'noindex, follow';

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>Edit Article</h1>
        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($article): ?>
            <form method="post" class="admin-sign-in-form" id="edit-article-form">
                <?php echo csrf_field(); ?>
                <p style="margin-bottom:1rem; font-weight:600; color: var(--text-color);">
                    Status: <?php echo $article['is_draft'] ? 'Draft (admin-only)' : 'Published'; ?>
                </p>
                <?php if ($article['is_draft']): ?>
                    <div class="badge badge-draft" style="margin-bottom:1rem; display:inline-block;">Draft</div>
                <?php endif; ?>

                <label for="title">Title*</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>

                <label for="article-content">Article Content*</label>
                <textarea name="content" id="article-content" required><?php echo htmlspecialchars($article['content']); ?></textarea>

                <label>
                    <input type="checkbox" name="is_draft" value="1" <?php if ($article['is_draft']) echo 'checked'; ?>> Save as draft
                </label>
                <p style="margin:0 0 1rem 0; color: var(--text-color); opacity:0.8;">Drafts are hidden from the public; publish to make visible.</p>

                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <button type="submit" name="action" value="draft" class="button" style="background:#555; color:white;">Save Draft</button>
                    <button type="submit" name="action" value="publish" class="button">Publish</button>
                </div>
            </form>
        <?php endif; ?>
        <p style="margin-top:1rem;"><a href="/pages/blog.php">&larr; Back to Blog</a></p>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
