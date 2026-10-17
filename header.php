<?php
$pageTitle = $pageTitle ?? 'Inventory Manager';
$notice = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Inventory</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= is_logged_in() ? 'dashboard.php' : 'index.php' ?>">Inventory</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navMain">
      <div class="navbar-nav me-auto">
        <?php if (is_logged_in()): ?>
          <a class="nav-link" href="dashboard.php">Dashboard</a>
          <a class="nav-link" href="products.php">Products</a>
          <?php if (is_admin()): ?><a class="nav-link" href="users.php">Users</a><?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="navbar-nav align-items-lg-center">
        <?php if (is_logged_in()): ?>
          <span class="navbar-text me-lg-3"><?= e($_SESSION['name'] ?? 'User') ?> · <?= e(ucfirst($_SESSION['role'] ?? 'user')) ?></span>
          <a class="btn btn-outline-light btn-sm" href="logout.php">Log out</a>
        <?php else: ?>
          <a class="nav-link" href="login.php">Log in</a>
          <a class="btn btn-primary btn-sm ms-lg-2" href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($notice): ?>
  <div class="alert alert-<?= e($notice['type']) ?> alert-dismissible fade show" role="alert">
    <?= e($notice['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
