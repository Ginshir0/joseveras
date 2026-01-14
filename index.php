<?php
/**
 * index.php - Front Controller
 * 
 * Entry point for all requests. Routes clean URLs to appropriate page files.
 * Centralizes configuration, session management, and authentication.
 */

// Initialize all configurations and session
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/csrf.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/router.php';

// Initialize session with secure settings
init_session();

// Check if initial setup is required (no admin users exist)
// Redirect to setup page if needed (but not if already on setup page)
$request_uri = $_SERVER['REQUEST_URI'];
$normalized_uri = strtok($request_uri, '?');
$normalized_uri = rtrim($normalized_uri, '/');

if ($normalized_uri !== '/setup' && is_setup_required($pdo)) {
    header('Location: /setup');
    exit;
}

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Initialize the router
$router = new Router($request_uri);

// Match the route
$route = $router->match();

// Handle no match (404)
if ($route === null) {
    http_response_code(404);
    $route = [
        'file' => 'pages/404.php',
        'params' => [],
        'requires_admin' => false
    ];
}

// Handle redirects (e.g., root to /home)
if (isset($route['redirect'])) {
    http_response_code($route['code'] ?? 302);
    header('Location: ' . $route['redirect']);
    exit;
}

// Authentication middleware - check if route requires admin access
if ($route['requires_admin']) {
    require_admin(); // Will redirect to login or show 403 if not authorized
}

// Extract route parameters as variables for page access
// This allows pages to use $route['id'], $route['slug'], etc.
$route_params = $route['params'] ?? [];

// For backward compatibility, also make individual variables available
foreach ($route_params as $key => $value) {
    $$key = $value;
}

// Include the target page file
$page_file = __DIR__ . '/' . $route['file'];

if (file_exists($page_file)) {
    include $page_file;
} else {
    // Page file not found - show 404
    error_log("Router: Page file not found: " . $page_file);
    http_response_code(404);
    include __DIR__ . '/pages/404.php';
}
