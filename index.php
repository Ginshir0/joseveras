<?php
// index.php - The main homepage for the portfolio website.

// Include necessary files
// Database configuration - needed to fetch the featured project
require_once __DIR__ . '/config/db.php';
// Header - includes the opening HTML, head section, and navigation
include __DIR__ . '/include/header.php'; // Use __DIR__ for reliable path resolution

// --- Fetch Featured Project ---
$featured_project = null; // Initialize variable
$db_error = '';         // Initialize error message

try {
    // Prepare and execute the query to find one project marked as featured
    // Ordering by updated_at DESC to get the most recently updated featured project if multiple exist
    $stmt = $pdo->prepare("SELECT id, title, description, image_url, project_url, technologies
                           FROM projects
                           WHERE is_featured = TRUE
                           ORDER BY updated_at DESC
                           LIMIT 1");
    $stmt->execute();

    // Fetch the featured project if found
    $featured_project = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log the error securely in a real application
    error_log("Homepage - Error fetching featured project: " . $e->getMessage());
    // Set a user-friendly error message (optional, could also just hide the section)
    $db_error = "Could not load the featured project at this time.";
}

?>

<main>
    <h1>This Is My Journey — One Project at a Time.</h1>
    <p>
        As a System Administrator venturing into the world of DevOps, I'm driven by a passion to understand how things work and a commitment to continuous learning. This space showcases the projects where I turn that curiosity into tangible results, documenting my progress in mastering new skills like Docker, CI/CD, and more, alongside my dedication to fitness pursuits like CrossFit.
    </p>

    <blockquote class="motivation-quote">
        <?php
        // --- Rotating Quotes (Simple Example) ---
        $quotes = [
            ['text' => "The journey of a thousand miles begins with a single step.", 'author' => "Lao Tzu"],
            ['text' => "The only way to do great work is to love what you do.", 'author' => "Steve Jobs"],
            ['text' => "Success is not final, failure is not fatal: It is the courage to continue that counts.", 'author' => "Winston Churchill"],
            ['text' => "Believe you can and you're halfway there.", 'author' => "Theodore Roosevelt"],
            ['text' => "Out of every one-hundred men, ten shouldn’t even be there, eighty are just targets, nine are the real fighters, and we are lucky to have them, for they make the battle. Ah, but the one, one is a warrior and he will bring the others back.", 'author' => "Heraclitus"]
        ];
        // Select a random quote
        $random_quote = $quotes[array_rand($quotes)];
        ?>
        <p>"<?php echo htmlspecialchars($random_quote['text']); ?>"</p>
        <footer>— <?php echo htmlspecialchars($random_quote['author']); ?></footer>
    </blockquote>

    <section id="featured-project">
        <h2>Featured Project</h2>

        <?php if ($db_error): ?>
            <p class="error-message"><?php echo htmlspecialchars($db_error); ?></p>
        <?php elseif ($featured_project): ?>
            <?php // Link the entire featured block to the projects page (or specific detail page) ?>
            <?php // Using project_detail.php?id=... assumes you have this page set up ?>
            <a href="/pages/project_detail.php?id=<?php echo htmlspecialchars($featured_project['id']); ?>" class="featured-project-link">
                <article>
                    <h3><?php echo htmlspecialchars($featured_project['title']); ?></h3>

                    <?php if (!empty($featured_project['image_url'])): ?>
                        <?php // Use root-relative path for image if stored locally ?>
                        <img src="/<?php echo htmlspecialchars($featured_project['image_url']); ?>"
                             alt="Screenshot or logo for <?php echo htmlspecialchars($featured_project['title']); ?>"
                             style="max-width: 100%; height: auto; margin-bottom: 15px; border-radius: 8px;">
                    <?php endif; ?>

                    <?php if (!empty($featured_project['description'])): ?>
                        <p>
                            <?php echo nl2br(htmlspecialchars(substr($featured_project['description'], 0, 200))); // Show first 200 chars ?>
                            <?php if (strlen($featured_project['description']) > 200) echo '...'; ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($featured_project['technologies'])): ?>
                        <p><strong>Technologies:</strong> <?php echo htmlspecialchars($featured_project['technologies']); ?></p>
                    <?php endif; ?>

                     <?php /* Specific links removed as the whole block is clickable */ ?>
                </article>
            </a>
            <?php // Link to see all projects ?>
             <p style="text-align: center; margin-top: 20px;">
                <a href="/pages/projects.php" style="font-weight: bold;">See All Projects &rarr;</a>
            </p>

        <?php else: ?>
            <p class="info-message">No featured project selected yet. Check back soon!</p>
             <?php // Link to see all projects even if none featured ?>
             <p style="text-align: center; margin-top: 20px;">
                <a href="/pages/projects.php" style="font-weight: bold;">See All Projects &rarr;</a>
            </p>
        <?php endif; ?>

    </section> <?php // End #featured-project ?>

</main>

<?php
// Include the footer - includes the closing body and html tags
include __DIR__ . '/include/footer.php';
?>
