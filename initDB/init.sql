-- initDB/init.sql
-- This script is executed when the MySQL container ('db' service) starts for the first time.
-- It initializes the database structure within the database automatically created
-- by Docker based on the MYSQL_DATABASE environment variable.

-- --- Create Projects Table ---
-- Stores information about your portfolio projects.
CREATE TABLE IF NOT EXISTS `projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for the project',
    `title` VARCHAR(255) NOT NULL COMMENT 'Title of the project',
    `description` TEXT NULL COMMENT 'Detailed description of the project',
    `image_url` VARCHAR(255) NULL COMMENT 'URL or relative path to the project image',
    `is_draft` BOOLEAN DEFAULT FALSE COMMENT 'When TRUE, project is hidden from public views until published',
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

-- --- Create Login Attempts Table ---
-- Tracks failed login attempts for brute-force protection
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for the attempt',
    `username` VARCHAR(50) NOT NULL COMMENT 'Username that was attempted',
    `ip_address` VARCHAR(45) NOT NULL COMMENT 'IP address of the attempt (supports IPv6)',
    `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When the attempt occurred',
    INDEX `idx_username_time` (`username`, `attempted_at`),
    INDEX `idx_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to track failed login attempts';

-- --- Create Articles Table ---
-- Stores blog articles with rich text content and slug-based URLs
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for the article',
    `title` VARCHAR(255) NOT NULL COMMENT 'Title of the article',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'URL-friendly slug for the article (auto-generated from title)',
    `content` LONGTEXT NOT NULL COMMENT 'Rich HTML content of the article',
    `is_draft` BOOLEAN DEFAULT FALSE COMMENT 'When TRUE, article is hidden from public views until published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the article was created',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp when the article was last updated'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table to store blog articles';

-- End of initialization script
