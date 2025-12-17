<?php
// config/auth.php
// Centralized authentication and flash message helpers

/**
 * Initialize session with secure settings
 */
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
    }
}

/**
 * Require admin authentication - redirects to login if not authenticated
 */
function require_admin(): void {
    init_session();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: /pages/adminSignIn.php');
        exit;
    }
}

/**
 * Check if current user is admin (without redirect)
 * @return bool
 */
function is_admin(): bool {
    init_session();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Set a flash message to display on next page load
 * @param string $type 'success' or 'error'
 * @param string $message The message to display
 */
function set_flash(string $type, string $message): void {
    init_session();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null Flash message array with 'type' and 'message', or null
 */
function get_flash(): ?array {
    init_session();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message HTML if one exists
 * @return string HTML for flash message or empty string
 */
function render_flash(): string {
    $flash = get_flash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $message = htmlspecialchars($flash['message']);
        $class = $type === 'success' ? 'success-message' : 'error-message';
        return '<div class="' . $class . '">' . $message . '</div>';
    }
    return '';
}

/**
 * Record a failed login attempt
 * @param PDO $pdo Database connection
 * @param string $username The username that was attempted
 * @param string $ip_address IP address of the attempt
 */
function record_login_attempt(PDO $pdo, string $username, string $ip_address): void {
    try {
        $stmt = $pdo->prepare("INSERT INTO login_attempts (username, ip_address) VALUES (?, ?)");
        $stmt->execute([$username, $ip_address]);
    } catch (PDOException $e) {
        error_log("Failed to record login attempt: " . $e->getMessage());
    }
}

/**
 * Clear login attempts for a user/IP after successful login
 * @param PDO $pdo Database connection
 * @param string $username The username
 * @param string $ip_address IP address
 */
function clear_login_attempts(PDO $pdo, string $username, string $ip_address): void {
    try {
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE username = ? OR ip_address = ?");
        $stmt->execute([$username, $ip_address]);
    } catch (PDOException $e) {
        error_log("Failed to clear login attempts: " . $e->getMessage());
    }
}

/**
 * Check if login is currently blocked due to too many attempts
 * @param PDO $pdo Database connection
 * @param string $username The username being attempted
 * @param string $ip_address IP address of the request
 * @param int $max_attempts Maximum allowed attempts (default 5)
 * @param int $lockout_minutes Lockout duration in minutes (default 15)
 * @return array ['blocked' => bool, 'remaining_minutes' => int]
 */
function check_login_blocked(PDO $pdo, string $username, string $ip_address, int $max_attempts = 5, int $lockout_minutes = 15): array {
    try {
        // Count recent attempts for this username OR IP address
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as attempt_count, MAX(attempted_at) as last_attempt
            FROM login_attempts 
            WHERE (username = ? OR ip_address = ?) 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        $stmt->execute([$username, $ip_address, $lockout_minutes]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['attempt_count'] >= $max_attempts) {
            // Calculate remaining lockout time
            $last_attempt = strtotime($result['last_attempt']);
            $lockout_ends = $last_attempt + ($lockout_minutes * 60);
            $remaining_seconds = $lockout_ends - time();
            $remaining_minutes = max(1, ceil($remaining_seconds / 60));
            
            return [
                'blocked' => true,
                'remaining_minutes' => $remaining_minutes
            ];
        }
    } catch (PDOException $e) {
        error_log("Failed to check login attempts: " . $e->getMessage());
    }
    
    return ['blocked' => false, 'remaining_minutes' => 0];
}

/**
 * Get client IP address
 * @return string
 */
function get_client_ip(): string {
    // Check for proxy headers (be careful with these in production)
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check if initial setup is required (no admin users exist)
 * @param PDO $pdo Database connection
 * @return bool True if setup is required (no admins exist)
 */
function is_setup_required(PDO $pdo): bool {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        $count = $stmt->fetchColumn();
        return $count === 0;
    } catch (PDOException $e) {
        error_log("Failed to check setup status: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate password strength
 * Requirements: minimum 12 characters, at least 1 lowercase, 1 uppercase, 1 number, 1 symbol
 * @param string $password The password to validate
 * @return array Array of error messages (empty if valid)
 */
function validate_password_strength(string $password): array {
    $errors = [];
    
    if (strlen($password) < 12) {
        $errors[] = 'Password must be at least 12 characters long';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least 1 lowercase letter';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least 1 uppercase letter';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least 1 number';
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = 'Password must contain at least 1 symbol';
    }
    
    return $errors;
}

/**
 * Validate username
 * Requirements: 1-25 characters, alphanumeric and underscores only
 * @param string $username The username to validate
 * @return array Array of error messages (empty if valid)
 */
function validate_username(string $username): array {
    $errors = [];
    
    if (strlen($username) === 0) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) > 25) {
        $errors[] = 'Username must be 25 characters or less';
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    return $errors;
}
