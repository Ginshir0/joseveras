-- initDB/init.sql
-- This script is executed when the MySQL container ('db' service) starts for the first time.
-- It initializes the database structure within the database automatically created
-- by Docker based on the MYSQL_DATABASE environment variable (e.g., 'my_website_db').

-- --- Create Projects Table ---
-- Stores information about your portfolio projects.
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for the project',
    `title` VARCHAR(255) NOT NULL COMMENT 'Title of the project',
    `description` TEXT NULL COMMENT 'Detailed description of the project',
    `image_url` VARCHAR(255) NULL COMMENT 'URL or relative path to the project image',
    `project_url` VARCHAR(255) NULL COMMENT 'URL to the live project or its code repository',
    `is_featured` BOOLEAN DEFAULT FALSE COMMENT 'Set to TRUE to feature this project on the homepage',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the project was added',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp when the project was last updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to store portfolio project details';

-- --- Create Admins Table ---
-- Stores credentials for users who can manage website content (e.g., add projects).
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for the admin user',
    `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Admin username (must be unique)',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Securely hashed password generated using PHP password_hash()',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the admin user was created'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to store admin user credentials';

-- --- Insert Default Admin User (Optional) ---
-- Inserts a default admin user.
-- !! IMPORTANT !!
-- You MUST replace '$2y$10$PLACEHOLDER_HASH_MUST_BE_REPLACED' below with a real,
-- secure password hash generated using PHP's password_hash() function.
-- Use a script (like your previous temp.php or a dedicated tool) to generate this hash
-- for the password you want to use.
-- The 'IGNORE' keyword prevents an error if an admin with this username already exists.
INSERT IGNORE INTO `admins` (`username`, `password_hash`) VALUES (
    'admin', -- Choose your desired default admin username (e.g., 'jveras', 'admin')
    '$2y$10$PLACEHOLDER_HASH_MUST_BE_REPLACED' -- <<< REPLACE THIS WITH YOUR ACTUAL GENERATED HASH >>>
);

-- --- Add Sample Project Data (Optional) ---
-- You can uncomment and modify the section below to add some initial project data
-- when the database is first created. This is useful for testing.
/*
INSERT INTO `projects` (`title`, `description`, `image_url`, `project_url`, `technologies`, `is_featured`) VALUES
(
    'My Portfolio Website',
    'The very website you are looking at! Built using PHP, MySQL, and Docker to showcase my projects and learn DevOps practices.',
    'images/portfolio_screenshot.png', -- Example path: Make sure the image exists in your app's images folder
    'https://github.com/Ginshir0/your-portfolio-repo', -- Replace with your actual repo URL
    'PHP, MySQL, Docker, Apache, HTML, CSS, JavaScript',
    TRUE -- Feature this project
),
(
    'DevOps Learning Lab',
    'A collection of scripts and configurations used for practicing CI/CD pipelines, containerization, and infrastructure as code.',
    NULL, -- No image for this one yet
    'https://github.com/Ginshir0/devops-lab-repo', -- Replace with your actual repo URL
    'Docker, Jenkins, Ansible, Bash, Git',
    FALSE
);
*/

-- End of initialization script
