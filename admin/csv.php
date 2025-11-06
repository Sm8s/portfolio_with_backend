<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$msg=''; $err='';

function csv_trim_bom($s){ return ltrim($s, "\xEF\xBB\xBF"); }

if ($action==='export' && ($_GET['type'] ?? '')==='tasks') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="tasks.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['id','area_id','title','description','hint','solution_file']);
  foreach ($pdo->query('SELECT id,area_id,title,description,hint,solution_file FROM tasks ORDER BY id') as $r) fputcsv($out, $r);
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

if ($action==='import' && !empty($_FILES['csv']['tmp_name'])) {
  $type = $_POST['type'] ?? '';
  $fh = fopen($_FILES['csv']['tmp_name'], 'r');
  if (!$fh) { $err='CSV konnte nicht gelesen werden.'; }
  else {
    $pdo->beginTransaction();
    try {
      $header = fgetcsv($fh);
      if ($header && $header[0] !== null) $header[0] = csv_trim_bom($header[0]);
      if ($type==='areas') {
        // Upsert by UNIQUE(name) or by id if vorhanden
        $selByName = $pdo->prepare('SELECT id FROM areas WHERE name=?');
        $upd = $pdo->prepare('UPDATE areas SET name=?, strengths=?, weaknesses=?, description=? WHERE id=?');
        $ins = $pdo->prepare('INSERT INTO areas(name,strengths,weaknesses,description) VALUES (?,?,?,?)');
        while (($row = fgetcsv($fh)) !== false) {
          $row = array_map('trim', $row);
          // Map columns tolerant: id,name,strengths,weaknesses,description
          $id = $row[0] !== '' ? (int)$row[0] : null;
          $name = $row[1] ?? $row[0] ?? '';
          $s = $row[2] ?? '';
          $w = $row[3] ?? '';
          $d = $row[4] ?? '';
          if ($name==='') continue;
          if ($id) {
            // try update by id; if no row, fallback to insert
            $count = $pdo->prepare('SELECT COUNT(*) FROM areas WHERE id=?'); $count->execute([$id]);
            if ($count->fetchColumn()>0) { $upd->execute([$name,$s,$w,$d,$id]); }
            else { $ins->execute([$name,$s,$w,$d]); }
          } else {
            // upsert by name
            $selByName->execute([$name]);
            $found = $selByName->fetchColumn();
            if ($found) { $upd->execute([$name,$s,$w,$d,$found]); }
            else { $ins->execute([$name,$s,$w,$d]); }
          }
        }
        $msg='Areas importiert (idempotent).';
      } else if ($type==='tasks') {
        // Upsert by id if present, else by (area_id,title)
        $selByKey = $pdo->prepare('SELECT id FROM tasks WHERE area_id=? AND title=?');
        $upd = $pdo->prepare('UPDATE tasks SET area_id=?, title=?, description=?, hint=?, solution_file=? WHERE id=?');
        $ins = $pdo->prepare('INSERT INTO tasks(area_id,title,description,hint,solution_file) VALUES (?,?,?,?,?)');
        while (($row = fgetcsv($fh)) !== false) {
          $row = array_map('trim', $row);
          // id,area_id,title,description,hint,solution_file
          $id = ($row[0] ?? '')!=='' ? (int)$row[0] : null;
          $area_id = ($row[1] ?? '')!=='' ? (int)$row[1] : null;
          $title = $row[2] ?? '';
          $desc = $row[3] ?? '';
          $hint = $row[4] ?? '';
          $file = $row[5] ?? '';
          if ($title==='') continue;
          if ($id) {
            $count = $pdo->prepare('SELECT COUNT(*) FROM tasks WHERE id=?'); $count->execute([$id]);
            if ($count->fetchColumn()>0) { $upd->execute([$area_id,$title,$desc,$hint,$file,$id]); }
            else { $ins->execute([$area_id,$title,$desc,$hint,$file]); }
          } else {
            $selByKey->execute([$area_id,$title]);
            $found = $selByKey->fetchColumn();
            if ($found) { $upd->execute([$area_id,$title,$desc,$hint,$file,$found]); }
            else { $ins->execute([$area_id,$title,$desc,$hint,$file]); }
          }
        }
        $msg='Tasks importiert (idempotent).';
      } else {
        $err='Unbekannter Typ.';
      }
      $pdo->commit();
    } catch (Throwable $e) {
      $pdo->rollBack();
      $err = 'Import fehlgeschlagen: ' . $e->getMessage();
    }
    fclose($fh);
  }
}

include __DIR__ . '/inc/header.php';
?>
<h1 class="h4">CSV Import/Export</h1>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card p-3">
      <h5>Export</h5>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="?action=export&type=areas">Areas CSV</a>
        <a class="btn btn-outline-light" href="?action=export&type=tasks">Tasks CSV</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <h5>Import</h5>
      <form method="post" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="import">
        <div class="mb-2">
          <select class="form-select" name="type" required>
            <option value="areas">Areas</option>
            <option value="tasks">Tasks</option>
          </select>
        </div>
        <input type="file" name="csv" class="form-control mb-2" accept=".csv" required>
        <button class="btn btn-violet">Importieren</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>
