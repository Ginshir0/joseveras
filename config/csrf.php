<?php
// config/csrf.php
// Simple CSRF protection helper functions

/**
 * Generate a CSRF token and store it in the session
 * @return string The generated token
 */
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Generate a hidden input field with the CSRF token
 * @return string HTML hidden input element
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Verify the CSRF token from the request
 * @param string|null $token The token to verify (defaults to $_POST['csrf_token'])
 * @return bool True if valid, false otherwise
 */
function csrf_verify(?string $token = null): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate the CSRF token (call after successful form submission)
 */
function csrf_regenerate(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
