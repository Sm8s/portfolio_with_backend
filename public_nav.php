<?php
// public_nav.php — simple navbar that lists pages (deleted_at is ignored)
require_once __DIR__ . '/config.php';
$pages = $pdo->query('SELECT slug, title FROM pages WHERE deleted_at IS NULL ORDER BY updated_at DESC')->fetchAll();
$menus = [];
try { $menus = $pdo->query('SELECT label, url FROM menus ORDER BY position ASC, id ASC')->fetchAll(); } catch(Throwable $e) { $menus = []; }
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="/portfolio_with_backend/">Mein Portfolio</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
        <?php foreach($menus as $m): ?>
          <li class="nav-item"><a class="nav-link" href="<?=htmlspecialchars($m['url'])?>"><?=htmlspecialchars($m['label'])?></a></li>
        <?php endforeach; ?>
        <?php foreach($pages as $p): ?>
          <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/page.php?slug=<?=urlencode($p['slug'])?>"><?=htmlspecialchars($p['title'])?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>