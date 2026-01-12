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
                    I focus on technology as a means to improve user experience and reduce unnecessary complexity. A key part of my work is identifying opportunities to lower cognitive and operational workload through automation, allowing systems and people to operate more efficiently and reliably. Well-designed technology should feel intuitive, purposeful, and supportive rather than burdensome.
                </p>
                <p style="margin-bottom: 1rem;">
                    I enjoy implementing and creating integrations between systems to streamline workflows and eliminate manual handoffs. Connecting tools in meaningful ways not only improves efficiency but also increases visibility and consistency across environments. Monitoring plays a critical role in this process, providing insight into system health, performance, and potential issues before they impact users.
                </p>
                <p style="margin-bottom: 1rem;">
                    Learning new systems and tools is a constant pursuit. I approach unfamiliar technologies with curiosity and structure, quickly building an understanding of how they fit into the broader ecosystem. This continuous learning mindset enables me to adapt, design resilient solutions, and apply the right tools to the right problems.
                </p>
            </div>

            <!-- Cognitivist Section -->
            <div class="project-card">
                <h3>Cognitivist</h3>
                <p style="margin-bottom: 1rem;">
                    I approach learning with constant curiosity and a drive to understand how things work. Being a cognitivist, to me, means actively engaging with problems, breaking them down, and reasoning through them until clarity emerges. I value the process of learning just as much as the outcome, especially when that process challenges assumptions and forces deeper thinking.
                </p>
                <p style="margin-bottom: 1rem;">
                    Problem solving is where this mindset shows most clearly. I enjoy tackling complex challenges, exploring multiple perspectives, and iterating until a solution makes sense both logically and practically. Each problem is an opportunity to refine how I think, strengthen mental models, and apply structured reasoning to real-world scenarios.
                </p>
                <p style="margin-bottom: 1rem;">
                    Figuring things out is both a discipline and a source of motivation. Whether it's learning a new concept, untangling a system, or refining an idea, I'm driven by the satisfaction that comes from insight and understanding. This way of thinking shapes how I learn, adapt, and continuously improve across everything I work on.
                </p>
            </div>

            <!-- Crossfitter (Hybrid Athlete) Section -->
            <div class="project-card">
                <h3>Crossfitter (Hybrid Athlete)</h3>
                <p style="margin-bottom: 1rem;">
                    CrossFit is a core part of my daily discipline and mindset. I approach it with a competitive edge, not just against others, but against my own limits. Waking up at 5 a.m. to make the 6 a.m. class is a non-negotiable commitment that reinforces consistency, focus, and accountability.
                </p>
                <p style="margin-bottom: 1rem;">
                    What keeps me engaged is the balance between skill work and measurable progress. Every session is an opportunity to refine technique, build strength, and track improvement through clear, objective goals. I value the structured challenge CrossFit provides and the satisfaction that comes from tangible results.
                </p>
                <p style="margin-bottom: 1rem;">
                    Equally important is the community. Training alongside driven, like-minded people creates an environment that pushes everyone to perform better. My favorite movements are the Olympic lifts, where precision, power, and discipline come together; making each rep both a technical challenge and a benchmark of progress.
                </p>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
