<?php
require_once __DIR__ . '/../config/auth.php';
init_session();

// SEO: Set page-specific meta tags
$page_title = 'About | Jose Veras - Portfolio';
$page_description = 'Learn about Jose Veras, a System Administrator with a passion for automation, problem-solving, and continuous learning in IT. Explore technical excellence across AI, Linux, and cloud infrastructure.';
$page_keywords = 'About Jose Veras, System Administrator, IT Professional, Automation, Problem-Solving, Continuous Learning, Technical Background';

include __DIR__ . '/../include/header.php';
?>

<main class="about-page">
    <div class="content-wrapper">
        <h1>About Me</h1>
        
        <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 900px; margin: 2rem auto 0;">
            
            <!-- Technologist Section -->
            <div class="project-card">
                <h3>Technologist</h3>
                <p style="margin-bottom: 1rem;">
                    TBD
                </p>
            </div>

            <!-- Cognitivist Section -->
            <div class="project-card">
                <h3>Cognitivist</h3>
                <p style="margin-bottom: 1rem;">
                    TBD
                </p>
            </div>

            <!-- Crossfitter (Hybrid Athlete) Section -->
            <div class="project-card">
                <h3>Crossfitter (Hybrid Athlete)</h3>
                <p style="margin-bottom: 1rem;">
                    TBD
                </p>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
