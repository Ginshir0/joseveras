<?php
require_once __DIR__ . '/../config/auth.php';
init_session();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$articles = [];
$db_error = '';

try {
    // Fetch published articles in reverse-chronological order (newest first)
    // If admin is logged in, also show drafts
    $is_admin = is_admin();
    
    if ($is_admin) {
        $stmt = $pdo->prepare("SELECT id, title, slug, content, is_draft, created_at, updated_at
                              FROM articles
                              ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->prepare("SELECT id, title, slug, content, is_draft, created_at, updated_at
                              FROM articles
                              WHERE is_draft = FALSE
                              ORDER BY created_at DESC");
    }
    
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Blog - Error fetching articles: " . $e->getMessage());
    $db_error = "Could not load blog articles at this time.";
}

// SEO: Set page-specific meta tags
$page_title = 'Blog | Jose Veras - System Administrator';
$page_description = 'Read articles and insights from Jose Veras about system administration, automation, Linux, cloud infrastructure, and IT projects.';
$page_keywords = 'Blog, Articles, Jose Veras, System Administration, IT, Automation, Linux';

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <h1>Blog</h1>

        <?php
        // Show flash messages
        $flash_message = get_flash('success');
        if ($flash_message) {
            echo '<p class="success-message">' . htmlspecialchars($flash_message) . '</p>';
        }
        ?>

        <?php if (is_admin()): ?>
            <p style="margin-bottom: 2rem;">
                <a href="/pages/addArticle.php" class="button">+ Add Article</a>
            </p>
        <?php endif; ?>

        <?php if ($db_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($db_error); ?></p>
        <?php elseif (empty($articles)): ?>
            <p class="info-message">No articles published yet. Check back soon!</p>
        <?php else: ?>
            <div class="articles-list" style="display: grid; gap: 2rem;">
                <?php foreach ($articles as $article): ?>
                    <article class="article-card" style="padding: 1.5rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-background, #fff);">
                        <?php if ($article['is_draft']): ?>
                            <div class="badge badge-draft" style="margin-bottom: 0.5rem; display: inline-block;">Draft</div>
                        <?php endif; ?>
                        
                        <h2 style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                            <a href="/pages/articleDetail.php?slug=<?php echo urlencode($article['slug']); ?>" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a>
                        </h2>
                        
                        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            <time datetime="<?php echo $article['created_at']; ?>">
                                <?php echo date('F j, Y', strtotime($article['created_at'])); ?>
                            </time>
                            — <?php echo estimate_reading_time(count_words($article['content'])); ?>
                        </p>

                        <p style="margin-bottom: 1rem;">
                            <?php echo htmlspecialchars(get_excerpt($article['content'], 250)); ?>
                        </p>

                        <p>
                            <a href="/pages/articleDetail.php?slug=<?php echo urlencode($article['slug']); ?>" style="font-weight: bold;">Read More →</a>
                        </p>

                        <?php if (is_admin()): ?>
                            <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                                <a href="/pages/editArticle.php?slug=<?php echo urlencode($article['slug']); ?>" class="button" style="background: #555; color: white; font-size: 0.9rem; padding: 0.5rem 1rem;">Edit</a>
                                <form method="post" action="/pages/deleteArticle.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                                    <button type="submit" class="button" style="background: #b80000; color: white; font-size: 0.9rem; padding: 0.5rem 1rem;">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
