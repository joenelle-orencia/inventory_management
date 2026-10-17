<?php
require_once 'config.php'; require_admin();
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $id=filter_var($_POST['id']??0,FILTER_VALIDATE_INT)?:0;
    $role=(string)($_POST['role']??'user');
    if (!in_array($role,['admin','user'],true)) $errors[]='Invalid role.';
    elseif ($id===(int)$_SESSION['user_id'] && $role!=='admin') $errors[]='You cannot remove your own administrator role.';
    else {
        $stmt=$pdo->prepare('UPDATE users SET role=? WHERE id=?'); $stmt->execute([$role,$id]);
        flash('User role updated.'); header('Location: users.php'); exit;
    }
}
$users=$pdo->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();
$pageTitle='User management'; require 'header.php';
?>
<h1 class="h2 mb-1">User management</h1><p class="text-muted">Administrators can assign admin or standard-user access.</p>
<?php foreach($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<div class="alert alert-info">Only grant administrator access to trusted accounts. Standard users can add and edit products but cannot delete products or manage users.</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Change role</th></tr></thead><tbody>
<?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e(ucfirst($u['role'])) ?></td><td><?= e($u['created_at']) ?></td><td><form method="post" class="d-flex gap-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><select class="form-select form-select-sm" name="role" aria-label="Role for <?= e($u['name']) ?>"><option value="user" <?= $u['role']==='user'?'selected':'' ?>>User</option><option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option></select><button class="btn btn-sm btn-primary">Save</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require 'footer.php'; ?>
