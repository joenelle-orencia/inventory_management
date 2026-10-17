<?php
require_once 'config.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id,name,email,password_hash,role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid email or password.';
}
$pageTitle = 'Log in'; require 'header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3 mb-3">Welcome back</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" data-validate>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" required autocomplete="username"></div>
<div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" type="password" id="password" name="password" required autocomplete="current-password"></div>
<button class="btn btn-primary w-100">Log in</button>
</form><p class="mt-3 mb-0 text-muted small">No account yet? <a href="register.php">Register here</a>.</p>
</div></div></div></div>
<?php require 'footer.php'; ?>
