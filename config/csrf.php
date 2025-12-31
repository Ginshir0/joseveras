<?php
// config/csrf.php
// Simple CSRF protection helper functions

require_once __DIR__ . '/auth.php';

/**
 * Generate a CSRF token and store it in the session
 * @return string The generated token
 */
function csrf_token(): string {
    init_session();
    
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
 * @param string|null $token The token to verify (defaults to $_POST['csrf_token'] or header)
 * @return bool True if valid, false otherwise
 */
function csrf_verify(?string $token = null): bool {
    init_session();
    
    // Accept token from POST body or X-CSRF-Token header (useful for AJAX)
    $token = $token ?? ($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        error_log('CSRF verify failed: session_token ' . (isset($_SESSION['csrf_token']) ? 'present' : 'missing') . ', post_token ' . (isset($_POST['csrf_token']) ? 'present' : 'missing') . ', header_token ' . (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? 'present' : 'missing'));
        return false;
    }
    
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    if (!$valid) {
        error_log('CSRF verify mismatch: token present but does not match stored session token');
    }
    return $valid;
}

/**
 * Regenerate the CSRF token (call after successful form submission)
 */
function csrf_regenerate(): void {
    init_session();
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
