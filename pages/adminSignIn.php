<?php
session_start();
$error = '';

// Include DB connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Check credentials against the admins table
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Successful login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: /pages/projects.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
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
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="button">Sign In</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/../include/footer.php'; ?>
