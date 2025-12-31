<?php
// pages/setup.php
// Initial admin setup page - only accessible when no admin users exist

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

init_session();

// Lock setup page if an admin already exists
if (!is_setup_required($pdo)) {
    header('Location: /pages/adminSignIn.php');
    exit;
}

$errors = [];
$username_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!csrf_verify()) {
        error_log('CSRF verify failed on setup: session_token ' . (isset($_SESSION['csrf_token']) ? 'present' : 'missing') . ', post_token ' . (isset($_POST['csrf_token']) ? 'present' : 'missing') . ', header_token ' . (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? 'present' : 'missing'));
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $ip_address = get_client_ip();
        
        // Preserve username for form repopulation
        $username_value = $username;
        
        // Check for brute-force lockout (use a generic key for setup attempts)
        $lockout = check_login_blocked($pdo, '__setup__', $ip_address);
        
        if ($lockout['blocked']) {
            $errors[] = "Too many failed attempts. Please try again in {$lockout['remaining_minutes']} minute(s).";
        } else {
            // Validate username
            $username_errors = validate_username($username);
            $errors = array_merge($errors, $username_errors);
            
            // Validate password strength
            $password_errors = validate_password_strength($password);
            $errors = array_merge($errors, $password_errors);
            
            // Check password confirmation
            if ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match';
            }
            
            // If validation passed, create the admin
            if (empty($errors)) {
                try {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
                    $stmt->execute([$username, $password_hash]);
                    
                    // Clear any setup attempts
                    clear_login_attempts($pdo, '__setup__', $ip_address);
                    
                    // Auto-login the new admin
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $username;
                    csrf_regenerate();
                    
                    // Set success message and redirect
                    set_flash('success', 'Admin account created successfully. Welcome!');
                    header('Location: /pages/projects.php');
                    exit;
                } catch (PDOException $e) {
                    error_log("Failed to create admin: " . $e->getMessage());
                    $errors[] = 'Failed to create admin account. Please try again.';
                    // Record failed attempt
                    record_login_attempt($pdo, '__setup__', $ip_address);
                }
            } else {
                // Record failed attempt for validation failures
                record_login_attempt($pdo, '__setup__', $ip_address);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Setup - Jose Veras Portfolio</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Initial admin setup for Jose Veras Portfolio. Create your admin account to get started.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#7FDCD6">
    
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>

    <header>
        <nav id="main-nav">
            <ul>
                <li><a href="/index.php">Home</a></li>
            </ul>
        </nav>
    </header>

<main class="admin-sign-in-main">
    <section class="admin-sign-in-container">
        <h2>Initial Setup</h2>
        <p style="text-align: center; margin-bottom: 1.5rem; color: #666;">Create your admin account to get started.</p>
        
        <?php if (!empty($errors)): ?>
            <div class="admin-sign-in-error">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="post" class="admin-sign-in-form" autocomplete="off">
            <?php echo csrf_field(); ?>
            
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus 
                   maxlength="25" 
                   value="<?php echo htmlspecialchars($username_value); ?>"
                   placeholder="Enter Username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required
                   placeholder="Enter Password">

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required
                   placeholder="Re-enter your password">

            <button type="submit" class="button">Create Admin Account</button>
        </form>
        
        <div class="password-requirements" style="margin-top: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 4px; font-size: 0.9rem;">
            <strong>Password Requirements:</strong>
            <ul style="margin: 0.5rem 0 0 0; padding-left: 1.2rem; color: #666; list-style: none;">
                <li data-check="length"><span class="req-icon" aria-hidden="true"></span>At least 12 characters</li>
                <li data-check="uppercase"><span class="req-icon" aria-hidden="true"></span>At least 1 uppercase letter</li>
                <li data-check="lowercase"><span class="req-icon" aria-hidden="true"></span>At least 1 lowercase letter</li>
                <li data-check="number"><span class="req-icon" aria-hidden="true"></span>At least 1 number</li>
                <li data-check="symbol"><span class="req-icon" aria-hidden="true"></span>At least 1 symbol</li>
            </ul>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pw = document.getElementById('password');
            if (!pw) return;
            var reqs = Array.prototype.slice.call(document.querySelectorAll('.password-requirements [data-check]'));

            function checkRule(rule, value) {
                switch (rule) {
                    case 'length': return value.length >= 12;
                    case 'uppercase': return /[A-Z]/.test(value);
                    case 'lowercase': return /[a-z]/.test(value);
                    case 'number': return /[0-9]/.test(value);
                    case 'symbol': return /[^A-Za-z0-9]/.test(value);
                    default: return false;
                }
            }

            function update() {
                var v = pw.value || '';
                reqs.forEach(function (li) {
                    var rule = li.getAttribute('data-check');
                    var ok = checkRule(rule, v);
                    var icon = li.querySelector('.req-icon');
                    if (ok) {
                        li.classList.add('met');
                        if (icon) {
                            // simple checkmark SVG
                            icon.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                        }
                    } else {
                        li.classList.remove('met');
                        if (icon) icon.innerHTML = '';
                    }
                });
            }

            pw.addEventListener('input', update);
            update();
        });
        </script>
    </section>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
