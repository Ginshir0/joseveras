<?php
/**
 * Router Class
 * 
 * Handles URL routing for the Front Controller pattern.
 * Maps clean URLs to page files with parameter extraction and validation.
 */

class Router {
    private $routes = [];
    private $request_uri;
    
    public function __construct($request_uri) {
        $this->request_uri = $request_uri;
        $this->defineRoutes();
    }
    
    /**
     * Define all application routes
     */
    private function defineRoutes() {
        // Public routes
        $this->routes = [
            // Homepage
            '/home' => [
                'file' => 'pages/home.php',
                'params' => [],
                'requires_admin' => false
            ],
            
            // Static pages
            '/about' => [
                'file' => 'pages/about.php',
                'params' => [],
                'requires_admin' => false
            ],
            '/blog' => [
                'file' => 'pages/blog.php',
                'params' => [],
                'requires_admin' => false
            ],
            '/projects' => [
                'file' => 'pages/projects.php',
                'params' => [],
                'requires_admin' => false
            ],
            
            // Authentication
            '/login' => [
                'file' => 'pages/adminSignIn.php',
                'params' => [],
                'requires_admin' => false
            ],
            '/logout' => [
                'file' => 'pages/logout.php',
                'params' => [],
                'requires_admin' => false
            ],
            '/setup' => [
                'file' => 'pages/setup.php',
                'params' => [],
                'requires_admin' => false
            ],
            
            // Admin routes - Project management
            '/addProject' => [
                'file' => 'pages/addProject.php',
                'params' => [],
                'requires_admin' => true
            ],
            '/editProject/{id}' => [
                'file' => 'pages/editProject.php',
                'params' => ['id'],
                'requires_admin' => true
            ],
            '/deleteProject' => [
                'file' => 'pages/deleteProject.php',
                'params' => [],
                'requires_admin' => true
            ],
            
            // Admin routes - Article management
            '/addArticle' => [
                'file' => 'pages/addArticle.php',
                'params' => [],
                'requires_admin' => true
            ],
            '/editArticle/{slug}' => [
                'file' => 'pages/editArticle.php',
                'params' => ['slug'],
                'requires_admin' => true
            ],
            '/deleteArticle' => [
                'file' => 'pages/deleteArticle.php',
                'params' => [],
                'requires_admin' => true
            ],
            
            // Detail pages with parameters
            '/project/{id}' => [
                'file' => 'pages/projectDetail.php',
                'params' => ['id'],
                'requires_admin' => false
            ],
            '/article/{slug}' => [
                'file' => 'pages/articleDetail.php',
                'params' => ['slug'],
                'requires_admin' => false
            ],
        ];
    }
    
    /**
     * Normalize URI: strip query string, trailing slashes, decode
     */
    private function normalizeUri($uri) {
        // Strip query string
        $uri = strtok($uri, '?');
        
        // Strip trailing slashes (except for root)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }
        
        // Decode URL encoding
        $uri = urldecode($uri);
        
        return $uri;
    }
    
    /**
     * Match the request URI to a route
     * 
     * @return array|null Route configuration with extracted params, or null if no match
     */
    public function match() {
        $uri = $this->normalizeUri($this->request_uri);
        
        // Handle root redirect to /home
        if ($uri === '' || $uri === '/') {
            return [
                'redirect' => '/home',
                'code' => 301
            ];
        }
        
        // Try exact match first
        if (isset($this->routes[$uri])) {
            return $this->routes[$uri];
        }
        
        // Try pattern matching for routes with parameters
        foreach ($this->routes as $pattern => $route) {
            // Check if pattern contains parameters
            if (strpos($pattern, '{') === false) {
                continue;
            }
            
            // Convert route pattern to regex
            $regex = $this->patternToRegex($pattern);
            
            if (preg_match($regex, $uri, $matches)) {
                // Extract parameter names from pattern
                preg_match_all('/\{([a-z_]+)\}/', $pattern, $param_names);
                
                // Build params array with validation
                $params = [];
                foreach ($param_names[1] as $index => $param_name) {
                    $value = $matches[$index + 1];
                    
                    // Validate and sanitize parameters
                    if (!$this->validateParam($param_name, $value)) {
                        // Invalid parameter format - return 404
                        return null;
                    }
                    
                    $params[$param_name] = $this->sanitizeParam($param_name, $value);
                }
                
                // Merge extracted params with route config
                return array_merge($route, ['params' => $params]);
            }
        }
        
        // No route matched
        return null;
    }
    
    /**
     * Convert route pattern to regex
     */
    private function patternToRegex($pattern) {
        // Escape forward slashes
        $regex = str_replace('/', '\/', $pattern);
        
        // Replace {id} with capturing group for digits
        $regex = preg_replace('/\{id\}/', '([0-9]+)', $regex);
        
        // Replace {slug} with capturing group for slugs
        $regex = preg_replace('/\{slug\}/', '([a-z0-9-]+)', $regex);
        
        // Replace any other {param} with generic capturing group
        $regex = preg_replace('/\{[a-z_]+\}/', '([^\/]+)', $regex);
        
        return '/^' . $regex . '$/';
    }
    
    /**
     * Validate parameter based on type
     */
    private function validateParam($name, $value) {
        switch ($name) {
            case 'id':
                // Must be positive integer
                return is_numeric($value) && intval($value) > 0;
                
            case 'slug':
                // Must match slug pattern: lowercase letters, numbers, hyphens
                return preg_match('/^[a-z0-9-]+$/', $value) === 1;
                
            default:
                // Default: non-empty string
                return !empty($value);
        }
    }
    
    /**
     * Sanitize parameter based on type
     */
    private function sanitizeParam($name, $value) {
        switch ($name) {
            case 'id':
                return intval($value);
                
            case 'slug':
                return trim($value);
                
            default:
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Get all defined routes (for debugging)
     */
    public function getRoutes() {
        return $this->routes;
    }
}
