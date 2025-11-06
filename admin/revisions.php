<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
$page_id = (int)($_GET['page_id'] ?? 0);
if ($page_id<=0) { http_response_code(400); echo 'page_id fehlt'; exit; }
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

$action = $_POST['action'] ?? '';
if ($action==='restore') {
  $rid=(int)($_POST['rid']??0);
  $rev = $pdo->prepare('SELECT title, content FROM page_revisions WHERE id=? AND page_id=?');
  $rev->execute([$rid,$page_id]);
  $r = $rev->fetch();
  if ($r) { $pdo->prepare('UPDATE pages SET title=?, content=? WHERE id=?')->execute([$r['title'],$r['content'],$page_id]); $msg='Revision wiederhergestellt.'; }
}

$page = $pdo->prepare('SELECT * FROM pages WHERE id=?'); $page->execute([$page_id]); $p = $page->fetch();
$rows = $pdo->prepare('SELECT * FROM page_revisions WHERE page_id=? ORDER BY id DESC'); $rows->execute([$page_id]); $rows = $rows->fetchAll();

include __DIR__ . '/inc/header.php';
?>
<h1 class="h4">Revisionen · <?=htmlspecialchars($p['title'])?></h1>
<?php if(!empty($msg)):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<div class="card p-0">
<table class="table table-dark table-hover mb-0">
  <thead><tr><th>ID</th><th>Erstellt</th><th>Vorschau</th><th>Aktion</th></tr></thead>
  <tbody>
    <?php foreach($rows as $r): ?>
    <tr>
      <td><?=$r['id']?></td>
      <td><?=$r['created_at']?></td>
      <td class="text-truncate" style="max-width:420px"><?=htmlspecialchars(strip_tags($r['content']))?></td>
      <td>
        <form method="post" class="d-inline">
          <?php csrf_field(); ?>
          <input type="hidden" name="action" value="restore"><input type="hidden" name="rid" value="<?=$r['id']?>">
          <button class="btn btn-sm btn-violet">Wiederherstellen</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>