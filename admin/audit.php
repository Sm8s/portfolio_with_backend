<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
$filter = $_GET['q'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 50;
$off = ($page-1)*$per;

$where = '1=1';
$args = [];
if ($filter !== '') { $where .= ' AND (action LIKE ? OR entity LIKE ? OR detail LIKE ? OR ip LIKE ?)'; $args = array_fill(0, 4, '%'.$filter.'%'); }

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS a.*, u.username FROM audit_log a LEFT JOIN users u ON u.id=a.user_id WHERE $where ORDER BY id DESC LIMIT $per OFFSET $off");
$stmt->execute($args);
$rows = $stmt->fetchAll();
$total = (int)$pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
$pages = max(1, (int)ceil($total/$per));

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Audit-Log</h1>
  <form class="d-flex gap-2" method="get">
    <input class="form-control" name="q" placeholder="Suche (Aktion, Entität, IP, Detail)" value="<?=htmlspecialchars($filter)?>">
    <button class="btn btn-outline-light">Suchen</button>
  </form>
</div>
<div class="card p-0">
  <div class="table-responsive">
    <table class="table table-dark table-hover mb-0">
      <thead><tr><th>ID</th><th>Zeit</th><th>User</th><th>Aktion</th><th>Entität</th><th>Entity-ID</th><th>IP</th><th>Detail</th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?=$r['id']?></td>
          <td><?=$r['created_at']?></td>
          <td><?=htmlspecialchars($r['username'] ?? '—')?></td>
          <td><?=$r['action']?></td>
          <td><?=$r['entity']?></td>
          <td><?=$r['entity_id']?></td>
          <td><?=$r['ip']?></td>
          <td class="text-truncate" style="max-width:500px"><?=htmlspecialchars($r['detail'])?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<nav class="mt-3">
  <ul class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
      <li class="page-item <?=$i==$page?'active':''?>"><a class="page-link" href="?q=<?=urlencode($filter)?>&page=<?=$i?>"><?=$i?></a></li>
    <?php endfor; ?>
  </ul>
</nav>
<?php include __DIR__ . '/inc/footer.php'; ?>