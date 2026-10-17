<?php
require_once 'config.php'; require_login();
$action = (string)($_GET['action'] ?? 'list');
$errors = [];
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
$product = ['name'=>'','sku'=>'','category'=>'','description'=>'','quantity'=>'0','unit_price'=>'0.00','low_stock_threshold'=>'5'];
if ($action === 'edit') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?'); $stmt->execute([$id]);
    $found = $stmt->fetch(); if (!$found) { flash('Product not found.', 'danger'); header('Location: products.php'); exit; }
    $product = $found;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? 'list');
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
    if ($action === 'delete') {
        if (!is_admin()) { http_response_code(403); exit('Only administrators can delete products.'); }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?'); $stmt->execute([$id]);
        flash('Product deleted.'); header('Location: products.php'); exit;
    }
    $product = [
        'name'=>trim((string)($_POST['name'] ?? '')),
        'sku'=>trim((string)($_POST['sku'] ?? '')),
        'category'=>trim((string)($_POST['category'] ?? '')),
        'description'=>trim((string)($_POST['description'] ?? '')),
        'quantity'=>(string)($_POST['quantity'] ?? ''),
        'unit_price'=>(string)($_POST['unit_price'] ?? ''),
        'low_stock_threshold'=>(string)($_POST['low_stock_threshold'] ?? '5')
    ];
    if ($product['name']==='' || mb_strlen($product['name'])>150) $errors[]='Product name is required (maximum 150 characters).';
    if ($product['sku']==='' || mb_strlen($product['sku'])>60) $errors[]='SKU is required (maximum 60 characters).';
    if (!filter_var($product['quantity'], FILTER_VALIDATE_INT) || (int)$product['quantity']<0) $errors[]='Quantity must be a non-negative whole number.';
    if (!is_numeric($product['unit_price']) || (float)$product['unit_price']<0) $errors[]='Price must be a non-negative number.';
    if (!filter_var($product['low_stock_threshold'], FILTER_VALIDATE_INT) || (int)$product['low_stock_threshold']<0) $errors[]='Low-stock threshold must be a non-negative whole number.';
    if (!$errors) {
        try {
            if ($action === 'update') {
                $stmt=$pdo->prepare('UPDATE products SET name=?,sku=?,category=?,description=?,quantity=?,unit_price=?,low_stock_threshold=?,updated_at=NOW() WHERE id=?');
                $stmt->execute([$product['name'],$product['sku'],$product['category'],$product['description'],(int)$product['quantity'],number_format((float)$product['unit_price'],2,'.',''),(int)$product['low_stock_threshold'],$id]);
                flash('Product updated.');
            } else {
                $stmt=$pdo->prepare('INSERT INTO products (name,sku,category,description,quantity,unit_price,low_stock_threshold,created_by) VALUES (?,?,?,?,?,?,?,?)');
                $stmt->execute([$product['name'],$product['sku'],$product['category'],$product['description'],(int)$product['quantity'],number_format((float)$product['unit_price'],2,'.',''),(int)$product['low_stock_threshold'],(int)$_SESSION['user_id']]);
                flash('Product added.');
            }
            header('Location: products.php'); exit;
        } catch (PDOException $e) {
            if ($e->getCode()==='23000') $errors[]='That SKU already exists. Please use a unique SKU.';
            else $errors[]='Could not save the product. Please check your input and database.';
        }
    }
    $action = $action === 'update' ? 'edit' : 'create';
}
if (in_array($action, ['create','edit'], true)) {
    $pageTitle = $action === 'edit' ? 'Edit product' : 'Add product'; require 'header.php';
    ?>
    <div class="row justify-content-center"><div class="col-lg-8"><div class="card shadow-sm"><div class="card-body p-4">
    <h1 class="h3 mb-3"><?= $action==='edit' ? 'Edit product' : 'Add product' ?></h1>
    <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
    <form method="post" data-validate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="<?= $action==='edit' ? 'update' : 'create' ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="name">Product name *</label><input class="form-control" id="name" name="name" maxlength="150" required value="<?= e((string)$product['name']) ?>"></div>
    <div class="col-md-6"><label class="form-label" for="sku">SKU / product code *</label><input class="form-control" id="sku" name="sku" maxlength="60" required value="<?= e((string)$product['sku']) ?>"></div>
    <div class="col-md-6"><label class="form-label" for="category">Category</label><input class="form-control" id="category" name="category" maxlength="100" value="<?= e((string)$product['category']) ?>" placeholder="e.g. Electronics"></div>
    <div class="col-md-6"><label class="form-label" for="quantity">Quantity *</label><input class="form-control" type="number" id="quantity" name="quantity" min="0" step="1" required value="<?= e((string)$product['quantity']) ?>"></div>
    <div class="col-md-6"><label class="form-label" for="unit_price">Unit price *</label><input class="form-control" type="number" id="unit_price" name="unit_price" min="0" step="0.01" required value="<?= e((string)$product['unit_price']) ?>"></div>
    <div class="col-md-6"><label class="form-label" for="low_stock_threshold">Low-stock threshold *</label><input class="form-control" type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" step="1" required value="<?= e((string)$product['low_stock_threshold']) ?>"></div>
    <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="3" maxlength="2000"><?= e((string)$product['description']) ?></textarea></div>
    </div><div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Save product</button><a class="btn btn-outline-secondary" href="products.php">Cancel</a></div>
    </form></div></div></div></div>
    <?php require 'footer.php'; exit;
}
$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT * FROM products';
$params = [];
if ($q !== '') { $sql .= ' WHERE name LIKE ? OR sku LIKE ? OR category LIKE ?'; $like='%'.$q.'%'; $params=[$like,$like,$like]; }
$sql .= ' ORDER BY name ASC';
$stmt=$pdo->prepare($sql); $stmt->execute($params); $products=$stmt->fetchAll();
$pageTitle='Products'; require 'header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><h1 class="h2 mb-1">Products</h1><p class="text-muted mb-0">Manage stock records and search the inventory.</p></div><a class="btn btn-primary" href="products.php?action=create">+ Add product</a></div>
<form class="row g-2 mb-3" method="get"><div class="col-sm-8 col-md-5"><label class="visually-hidden" for="q">Search products</label><input class="form-control" id="q" name="q" value="<?= e($q) ?>" placeholder="Search name, SKU, or category"></div><div class="col-auto"><button class="btn btn-outline-primary">Search</button></div><?php if($q!==''): ?><div class="col-auto"><a class="btn btn-outline-secondary" href="products.php">Clear</a></div><?php endif; ?></form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Name / SKU</th><th>Category</th><th>Qty</th><th>Unit price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if (!$products): ?><tr><td colspan="6" class="text-center text-muted py-4">No matching products found.</td></tr><?php endif; ?>
<?php foreach($products as $p): ?><tr><td><strong><?= e($p['name']) ?></strong><div class="small text-muted"><?= e($p['sku']) ?></div></td><td><?= e($p['category']) ?: '—' ?></td><td><?= (int)$p['quantity'] ?></td><td>₱<?= number_format((float)$p['unit_price'],2) ?></td><td><?php if((int)$p['quantity'] <= (int)$p['low_stock_threshold']): ?><span class="badge text-bg-warning">Low stock</span><?php else: ?><span class="badge text-bg-success">In stock</span><?php endif; ?></td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="products.php?action=edit&id=<?= (int)$p['id'] ?>">Edit</a><?php if(is_admin()): ?><form method="post" data-confirm="Delete this product? This cannot be undone."><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form><?php endif; ?></div></td></tr><?php endforeach; ?>
</tbody></table></div></div><p class="small text-muted mt-2"><?= count($products) ?> product(s) shown. Delete is administrator-only.</p>
<?php require 'footer.php'; ?>
