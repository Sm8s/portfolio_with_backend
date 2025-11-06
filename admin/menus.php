<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

$action = $_POST['action'] ?? '';
if ($action==='create') {
  $label=trim($_POST['label']??''); $url=trim($_POST['url']??''); $pos=(int)($_POST['position']??0);
  $pdo->prepare('INSERT INTO menus(label,url,position) VALUES (?,?,?)')->execute([$label,$url,$pos]);
}
if ($action==='delete') {
  $id=(int)($_POST['id']??0); if($id) $pdo->prepare('DELETE FROM menus WHERE id=?')->execute([$id]);
}
if ($action==='saveOrder') {
  $order = $_POST['order'] ?? '';
  $items = explode(',', $order);
  $pos = 0;
  foreach ($items as $id) { $id=(int)$id; if($id>0){ $pdo->prepare('UPDATE menus SET position=? WHERE id=?')->execute([$pos++,$id]); } }
  exit('ok');
}

$rows = $pdo->query('SELECT * FROM menus ORDER BY position ASC, id ASC')->fetchAll();

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Menü-Builder</h1>
  <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#createModal">Neuer Link</button>
</div>

<div class="card p-3">
  <ul id="menuList" class="list-group">
    <?php foreach($rows as $r): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center" draggable="true" data-id="<?=$r['id']?>">
        <div>
          <span class="badge bg-secondary me-2">#<?=$r['position']?></span>
          <strong><?=htmlspecialchars($r['label'])?></strong>
          <span class="text-secondary ms-2"><?=htmlspecialchars($r['url'])?></span>
        </div>
        <form method="post" class="m-0" onsubmit="return confirm('Link löschen?')">
          <?php csrf_field(); ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?=$r['id']?>">
          <button class="btn btn-sm btn-outline-danger">Löschen</button>
        </form>
      </li>
    <?php endforeach; ?>
  </ul>
  <div class="text-end mt-3"><button class="btn btn-violet" id="saveOrder">Reihenfolge speichern</button></div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content glassy">
      <div class="modal-header"><h5 class="modal-title">Neuer Menüpunkt</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form method="post">
          <?php csrf_field(); ?>
          <input type="hidden" name="action" value="create">
          <div class="mb-3"><label class="form-label">Label</label><input class="form-control" name="label" required></div>
          <div class="mb-3"><label class="form-label">URL</label><input class="form-control" name="url" placeholder="/portfolio_with_backend/page.php?slug=about" required></div>
          <div class="mb-3"><label class="form-label">Position</label><input type="number" class="form-control" name="position" value="0"></div>
          <button class="btn btn-violet">Erstellen</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// drag & drop ordering
const list = document.getElementById('menuList');
let dragEl = null;
list.addEventListener('dragstart', (e)=>{ dragEl = e.target.closest('li'); });
list.addEventListener('dragover', (e)=>{
  e.preventDefault();
  const li = e.target.closest('li');
  if (!li || li===dragEl) return;
  const rect = li.getBoundingClientRect();
  const next = (e.clientY - rect.top) / (rect.height) > 0.5;
  list.insertBefore(dragEl, next ? li.nextSibling : li);
});
document.getElementById('saveOrder').addEventListener('click', ()=>{
  const ids = [...list.querySelectorAll('li')].map(li => li.dataset.id).join(',');
  const form = new FormData();
  form.append('action','saveOrder'); form.append('order', ids);
  fetch('', {method:'POST', body:form}).then(r=>r.text()).then(()=> location.reload());
});
</script>

<?php include __DIR__ . '/inc/footer.php'; ?>