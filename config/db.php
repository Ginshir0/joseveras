<?php
// config/db.php
// Establishes the connection to the MySQL database using PDO.
// Reads credentials from Railway environment variables.

// --- Database Credentials ---
// Read credentials from Railway's auto-injected environment variables.
$db_host = getenv('MYSQLHOST');
$db_name = getenv('MYSQLDATABASE');
$db_user = getenv('MYSQLUSER');
$db_pass = getenv('MYSQLPASSWORD');
$db_port = getenv('MYSQLPORT') ?: 3306;

// Validate required credentials
$required_env = ['MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD'];
$missing = array_filter($required_env, function ($key) {
    $value = getenv($key);
    return $value === false || $value === '';
});

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
