<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$msg=''; $err='';

if ($action==='create') {
  $slug = trim($_POST['slug'] ?? '');
  $title = trim($_POST['title'] ?? '');
  $content = $_POST['content'] ?? '';
  $delta = $_POST['content_delta'] ?? null;
  if ($slug===''||$title==='') { $err='Slug und Titel sind Pflicht.'; }
  else {
    $st=$pdo->prepare('INSERT INTO pages(slug,title,content,content_delta) VALUES (?,?,?,?)');
    try { $st->execute([$slug,$title,$content,$delta]); $msg='Seite erstellt.'; } catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}
if ($action==='update') {
  $id=(int)($_POST['id']??0);
  $slug=trim($_POST['slug']??''); $title=trim($_POST['title']??''); $content=$_POST['content']??''; $delta=$_POST['content_delta']??null;
  if ($id<=0) $err='ID fehlt.';
  else {
    $pdo->prepare('INSERT INTO page_revisions(page_id,title,content) SELECT id,title,content FROM pages WHERE id=?')->execute([$id]);
    $st=$pdo->prepare('UPDATE pages SET slug=?, title=?, content=?, content_delta=? WHERE id=?');
    try { $st->execute([$slug,$title,$content,$delta,$id]); $msg='Seite aktualisiert.'; } catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}
if ($action==='trash') { $id=(int)($_POST['id']??0); if ($id>0) { $pdo->prepare('UPDATE pages SET deleted_at=NOW() WHERE id=?')->execute([$id]); $msg='Seite in den Papierkorb verschoben.'; } }
if ($action==='restore') { $id=(int)($_POST['id']??0); if ($id>0) { $pdo->prepare('UPDATE pages SET deleted_at=NULL WHERE id=?')->execute([$id]); $msg='Seite wiederhergestellt.'; } }
if ($action==='delete') { $id=(int)($_POST['id']??0); if ($id>0) { $pdo->prepare('DELETE FROM pages WHERE id=?')->execute([$id]); $msg='Seite gelöscht.'; } }

$trash = isset($_GET['trash']);
$query = $trash ? 'SELECT * FROM pages WHERE deleted_at IS NOT NULL ORDER BY updated_at DESC' : 'SELECT * FROM pages WHERE deleted_at IS NULL ORDER BY updated_at DESC';
$rows = $pdo->query($query)->fetchAll();

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Seiten <?php if($trash) echo '(Papierkorb)'; ?></h1>
  <div class="btn-group">
    <a class="btn btn-outline-light" href="?">Aktiv</a>
    <a class="btn btn-outline-light" href="?trash=1">Papierkorb</a>
  </div>
  <button class="btn btn-violet ms-2" data-bs-toggle="modal" data-bs-target="#createModal" <?= $trash? 'disabled':'' ?>>Neue Seite</button>
</div>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
async function bindCKE(textarea){
  const form = textarea.closest('form');
  const hiddenDelta = document.createElement('input');
  hiddenDelta.type='hidden'; hiddenDelta.name='content_delta';
  form.appendChild(hiddenDelta);

  const editor = await ClassicEditor.create(textarea, {
    toolbar: ['heading','bold','italic','underline','strikethrough','link','bulletedList','numberedList','blockQuote','codeBlock','insertTable','undo','redo','alignment','outdent','indent','|','uploadImage','imageTextAlternative'],
    simpleUpload: {
      uploadUrl: '/portfolio_with_backend/admin/ckeditor_upload.php'
    }
  });
  form.addEventListener('submit', ()=>{ hiddenDelta.value=''; });
}
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelectorAll('textarea.cke-bind').forEach(bindCKE);
});
</script>

<div class="card p-0">
  <div class="table-responsive">
    <table class="table table-dark table-hover align-middle mb-0">
      <thead><tr><th>ID</th><th>Slug</th><th>Titel</th><th>Aktualisiert</th><th style="width:220px"></th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
          <td><?=$r['id']?></td>
          <td><?=$r['slug']?></td>
          <td><?=$r['title']?></td>
          <td><?=$r['updated_at']?></td>
          <td class="d-flex flex-wrap gap-2">
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#edit<?=$r['id']?>">Bearbeiten</button>
            <a class="btn btn-sm btn-outline-light" href="revisions.php?page_id=<?=$r['id']?>">Revisionen</a>
            <?php if(!$trash): ?>
            <form method="post" class="d-inline" onsubmit="return confirm('In den Papierkorb verschieben?')">
              <?php csrf_field(); ?>
              <input type="hidden" name="action" value="trash"><input type="hidden" name="id" value="<?=$r['id']?>">
              <button class="btn btn-sm btn-outline-warning">Papierkorb</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <tr class="collapse" id="edit<?=$r['id']?>">
          <td colspan="5">
            <form method="post">
              <?php csrf_field(); ?>
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?=$r['id']?>">
              <div class="row g-3">
                <div class="col-md-3"><input class="form-control" name="slug" value="<?=$r['slug']?>" required></div>
                <div class="col-md-4"><input class="form-control" name="title" value="<?=$r['title']?>" required></div>
                <div class="col-12">
                  <textarea class="form-control cke-bind" rows="10" name="content"><?=htmlspecialchars($r['content'])?></textarea>
                </div>
                <div class="col-12 d-flex gap-2 flex-wrap">
                  <button class="btn btn-violet">Speichern</button>
            </form>
            <form method="post" onsubmit="return confirm('Wirklich löschen?')">
              <?php csrf_field(); ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=$r['id']?>">
              <button class="btn btn-outline-danger">Löschen</button>
            </form>
                </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content glassy">
      <div class="modal-header"><h5 class="modal-title">Neue Seite</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form method="post">
          <?php csrf_field(); ?>
          <input type="hidden" name="action" value="create">
          <div class="mb-3"><label class="form-label">Slug</label><input class="form-control" name="slug" required placeholder="z.B. about"></div>
          <div class="mb-3"><label class="form-label">Titel</label><input class="form-control" name="title" required></div>
          <div class="mb-3"><label class="form-label">Inhalt</label>
            <textarea class="form-control cke-bind" rows="10" name="content"></textarea>
          </div>
          <button class="btn btn-violet">Erstellen</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/inc/footer.php'; ?>
