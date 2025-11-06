<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$msg=''; $err='';

if ($action==='export' && ($_GET['type'] ?? '')==='tasks') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="tasks.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['id','area_id','title','description','hint','solution_file','is_active']);
  foreach ($pdo->query('SELECT id,area_id,title,description,hint,solution_file,is_active FROM tasks ORDER BY id') as $r) fputcsv($out, $r);
  fclose($out); exit;
}
if ($action==='export' && ($_GET['type'] ?? '')==='areas') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="areas.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['id','name','strengths','weaknesses','description']);
  foreach ($pdo->query('SELECT id,name,strengths,weaknesses,description FROM areas ORDER BY id') as $r) fputcsv($out, $r);
  fclose($out); exit;
}

// Der CSV-Import für Aufgaben erfolgt nun direkt auf der Aufgaben-Seite.

include __DIR__ . '/inc/header.php';
?>
<h1 class="h4">CSV Export</h1>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
<div class="card p-3 mb-4">
  <h5>Datensätze exportieren</h5>
  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-light" href="?action=export&type=areas">Areas CSV</a>
    <a class="btn btn-outline-light" href="?action=export&type=tasks">Tasks CSV</a>
  </div>
</div>
<div class="alert alert-info">
  Der CSV-Import für Aufgaben befindet sich jetzt direkt in der <a class="link-light" href="/portfolio_with_backend/admin/tasks.php">Aufgabenverwaltung</a> und bietet dort eine Vorschau mit Duplikatprüfung.
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>
