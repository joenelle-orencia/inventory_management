<?php
require_once 'config.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Welcome';
require 'header.php';
?>
<section class="hero rounded-4 p-4 p-md-5 mb-4">
  <div class="row align-items-center g-4">
    <div class="col-lg-7">
      <span class="badge text-bg-light mb-3">Inventory control made simple</span>
      <h1 class="display-5 fw-bold">Know what you have. Know what you need.</h1>
      <p class="lead">Manage products, quantities, prices, and stock levels from one responsive dashboard.</p>
      <a class="btn btn-light btn-lg me-2" href="login.php">Log in</a>
      <a class="btn btn-outline-light btn-lg" href="register.php">Create account</a>
    </div>
    <div class="col-lg-5"><div class="hero-stat"><div class="small opacity-75">StockWise</div><div class="display-6 fw-bold">Inventory</div><div class="mt-3">Products · Stock alerts · User roles</div></div></div>
  </div>
</section>
<div class="row g-3">
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h2 class="h5">Track products</h2><p class="text-muted mb-0">Create, view, edit, and remove inventory records.</p></div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h2 class="h5">Protect access</h2><p class="text-muted mb-0">Secure passwords, sessions, CSRF protection, and role-based pages.</p></div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body"><h2 class="h5">See stock status</h2><p class="text-muted mb-0">Quickly spot low-stock products from the dashboard.</p></div></div></div>
</div>
<?php require 'footer.php'; ?>
