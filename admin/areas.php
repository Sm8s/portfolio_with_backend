
<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/audit.php';
require_once __DIR__ . '/inc/csrf.php';
csrf_check();
require_admin();
$action = $_POST['action'] ?? '';
$msg=''; $err='';

if ($action==='create') {
  $name=trim($_POST['name']??''); $strengths=$_POST['strengths']??null; $weaknesses=$_POST['weaknesses']??null; $description=$_POST['description']??null;
  if ($name==='') $err='Name fehlt.';
  else { $pdo->prepare('INSERT INTO areas(name,strengths,weaknesses,description) VALUES (?,?,?,?)')->execute([$name,$strengths,$weaknesses,$description]); $msg='Area erstellt.';
  audit_log('create','area', $pdo->lastInsertId(), $name); }
}
if ($action==='update') {
  $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $strengths=$_POST['strengths']??null; $weaknesses=$_POST['weaknesses']??null; $description=$_POST['description']??null;
  if ($id<=0) $err='ID fehlt.';
  else { $pdo->prepare('UPDATE areas SET name=?, strengths=?, weaknesses=?, description=? WHERE id=?')->execute([$name,$strengths,$weaknesses,$description,$id]); $msg='Area aktualisiert.';
  audit_log('update','area', $id, $name); }
}
if ($action==='delete') {
  $id=(int)($_POST['id']??0); if ($id>0) { $pdo->prepare('DELETE FROM areas WHERE id=?')->execute([$id]); $msg='Area gelöscht.';
  audit_log('delete','area', $id, 'delete'); }
}

$rows=$pdo->query('SELECT * FROM areas ORDER BY id DESC')->fetchAll();

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Areas</h1>
  <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#createModal">Neue Area</button>
</div>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

<div class="card p-0">
<table class="table table-dark table-hover mb-0">
  <thead><tr><th>ID</th><th>Name</th><th>Stärken</th><th>Schwächen</th><th style="width:160px"></th></tr></thead>
  <tbody>
    <?php foreach($rows as $r): ?>
    <tr>
      <td><?=$r['id']?></td><td><?=$r['name']?></td><td class="text-truncate" style="max-width: 260px"><?=$r['strengths']?></td><td class="text-truncate" style="max-width: 260px"><?=$r['weaknesses']?></td>
      <td><button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#a<?=$r['id']?>">Bearbeiten</button></td>
    </tr>
    <tr class="collapse" id="a<?=$r['id']?>">
      <td colspan="5">
        <form method="post"><?php csrf_field(); ?>
          <input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=$r['id']?>">
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Name</label><input class="form-control" name="name" value="<?=$r['name']?>" required></div>
            <div class="col-md-4"><label class="form-label">Stärken</label><textarea class="form-control" name="strengths" rows="3"><?=$r['strengths']?></textarea></div>
            <div class="col-md-4"><label class="form-label">Schwächen</label><textarea class="form-control" name="weaknesses" rows="3"><?=$r['weaknesses']?></textarea></div>
            <div class="col-12"><label class="form-label">Beschreibung</label><textarea class="form-control" name="description" rows="6"><?=$r['description']?></textarea></div>
          </div>
          <div class="mt-2 d-flex gap-2">
            <button class="btn btn-violet">Speichern</button>
        </form>
        <form method="post" onsubmit="return confirm('Wirklich löschen?')">
          <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>">
          <button class="btn btn-outline-danger">Löschen</button>
        </form>
          </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content glassy">
      <div class="modal-header"><h5 class="modal-title">Neue Area</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form method="post"><?php csrf_field(); ?>
          <input type="hidden" name="action" value="create">
          <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Stärken</label><textarea class="form-control" name="strengths" rows="3"></textarea></div>
          <div class="mb-3"><label class="form-label">Schwächen</label><textarea class="form-control" name="weaknesses" rows="3"></textarea></div>
          <div class="mb-3"><label class="form-label">Beschreibung</label><textarea class="form-control" name="description" rows="6"></textarea></div>
          <button class="btn btn-violet">Erstellen</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>
