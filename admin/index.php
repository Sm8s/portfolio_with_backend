<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
include __DIR__ . '/inc/header.php';

// Quick counts
$pages_count = $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$areas_count = $pdo->query('SELECT COUNT(*) FROM areas')->fetchColumn();
$tasks_count = $pdo->query('SELECT COUNT(*) FROM tasks')->fetchColumn();
$users_count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
?>
<div class="row g-4">
  <div class="col-md-3">
    <div class="card p-3 hover-tilt">
      <div class="d-flex justify-content-between align-items-center">
        <div><div class="text-secondary small">Seiten</div><div class="h3 mb-0"><?=$pages_count?></div></div>
        <a class="btn btn-sm btn-violet" href="pages.php">Verwalten</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 hover-tilt">
      <div class="d-flex justify-content-between align-items-center">
        <div><div class="text-secondary small">Areas</div><div class="h3 mb-0"><?=$areas_count?></div></div>
        <a class="btn btn-sm btn-violet" href="areas.php">Verwalten</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 hover-tilt">
      <div class="d-flex justify-content-between align-items-center">
        <div><div class="text-secondary small">Aufgaben</div><div class="h3 mb-0"><?=$tasks_count?></div></div>
        <a class="btn btn-sm btn-violet" href="tasks.php">Verwalten</a>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 hover-tilt">
      <div class="d-flex justify-content-between align-items-center">
        <div><div class="text-secondary small">Benutzer</div><div class="h3 mb-0"><?=$users_count?></div></div>
        <a class="btn btn-sm btn-violet" href="users.php">Verwalten</a>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>