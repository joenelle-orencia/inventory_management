<?php
require_once 'config.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
$errors = [];
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter a name (up to 100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'That email is already registered.';
        else {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'user']);
            flash('Account created. You can now log in.');
            header('Location: login.php'); exit;
        }
    }
}
$pageTitle = 'Register'; require 'header.php';
?>
<div class="row justify-content-center"><div class="col-md-7 col-lg-5">
<div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h3 mb-3">Create account</h1>
<p class="text-muted">New accounts are standard users. Only an administrator can access user management.</p>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" data-validate>
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" maxlength="100" required value="<?= e($name) ?>"></div>
<div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" type="email" id="email" name="email" maxlength="190" required value="<?= e($email) ?>"></div>
<div class="mb-3"><label class="form-label" for="password">Password (at least 8 characters)</label><input class="form-control" type="password" id="password" name="password" minlength="8" autocomplete="new-password" required></div>
<div class="mb-3"><label class="form-label" for="confirm_password">Confirm password</label><input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" required></div>
<button class="btn btn-primary w-100">Register</button>
</form>
</div></div></div></div>
<?php require 'footer.php'; ?>
