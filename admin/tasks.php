<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();
if (file_exists(__DIR__.'/inc/audit.php')) require_once __DIR__.'/inc/audit.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$msg=''; $err='';

function get_areas($pdo){
  return $pdo->query('SELECT id, name FROM areas ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
}

if ($action==='create') {
  $area_id = (int)($_POST['area_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = $_POST['description'] ?? '';
  $hint = $_POST['hint'] ?? '';
  $solution_file = $_POST['solution_file'] ?? null;
  if ($area_id<=0 || $title==='') { $err='Area und Titel sind Pflicht.'; }
  else {
    $st = $pdo->prepare('INSERT INTO tasks(area_id,title,description,hint,solution_file) VALUES (?,?,?,?,?)');
    try { $st->execute([$area_id,$title,$description,$hint,$solution_file]); $msg='Aufgabe erstellt.'; if(function_exists('audit_log')) audit_log('create','task',$pdo->lastInsertId(),$title); }
    catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}

if ($action==='update') {
  $id = (int)($_POST['id'] ?? 0);
  $area_id = (int)($_POST['area_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = $_POST['description'] ?? '';
  $hint = $_POST['hint'] ?? '';
  $solution_file = $_POST['solution_file'] ?? null;
  if ($id<=0 || $area_id<=0 || $title==='') { $err='ID, Area und Titel sind Pflicht.'; }
  else {
    $st = $pdo->prepare('UPDATE tasks SET area_id=?, title=?, description=?, hint=?, solution_file=? WHERE id=?');
    try { $st->execute([$area_id,$title,$description,$hint,$solution_file,$id]); $msg='Aufgabe aktualisiert.'; if(function_exists('audit_log')) audit_log('update','task',$id,$title); }
    catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}

if ($action==='delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id>0) {
    try { $pdo->prepare('DELETE FROM tasks WHERE id=?')->execute([$id]); $msg='Aufgabe gelöscht.'; if(function_exists('audit_log')) audit_log('delete','task',$id,'delete'); }
    catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}

$rows = $pdo->query('SELECT t.*, a.name AS area_name FROM tasks t LEFT JOIN areas a ON a.id=t.area_id ORDER BY t.id DESC')->fetchAll(PDO::FETCH_ASSOC);
$areas = get_areas($pdo);

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Aufgaben</h1>
  <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#modalCreate">Neue Aufgabe</button>
</div>

<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

<div class="card p-0">
  <div class="table-responsive">
    <table class="table table-dark table-hover align-middle mb-0">
      <thead>
        <tr><th>ID</th><th>Area</th><th>Titel</th><th>Hinweis</th><th style="width:210px"></th></tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['area_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <td class="text-truncate" style="max-width:360px"><?= htmlspecialchars($r['hint']) ?></td>
          <td class="d-flex flex-wrap gap-2">
            <button
              class="btn btn-sm btn-outline-light"
              data-bs-toggle="modal"
              data-bs-target="#modalEdit"
              data-id="<?= $r['id'] ?>"
              data-area="<?= (int)$r['area_id'] ?>"
              data-title="<?= htmlspecialchars($r['title'],ENT_QUOTES) ?>"
              data-description="<?= htmlspecialchars($r['description'],ENT_QUOTES) ?>"
              data-hint="<?= htmlspecialchars($r['hint'],ENT_QUOTES) ?>"
              data-solution="<?= htmlspecialchars($r['solution_file'] ?? '',ENT_QUOTES) ?>"
            >Bearbeiten</button>

            <form method="post" onsubmit="return confirm('Wirklich löschen?')">
              <?php csrf_field(); ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Löschen</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content glassy">
      <div class="modal-header">
        <h5 class="modal-title">Neue Aufgabe</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="create">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Area</label>
              <select class="form-select" name="area_id" required>
                <option value="">Bitte wählen…</option>
                <?php foreach($areas as $a): ?>
                  <option value="<?=$a['id']?>"><?=htmlspecialchars($a['name'])?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Titel</label>
              <input class="form-control" name="title" required>
            </div>
            <div class="col-12">
              <label class="form-label">Beschreibung</label>
              <textarea class="form-control" rows="6" name="description"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Hinweis</label>
              <textarea class="form-control" rows="3" name="hint"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Lösungsdatei (optional)</label>
              <input class="form-control" name="solution_file" placeholder="z. B. solution1.zip">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Abbrechen</button>
          <button class="btn btn-violet">Speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content glassy">
      <div class="modal-header">
        <h5 class="modal-title">Aufgabe bearbeiten</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Area</label>
              <select class="form-select" name="area_id" id="edit-area" required>
                <option value="">Bitte wählen…</option>
                <?php foreach($areas as $a): ?>
                  <option value="<?=$a['id']?>"><?=htmlspecialchars($a['name'])?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Titel</label>
              <input class="form-control" name="title" id="edit-title" required>
            </div>
            <div class="col-12">
              <label class="form-label">Beschreibung</label>
              <textarea class="form-control" rows="6" name="description" id="edit-description"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Hinweis</label>
              <textarea class="form-control" rows="3" name="hint" id="edit-hint"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Lösungsdatei (optional)</label>
              <input class="form-control" name="solution_file" id="edit-solution" placeholder="z. B. solution1.zip">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Abbrechen</button>
          <button class="btn btn-violet">Speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('modalEdit').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  const id = btn.getAttribute('data-id');
  const area = btn.getAttribute('data-area');
  const title = btn.getAttribute('data-title');
  const description = btn.getAttribute('data-description');
  const hint = btn.getAttribute('data-hint');
  const solution = btn.getAttribute('data-solution');
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-area').value = area;
  document.getElementById('edit-title').value = title;
  document.getElementById('edit-description').value = description;
  document.getElementById('edit-hint').value = hint;
  document.getElementById('edit-solution').value = solution;
});
</script>

<?php include __DIR__ . '/inc/footer.php'; ?>
