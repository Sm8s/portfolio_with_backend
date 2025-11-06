
<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/audit.php';
require_once __DIR__ . '/inc/csrf.php';
csrf_check();
require_admin();
$action = $_POST['action'] ?? '';
$msg=''; $err='';

if ($action==='create') {
  $username=trim($_POST['username']??''); $password=$_POST['password']??''; $is_admin=isset($_POST['is_admin'])?1:0;
  if ($username===''||$password==='') $err='Benutzername/Passwort fehlen.';
  else {
    $hash=password_hash($password, PASSWORD_BCRYPT);
    try{ $pdo->prepare('INSERT INTO users(username,password,is_admin) VALUES (?,?,?)')->execute([$username,$hash,$is_admin]); $msg='Benutzer erstellt.';
  audit_log('create','user', $pdo->lastInsertId(), $username); } catch(Throwable $e){ $err=$e->getMessage(); }
  }
}
if ($action==='update') {
  $id=(int)($_POST['id']??0); $username=trim($_POST['username']??''); $is_admin=isset($_POST['is_admin'])?1:0;
  if ($id<=0) $err='ID fehlt.';
  else {
    $fields=['username=?','is_admin=?']; $args=[$username,$is_admin];
    if (!empty($_POST['password'])) { $fields[]='password=?'; $args[]=password_hash($_POST['password'], PASSWORD_BCRYPT); }
    $args[]=$id;
    $sql='UPDATE users SET '.implode(',', $fields).' WHERE id=?';
    $pdo->prepare($sql)->execute($args); $msg='Benutzer aktualisiert.';
  audit_log('update','user', $id, $username);
  }
}
if ($action==='delete') {
  $id=(int)($_POST['id']??0); if ($id>0) { $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]); $msg='Benutzer gelöscht.';
  audit_log('delete','user', $id, 'delete'); }
}

$rows=$pdo->query('SELECT id,username,is_admin FROM users ORDER BY id DESC')->fetchAll();

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Benutzerverwaltung</h1>
  <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#createModal">Neuer Benutzer</button>
</div>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

<div class="card p-0">
<table class="table table-dark table-hover mb-0">
  <thead><tr><th>ID</th><th>Username</th><th>Admin</th><th style="width:200px"></th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?=$r['id']?></td><td><?=$r['username']?></td><td><?=$r['is_admin']?'Ja':'Nein'?></td>
      <td><button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#u<?=$r['id']?>">Bearbeiten</button></td>
    </tr>
    <tr class="collapse" id="u<?=$r['id']?>">
      <td colspan="4">
        <form method="post" class="d-flex gap-2 align-items-end flex-wrap">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?=$r['id']?>">
          <div><label class="form-label">Username</label><input class="form-control" name="username" value="<?=$r['username']?>" required></div>
          <div><label class="form-label">Passwort (neu)</label><input class="form-control" name="password" placeholder="leer = unverändert"></div>
          <div class="form-check ms-2 mb-3"><input class="form-check-input" type="checkbox" name="is_admin" <?=$r['is_admin']?'checked':''?>> <label class="form-check-label">Admin</label></div>
          <button class="btn btn-violet">Speichern</button>
        </form>
        <form method="post" onsubmit="return confirm('Benutzer wirklich löschen?')" class="mt-2">
          <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>">
          <button class="btn btn-outline-danger">Löschen</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glassy">
      <div class="modal-header"><h5 class="modal-title">Neuer Benutzer</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form method="post"><?php csrf_field(); ?>
          <input type="hidden" name="action" value="create">
          <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
          <div class="mb-3"><label class="form-label">Passwort</label><input class="form-control" name="password" required></div>
          <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_admin" id="adminFlag"><label class="form-check-label" for="adminFlag">Admin</label></div>
          <button class="btn btn-violet">Erstellen</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>
