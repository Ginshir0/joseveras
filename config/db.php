<?php
// config/db.php
// Establishes the connection to the MySQL database using PDO.
// Reads credentials from environment variables set by Docker Compose or Railway.

// --- Helper Function ---
// Gets the first non-empty value from a list of environment variable names.
function getEnvWithFallback(array $keys, $default = null) {
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
    }
    return $default;
}

// --- Database Credentials ---
// Read credentials from environment variables.
// Supports both standard DB_* variables (Docker Compose) and Railway's MYSQL* variables.
$db_host = getEnvWithFallback(['DB_HOST', 'MYSQLHOST', 'MYSQL_HOST']);
$db_name = getEnvWithFallback(['DB_DATABASE', 'MYSQLDATABASE', 'MYSQL_DATABASE']);
$db_user = getEnvWithFallback(['DB_USER', 'MYSQLUSER', 'MYSQL_USER']);
$db_pass = getEnvWithFallback(['DB_PASSWORD', 'MYSQLPASSWORD', 'MYSQL_PASSWORD']);
$db_port = getEnvWithFallback(['DB_PORT', 'MYSQLPORT', 'MYSQL_PORT'], 3306);

// Validate required credentials
$missing = [];
if (empty($db_host)) $missing[] = 'DB_HOST/MYSQLHOST';
if (empty($db_name)) $missing[] = 'DB_DATABASE/MYSQLDATABASE';
if (empty($db_user)) $missing[] = 'DB_USER/MYSQLUSER';
if ($db_pass === null) $missing[] = 'DB_PASSWORD/MYSQLPASSWORD';

if (!empty($missing)) {
    error_log('Database configuration missing environment variables: ' . implode(', ', $missing));
    http_response_code(500);
    die('Application is not configured to connect to the database.');
}

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
