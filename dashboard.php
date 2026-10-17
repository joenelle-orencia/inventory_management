<?php
require_once 'config.php'; require_login();
$total = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$units = (int)$pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn();
$low = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= low_stock_threshold')->fetchColumn();
$recent = $pdo->query('SELECT id,name,sku,quantity,low_stock_threshold,updated_at FROM products ORDER BY updated_at DESC, id DESC LIMIT 8')->fetchAll();
$pageTitle = 'Dashboard'; require 'header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div><h1 class="h2 mb-1">Dashboard</h1><p class="text-muted mb-0">Inventory overview and recent changes.</p></div><a class="btn btn-primary" href="products.php?action=create">+ Add product</a></div>
<div class="row g-3 mb-4">
<div class="col-sm-6 col-xl-4"><div class="stat-card"><div class="text-muted">Product types</div><div class="stat-number"><?= $total ?></div></div></div>
<div class="col-sm-6 col-xl-4"><div class="stat-card"><div class="text-muted">Units in stock</div><div class="stat-number"><?= $units ?></div></div></div>
<div class="col-sm-6 col-xl-4"><div class="stat-card <?= $low ? 'stat-warning' : '' ?>"><div class="text-muted">Low-stock products</div><div class="stat-number"><?= $low ?></div></div></div>
</div>
<div class="card"><div class="card-header bg-white py-3 d-flex justify-content-between align-items-center"><strong>Recently updated products</strong><a href="products.php">View all</a></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Product</th><th>SKU</th><th>Quantity</th><th>Status</th><th>Last updated</th></tr></thead><tbody>
<?php if (!$recent): ?><tr><td colspan="5" class="text-center text-muted py-4">No products yet. Add your first product.</td></tr><?php endif; ?>
<?php foreach ($recent as $p): ?><tr><td><?= e($p['name']) ?></td><td><?= e($p['sku']) ?></td><td><?= (int)$p['quantity'] ?></td><td><?php if ((int)$p['quantity'] <= (int)$p['low_stock_threshold']): ?><span class="badge text-bg-warning">Low stock</span><?php else: ?><span class="badge text-bg-success">In stock</span><?php endif; ?></td><td><?= e($p['updated_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'footer.php'; ?>
