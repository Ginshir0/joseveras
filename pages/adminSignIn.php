<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/csrf.php';

init_session();

// Redirect if already logged in
if (is_admin()) {
    header('Location: /pages/projects.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!csrf_verify()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip_address = get_client_ip();
        
        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
        } else {
            // Check for brute-force lockout
            $lockout = check_login_blocked($pdo, $username, $ip_address);
            
            if ($lockout['blocked']) {
                $error = "Too many failed attempts. Please try again in {$lockout['remaining_minutes']} minute(s).";
            } else {
                // Check credentials against the admins table
                $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Successful login - clear attempts and regenerate session
                    clear_login_attempts($pdo, $username, $ip_address);
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    csrf_regenerate();
                    header('Location: /pages/projects.php');
                    exit;
                } else {
                    // Record failed attempt
                    record_login_attempt($pdo, $username, $ip_address);
                    $error = 'Invalid credentials.';
                }
            }
        }
    }
}

include __DIR__ . '/../include/header.php';
?>

<main class="admin-sign-in-main">
    <section class="admin-sign-in-container">
        <h2>Admin Sign In</h2>
        <?php if ($error): ?>
            <div class="admin-sign-in-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" class="admin-sign-in-form" autocomplete="off">
            <?php echo csrf_field(); ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="button">Sign In</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
