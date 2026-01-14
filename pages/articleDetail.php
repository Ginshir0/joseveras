<?php
// Get article slug from route parameter
$article_slug = isset($route['slug']) ? trim($route['slug']) : '';
$article = null;
$db_error = '';

if ($article_slug) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ?");
        $stmt->execute([$article_slug]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Article detail - Error fetching article: " . $e->getMessage());
        $db_error = "Could not load article details.";
    }
} else {
    $db_error = "Invalid article slug.";
}

// Handle draft visibility and SEO
if ($article && !empty($article['is_draft']) && !is_admin()) {
    http_response_code(404);
    $article = null;
    $page_robots = 'noindex, nofollow';
}

// SEO: Set dynamic page-specific meta tags based on article data
if ($article) {
    $page_title = htmlspecialchars($article['title']) . ' | Jose Veras Blog';
    $page_description = get_excerpt($article['content'], 160);
    $page_keywords = 'Jose Veras, Blog, System Administration, IT, ' . htmlspecialchars($article['title']);
    if (!empty($article['is_draft'])) {
        $page_robots = 'noindex, nofollow';
    }
} else {
    http_response_code(404);
    $page_title = 'Article Not Found | Jose Veras Blog';
    $page_description = 'The requested article could not be found.';
    $page_keywords = 'Jose Veras, Article, Blog';
    $page_robots = 'noindex, nofollow';
}

include __DIR__ . '/../include/header.php';
?>

<main>
    <div class="content-wrapper">
        <?php if ($db_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($db_error); ?></p>
        <?php elseif (!$article): ?>
            <p class="info-message">Article not found.</p>
        <?php else: ?>
            <?php if (!empty($article['is_draft'])): ?>
                <div class="badge badge-draft" style="margin-bottom:1rem; display:inline-block;">Draft (visible to admins only)</div>
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($article['title']); ?></h1>

            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 2rem;">
                <time datetime="<?php echo $article['created_at']; ?>">
                    Published on <?php echo date('F j, Y', strtotime($article['created_at'])); ?>
                </time>
                <?php if ($article['updated_at'] !== $article['created_at']): ?>
                    · <time datetime="<?php echo $article['updated_at']; ?>">
                        Updated <?php echo date('F j, Y', strtotime($article['updated_at'])); ?>
                    </time>
                <?php endif; ?>
                — <?php echo estimate_reading_time(count_words($article['content'])); ?>
                — <?php echo count_words($article['content']); ?> words
            </p>

            <article class="article-content" style="line-height: 1.7; color: var(--text-color);">
                <?php echo $article['content']; ?>
            </article>

            <?php if (is_admin()): ?>
                <div class="project-admin-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <a href="/editArticle/<?php echo urlencode($article['slug']); ?>" class="button">Edit</a>
                    <form method="post" action="/deleteArticle" onsubmit="return confirm('Are you sure you want to delete this article?');">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                        <button type="submit" class="button button--danger">Delete</button>
                    </form>
                </div>
            <?php endif; ?>

            <p style="margin-top: 2rem;"><a href="/blog">&larr; Back to Blog</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
