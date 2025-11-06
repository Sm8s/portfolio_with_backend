<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();
if (file_exists(__DIR__.'/inc/audit.php')) require_once __DIR__.'/inc/audit.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$msg=''; $err=''; $errType='danger';
$importPreview = null;

function get_areas(PDO $pdo){
  return $pdo->query('SELECT id, name FROM areas ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
}

function normalize_search_value($value){
  return function_exists('mb_strtolower') ? mb_strtolower((string)$value, 'UTF-8') : strtolower((string)$value);
}

function csv_trim_bom($s){ return ltrim($s, "\xEF\xBB\xBF"); }

function ensure_task_is_active_column(PDO $pdo): void {
  static $checked = false;
  if ($checked) return;
  $checked = true;
  try {
    $pdo->query('SELECT is_active FROM tasks LIMIT 1');
  } catch (Throwable $e) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    try {
      if ($driver === 'pgsql') {
        $pdo->exec('ALTER TABLE tasks ADD COLUMN is_active BOOLEAN NOT NULL DEFAULT TRUE');
        $pdo->exec('UPDATE tasks SET is_active = TRUE WHERE is_active IS NULL');
      } else {
        $pdo->exec('ALTER TABLE tasks ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1');
        $pdo->exec('UPDATE tasks SET is_active = 1 WHERE is_active IS NULL');
      }
    } catch (Throwable $inner) {
      // Column exists but SELECT failed earlier; ignore so page remains usable.
    }
  }
}

function get_task_key_map(PDO $pdo): array {
  $map = [];
  try {
    $stmt = $pdo->query('SELECT id, area_id, title FROM tasks');
    foreach ($stmt as $row) {
      $key = $row['area_id'] . '|' . normalize_search_value($row['title']);
      $map[$key] = (int)$row['id'];
    }
  } catch (Throwable $e) {
    // ignore
  }
  return $map;
}

function store_solution_upload(string $field, ?string $currentPath = null, bool $remove = false): ?string {
  $file = $_FILES[$field] ?? null;
  $hasUpload = $file && $file['error'] !== UPLOAD_ERR_NO_FILE;
  $projectRoot = dirname(__DIR__);

  if ($hasUpload) {
    if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
      throw new RuntimeException('Upload fehlgeschlagen (ungültige Datei oder Abbruch).');
    }

    $targetDir = $projectRoot . '/uploads/solutions';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
      throw new RuntimeException('Upload-Verzeichnis konnte nicht erstellt werden.');
    }

    $sanitized = preg_replace('/[^A-Za-z0-9._-]/', '_', $file['name']);
    $extension = pathinfo($sanitized, PATHINFO_EXTENSION);
    $filename = uniqid('solution_', true) . ($extension ? '.' . $extension : '');
    $targetPath = $targetDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
      throw new RuntimeException('Die hochgeladene Datei konnte nicht gespeichert werden.');
    }

    if ($currentPath) {
      $existing = $projectRoot . '/' . ltrim($currentPath, '/');
      if (is_file($existing)) {
        @unlink($existing);
      }
    }

    return 'uploads/solutions/' . $filename;
  }

  if ($remove && $currentPath) {
    $existing = $projectRoot . '/' . ltrim($currentPath, '/');
    if (is_file($existing)) {
      @unlink($existing);
    }
    return null;
  }

  return $currentPath;
}

ensure_task_is_active_column($pdo);
$areas = get_areas($pdo);
$areaNameMap = [];
foreach ($areas as $a) {
  $areaNameMap[$a['id']] = $a['name'];
}

if ($action === 'create') {
  $area_id = (int)($_POST['area_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = $_POST['description'] ?? '';
  $hint = $_POST['hint'] ?? '';
  $is_active = !empty($_POST['is_active']) ? 1 : 0;
  $solution_file = null;

  if ($area_id<=0 || $title==='') {
    $err='Area und Titel sind Pflicht.';
  } else {
    try {
      $solution_file = store_solution_upload('solution_upload', null, false);
    } catch (RuntimeException $e) {
      $err = $e->getMessage();
    }
  }

  if (!$err) {
    $st = $pdo->prepare('INSERT INTO tasks(area_id,title,description,hint,solution_file,is_active) VALUES (?,?,?,?,?,?)');
    try {
      $st->execute([$area_id,$title,$description,$hint,$solution_file,$is_active]);
      $msg='Aufgabe erstellt.';
      if(function_exists('audit_log')) audit_log('create','task',$pdo->lastInsertId(),$title);
    }
    catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}
elseif ($action==='update') {
  $id = (int)($_POST['id'] ?? 0);
  $area_id = (int)($_POST['area_id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $description = $_POST['description'] ?? '';
  $hint = $_POST['hint'] ?? '';
  $is_active = !empty($_POST['is_active']) ? 1 : 0;
  $existing_solution = $_POST['existing_solution'] ?? null;
  $remove_solution = !empty($_POST['remove_solution']);

  if ($id<=0 || $area_id<=0 || $title==='') { $err='ID, Area und Titel sind Pflicht.'; }
  else {
    try {
      $solution_file = store_solution_upload('solution_upload', $existing_solution, $remove_solution);
    } catch (RuntimeException $e) {
      $err = $e->getMessage();
    }

    if (!$err) {
      $st = $pdo->prepare('UPDATE tasks SET area_id=?, title=?, description=?, hint=?, solution_file=?, is_active=? WHERE id=?');
      try {
        $st->execute([$area_id,$title,$description,$hint,$solution_file,$is_active,$id]);
        $msg='Aufgabe aktualisiert.';
        if(function_exists('audit_log')) audit_log('update','task',$id,$title);
      }
      catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
    }
  }
}
elseif ($action==='delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id>0) {
    try {
      $pdo->prepare('DELETE FROM tasks WHERE id=?')->execute([$id]);
      $msg='Aufgabe gelöscht.';
      if(function_exists('audit_log')) audit_log('delete','task',$id,'delete');
    }
    catch(Throwable $e){ $err='Fehler: '.$e->getMessage(); }
  }
}
elseif ($action==='toggle') {
  $id = (int)($_POST['id'] ?? 0);
  $current = isset($_POST['current']) && (int)$_POST['current'] === 1 ? 1 : 0;
  if ($id>0) {
    $newStatus = $current ? 0 : 1;
    try {
      $pdo->prepare('UPDATE tasks SET is_active=? WHERE id=?')->execute([$newStatus,$id]);
      $msg = $newStatus ? 'Aufgabe aktiviert.' : 'Aufgabe deaktiviert.';
      if(function_exists('audit_log')) audit_log('update','task',$id,$newStatus ? 'activate' : 'deactivate');
    } catch (Throwable $e) {
      $err = 'Status konnte nicht aktualisiert werden: '.$e->getMessage();
    }
  }
}
elseif ($action==='import_preview') {
  if (empty($_FILES['tasks_csv']['tmp_name'])) {
    $err = 'Bitte wähle eine CSV-Datei aus.';
  } else {
    $fh = fopen($_FILES['tasks_csv']['tmp_name'], 'r');
    if (!$fh) {
      $err = 'CSV konnte nicht gelesen werden.';
    } else {
      $header = fgetcsv($fh);
      if ($header && $header[0] !== null) $header[0] = csv_trim_bom($header[0]);
      $normalized = array_map(fn($c) => normalize_search_value(trim($c ?? '')), $header ?? []);
      $idx = [
        'area_id' => array_search('area_id', $normalized, true),
        'title' => array_search('title', $normalized, true),
        'description' => array_search('description', $normalized, true),
        'hint' => array_search('hint', $normalized, true),
        'solution_file' => array_search('solution_file', $normalized, true),
        'is_active' => array_search('is_active', $normalized, true),
      ];
      if ($idx['area_id'] === false || $idx['title'] === false) {
        $err = 'CSV benötigt mindestens die Spalten area_id und title.';
      } else {
        $existingIndex = get_task_key_map($pdo);
        $seenKeys = [];
        $rows = [];
        $payload = [];
        $line = 1;
        $newCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;
        while (($row = fgetcsv($fh)) !== false) {
          $line++;
          $areaId = isset($row[$idx['area_id']]) ? (int)trim($row[$idx['area_id']]) : 0;
          $title = isset($row[$idx['title']]) ? trim($row[$idx['title']]) : '';
          $description = $idx['description'] !== false && isset($row[$idx['description']]) ? $row[$idx['description']] : '';
          $hint = $idx['hint'] !== false && isset($row[$idx['hint']]) ? $row[$idx['hint']] : '';
          $solution = $idx['solution_file'] !== false && isset($row[$idx['solution_file']]) ? trim($row[$idx['solution_file']]) : '';
          $rawActive = $idx['is_active'] !== false && isset($row[$idx['is_active']]) ? trim($row[$idx['is_active']]) : '';
          $normalizedActive = normalize_search_value($rawActive);
          $isActive = ($normalizedActive === '' || !in_array($normalizedActive, ['0','false','nein','no','inactive'], true)) ? 1 : 0;

          $notes = [];
          $status = 'new';
          if ($areaId <= 0 || !isset($areaNameMap[$areaId])) {
            $status = 'error';
            $notes[] = 'Unbekannte Area';
          }
          if ($title === '') {
            $status = 'error';
            $notes[] = 'Titel fehlt';
          }
          $key = $areaId . '|' . normalize_search_value($title);
          if ($status !== 'error') {
            if (isset($existingIndex[$key])) {
              $status = 'duplicate';
              $notes[] = 'Existiert bereits (ID ' . $existingIndex[$key] . ')';
            }
            if (isset($seenKeys[$key])) {
              $status = 'duplicate';
              $notes[] = 'Duplikat in CSV (Zeile ' . $seenKeys[$key] . ')';
            } else {
              $seenKeys[$key] = $line;
            }
          }

          if ($status === 'new') $newCount++;
          if ($status === 'duplicate') $duplicateCount++;
          if ($status === 'error') $errorCount++;

          $rows[] = [
            'line' => $line,
            'area_id' => $areaId,
            'area_name' => $areaNameMap[$areaId] ?? '–',
            'title' => $title,
            'description' => $description,
            'hint' => $hint,
            'solution_file' => $solution,
            'is_active' => $isActive,
            'status' => $status,
            'notes' => $notes ? implode(' | ', $notes) : '–',
          ];

          $payload[] = [
            'area_id' => $areaId,
            'title' => $title,
            'description' => $description,
            'hint' => $hint,
            'solution_file' => $solution,
            'is_active' => $isActive,
            'skip' => $status !== 'new',
          ];
        }
        $encoded = base64_encode(json_encode($payload, JSON_UNESCAPED_UNICODE));
        if ($encoded === false) {
          $err = 'Import konnte nicht vorbereitet werden.';
        } else {
          $importPreview = [
            'rows' => $rows,
            'payload' => $encoded,
            'summary' => [
              'new' => $newCount,
              'duplicates' => $duplicateCount,
              'errors' => $errorCount,
            ],
          ];
          if ($errorCount > 0) {
            $errType = 'warning';
            $err = 'Einige Zeilen enthalten Fehler und werden übersprungen.';
          }
        }
      }
      fclose($fh);
    }
  }
}
elseif ($action==='import_confirm') {
  $payload = $_POST['payload'] ?? '';
  if ($payload === '') {
    $err = 'Keine Importdaten übermittelt.';
  } else {
    $decoded = json_decode(base64_decode($payload, true) ?: '', true);
    if (!is_array($decoded)) {
      $err = 'Importdaten konnten nicht gelesen werden.';
    } else {
      $existingIndex = get_task_key_map($pdo);
      $inserted = 0;
      $skipped = 0;
      $stmt = $pdo->prepare('INSERT INTO tasks(area_id,title,description,hint,solution_file,is_active) VALUES (?,?,?,?,?,?)');
      foreach ($decoded as $row) {
        $areaId = (int)($row['area_id'] ?? 0);
        $title = trim($row['title'] ?? '');
        if (!empty($row['skip']) || $areaId <= 0 || $title === '') {
          $skipped++;
          continue;
        }
        $key = $areaId . '|' . normalize_search_value($title);
        if (isset($existingIndex[$key])) {
          $skipped++;
          continue;
        }
        $description = $row['description'] ?? '';
        $hint = $row['hint'] ?? '';
        $solution = trim($row['solution_file'] ?? '');
        $solution = $solution !== '' ? $solution : null;
        $isActive = !empty($row['is_active']) ? 1 : 0;
        try {
          $stmt->execute([$areaId,$title,$description,$hint,$solution,$isActive]);
          $existingIndex[$key] = true;
          $inserted++;
        } catch (Throwable $e) {
          $err = 'Importfehler: '.$e->getMessage();
          break;
        }
      }
      if (!$err) {
        $msg = 'Import abgeschlossen: '.$inserted.' neue Aufgaben, '.$skipped.' übersprungen.';
      }
    }
  }
}

$filterArea = isset($_GET['area']) ? (int)$_GET['area'] : 0;
$search = trim($_GET['q'] ?? '');
$params = [];
$where = [];
if ($filterArea > 0) {
  $where[] = 't.area_id = ?';
  $params[] = $filterArea;
}
if ($search !== '') {
  $needle = '%' . normalize_search_value($search) . '%';
  $where[] = '(LOWER(t.title) LIKE ? OR LOWER(a.name) LIKE ?)';
  $params[] = $needle;
  $params[] = $needle;
}

$sql = 'SELECT t.*, a.name AS area_name FROM tasks t LEFT JOIN areas a ON a.id=t.area_id';
if ($where) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY t.id DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h1 class="h4 mb-0">Aufgaben</h1>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#csvImport" aria-expanded="<?= $importPreview ? 'true' : 'false' ?>" aria-controls="csvImport">⬆️ CSV-Import</button>
    <button class="btn btn-violet" data-bs-toggle="modal" data-bs-target="#modalCreate">Neue Aufgabe</button>
  </div>
</div>

<form class="card p-3 mb-3" method="get">
  <div class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Bereich</label>
      <select class="form-select" name="area">
        <option value="">Alle Bereiche</option>
        <?php foreach($areas as $a): ?>
          <option value="<?=$a['id']?>" <?= (int)$a['id'] === $filterArea ? 'selected' : '' ?>><?=htmlspecialchars($a['name'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">🔍 Suche nach Titel oder Bereich</label>
      <input class="form-control" name="q" value="<?=htmlspecialchars($search)?>" placeholder="z. B. Fibonacci">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-violet flex-grow-1">Filtern</button>
      <a class="btn btn-outline-light" href="tasks.php">Zurücksetzen</a>
    </div>
  </div>
</form>

<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if($err):?><div class="alert alert-<?=$errType?>"><?=$err?></div><?php endif; ?>

<div class="card p-0">
  <div class="table-responsive">
    <table class="table table-dark table-hover align-middle mb-0">
      <thead>
        <tr><th>ID</th><th>Area</th><th>Titel</th><th>Status</th><th>Hinweis</th><th>Lösung</th><th style="width:260px"></th></tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['area_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <td>
            <?php if(!empty($r['is_active'])): ?>
              <span class="badge text-bg-success">aktiv</span>
            <?php else: ?>
              <span class="badge text-bg-secondary">inaktiv</span>
            <?php endif; ?>
          </td>
          <td class="text-truncate" style="max-width:260px"><?= htmlspecialchars($r['hint']) ?></td>
          <td>
            <?php if(!empty($r['solution_file'])): ?>
              <a class="link-light" href="/portfolio_with_backend/<?= htmlspecialchars(ltrim($r['solution_file'],'/')) ?>" target="_blank">Download</a>
            <?php else: ?>
              <span class="text-secondary">–</span>
            <?php endif; ?>
          </td>
          <td class="d-flex flex-wrap gap-2">
            <form method="post" class="d-inline">
              <?php csrf_field(); ?>
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= $r['id'] ?>">
              <input type="hidden" name="current" value="<?= !empty($r['is_active']) ? 1 : 0 ?>">
              <button class="btn btn-sm <?= !empty($r['is_active']) ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                <?= !empty($r['is_active']) ? 'Deaktivieren' : 'Aktivieren' ?>
              </button>
            </form>
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
              data-active="<?= !empty($r['is_active']) ? 1 : 0 ?>"
            >Bearbeiten</button>

            <form method="post" onsubmit="return confirm('Wirklich löschen?')" class="d-inline">
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

<div class="collapse mt-4 <?= $importPreview ? 'show' : '' ?>" id="csvImport">
  <div class="card p-3">
    <h5 class="mb-3">Tasks per CSV importieren</h5>
    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
      <?php csrf_field(); ?>
      <input type="hidden" name="action" value="import_preview">
      <div class="col-md-6">
        <label class="form-label">CSV-Datei</label>
        <input type="file" name="tasks_csv" class="form-control" accept=".csv" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">&nbsp;</label>
        <button class="btn btn-outline-light w-100">Vorschau erstellen</button>
      </div>
      <div class="col-md-3">
        <small class="text-secondary d-block">Erwartet Spalten: area_id, title, description, hint, solution_file, is_active.</small>
      </div>
    </form>

    <?php if ($importPreview): ?>
      <hr>
      <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
        <strong>Vorschau:</strong>
        <span class="badge text-bg-success">Neue: <?=$importPreview['summary']['new']?></span>
        <span class="badge text-bg-warning">Duplikate: <?=$importPreview['summary']['duplicates']?></span>
        <span class="badge text-bg-danger">Fehler: <?=$importPreview['summary']['errors']?></span>
      </div>
      <div class="table-responsive mb-3" style="max-height:400px;">
        <table class="table table-dark table-striped table-sm align-middle">
          <thead>
            <tr>
              <th>Zeile</th>
              <th>Area</th>
              <th>Titel</th>
              <th>Status</th>
              <th>Notizen</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($importPreview['rows'] as $row): ?>
              <?php
                $statusClass = 'text-bg-secondary';
                if ($row['status'] === 'new') $statusClass = 'text-bg-success';
                elseif ($row['status'] === 'duplicate') $statusClass = 'text-bg-warning';
                elseif ($row['status'] === 'error') $statusClass = 'text-bg-danger';
              ?>
              <tr>
                <td><?=$row['line']?></td>
                <td><?=htmlspecialchars($row['area_id'] . ' · ' . $row['area_name'])?></td>
                <td><?=htmlspecialchars($row['title'])?></td>
                <td><span class="badge <?=$statusClass?>"><?=htmlspecialchars($row['status'])?></span></td>
                <td><?=htmlspecialchars($row['notes'])?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <form method="post" class="d-flex gap-2">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="import_confirm">
        <input type="hidden" name="payload" value="<?= htmlspecialchars($importPreview['payload'], ENT_QUOTES) ?>">
        <button class="btn btn-violet" <?= $importPreview['summary']['new'] > 0 ? '' : 'disabled' ?>>Import starten</button>
        <a class="btn btn-outline-light" href="tasks.php">Abbrechen</a>
      </form>
    <?php endif; ?>
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
      <form method="post" enctype="multipart/form-data">
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
              <input class="form-control" type="file" name="solution_upload">
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-3 mt-md-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="create-active" checked>
                <label class="form-check-label" for="create-active">Aufgabe aktiv</label>
              </div>
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
      <form method="post" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="existing_solution" id="edit-existing-solution">
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
              <label class="form-label">Lösungsdatei ersetzen</label>
              <input class="form-control" type="file" name="solution_upload">
              <div class="form-text" id="edit-solution-info">Keine Datei hochgeladen.</div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="remove_solution" id="edit-remove-solution">
                <label class="form-check-label" for="edit-remove-solution">Aktuelle Datei entfernen</label>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check mt-3 mt-md-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="edit-active">
                <label class="form-check-label" for="edit-active">Aufgabe aktiv</label>
              </div>
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
  const isActive = btn.getAttribute('data-active') === '1';
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-area').value = area;
  document.getElementById('edit-title').value = title;
  document.getElementById('edit-description').value = description;
  document.getElementById('edit-hint').value = hint;
  document.getElementById('edit-existing-solution').value = solution;
  document.getElementById('edit-active').checked = isActive;
  const info = document.getElementById('edit-solution-info');
  const remove = document.getElementById('edit-remove-solution');
  remove.checked = false;
  if (solution) {
    info.innerHTML = `<a class="link-light" href="/portfolio_with_backend/${solution.startsWith('/') ? solution.substring(1) : solution}" target="_blank">Aktuelle Datei ansehen</a>`;
  } else {
    info.textContent = 'Keine Datei hochgeladen.';
  }
});
</script>

<?php include __DIR__ . '/inc/footer.php'; ?>
