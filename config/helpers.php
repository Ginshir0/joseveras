<?php
// helpers.php - Utility functions for the portfolio application

/**
 * Generate a URL-friendly slug from a title string
 * 
 * @param string $title The title to slugify
 * @return string The slugified title
 */
function generate_slug($title) {
    // Convert to lowercase
    $slug = strtolower(trim($title));
    
    // Replace spaces and underscores with hyphens
    $slug = preg_replace('/[\s_]+/', '-', $slug);
    
    // Remove all non-alphanumeric characters (except hyphens)
    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
    
    // Remove multiple consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Trim hyphens from start and end
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Get a unique slug by checking the database and appending a number if necessary
 * 
 * @param PDO $pdo Database connection object
 * @param string $base_slug The base slug to check for uniqueness
 * @param int $exclude_id Optional article ID to exclude from uniqueness check (for editing)
 * @return string A unique slug
 */
function get_unique_slug($pdo, $base_slug, $exclude_id = null) {
    $slug = $base_slug;
    $counter = 2;
    
    while (true) {
        try {
            if ($exclude_id) {
                // For editing: allow the same slug if it's the same article
                $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $exclude_id]);
            } else {
                // For creating: check if slug exists at all
                $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ?");
                $stmt->execute([$slug]);
            }
            
            if (!$stmt->fetch()) {
                // Slug doesn't exist, we can use it
                return $slug;
            }
            
            // Slug exists, append counter and try again
            $slug = $base_slug . '-' . $counter;
            $counter++;
            
            // Safety limit to prevent infinite loop
            if ($counter > 100) {
                return $base_slug . '-' . time();
            }
        } catch (PDOException $e) {
            error_log("Error checking slug uniqueness: " . $e->getMessage());
            return $base_slug . '-' . time();
        }
    }
}

/**
 * Get a text excerpt from HTML content
 * Removes HTML tags and returns a plain text excerpt
 * 
 * @param string $content The HTML content to excerpt
 * @param int $length Maximum length of the excerpt
 * @return string Plain text excerpt
 */
function get_excerpt($content, $length = 200) {
    // Remove HTML tags
    $text = strip_tags($content);
    
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    // Truncate to length
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    
    return $text;
}

/**
 * Calculate word count from HTML content
 * 
 * @param string $content The HTML content
 * @return int Word count
 */
function count_words($content) {
    // Remove HTML tags
    $text = strip_tags($content);
    
    // Count words
    $words = str_word_count($text);
    
    return $words;
}

/**
 * Estimate reading time from word count
 * Average reading speed: 200 words per minute
 * 
 * @param int $word_count The number of words
 * @return string Reading time estimate (e.g., "5 min read")
 */
function estimate_reading_time($word_count) {
    $minutes = ceil($word_count / 200);
    
    if ($minutes == 1) {
        return '1 min read';
    }
    
    return $minutes . ' min read';
}

?>
