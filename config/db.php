<?php
// config/db.php
// Establishes the connection to the MySQL database using PDO.
// Reads credentials from environment variables set by Docker Compose.

// --- Environment Configuration ---
// Read application environment (production or development)
$app_env = getenv('APP_ENV') ?: 'production';
define('APP_ENV', $app_env);

// Set error handling based on environment
if ($app_env === 'development' || $app_env === 'dev') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// --- Database Credentials ---
// Read credentials from environment variables set by Railway.
// Railway uses MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD conventions.
// Fallback values are provided for local development.
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('MYSQL_DATABASE') ?: 'my_website_db';
$db_user = getenv('MYSQL_USER') ?: 'user';
$db_pass = getenv('MYSQL_PASSWORD') ?: 'password';
$db_port = 3306; // Default MySQL port, usually doesn't need to be an env variable unless non-standard

// --- Data Source Name (DSN) ---
// String that specifies the database connection details for PDO.
$dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

// --- PDO Connection Options ---
// Recommended settings for security and error handling.
$options = [
    // Throw exceptions on errors instead of warnings/silent failures.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Fetch results as associative arrays (column name => value) by default.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disable emulation of prepared statements for security and performance with MySQL.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Create PDO Instance ---
// Attempt to connect to the database.
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Connection failed. Log the detailed error for debugging purposes.
    // IMPORTANT: Do NOT display $e->getMessage() directly to users in production!
    error_log("Database Connection Error: " . $e->getMessage());

    // Display a generic, user-friendly error message and stop script execution.
    // You could customize this further, perhaps show a maintenance page.
    die("Sorry, the application could not connect to the database. Please try again later or contact support.");
}

// --- Connection Successful ---
// If the script reaches this point, the connection was successful.
// The $pdo variable is now available for use in any script that includes this file.
// Example usage in other files: require_once 'config/db.php'; $stmt = $pdo->query(...);

?>
