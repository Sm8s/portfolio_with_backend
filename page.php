<?php
require_once __DIR__ . '/config.php';
session_start();
$slug = $_GET['slug'] ?? '';
$st = $pdo->prepare('SELECT * FROM pages WHERE slug=? AND (deleted_at IS NULL)');
$st->execute([$slug]);
$page = $st->fetch();
if (!$page) { http_response_code(404); $title='Seite nicht gefunden'; $content='<p>Diese Seite existiert nicht.</p>'; }
else { $title=$page['title']; $content=$page['content']; }
?><!DOCTYPE html>
<html lang="de" data-bs-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title) ?> · C-Portfolio</title>
  <link rel="stylesheet" href="/portfolio_with_backend/style.css">
  <?php if (file_exists(__DIR__ . '/assets/site.css')): ?><link rel="stylesheet" href="/portfolio_with_backend/assets/site.css"><?php endif; ?>
  <style>
    body{margin:0;background:#0b0b12;color:#e6e6f0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial}
    .container{max-width:1100px;margin:0 auto;padding:24px}
    .top-nav{display:flex;gap:18px;align-items:center;padding:10px 16px;background:#0e0e16;border-bottom:1px solid rgba(255,255,255,.09)}
    .top-nav a{color:#9b6dff;text-decoration:none} .top-nav a:hover{color:#b48cff}
    .hero{padding:56px 0 16px;margin-bottom:8px;text-align:center;background:radial-gradient(1200px 400px at 50% -50%, rgba(124,77,255,.22), transparent)}
    .hero h1{margin:0 0 8px;font-size:44px;font-weight:800}
    .card{background:#0f0f1a;border:1px solid rgba(255,255,255,.09);border-radius:14px;padding:18px}
  </style>
</head>
<body>
<?php if (file_exists(__DIR__ . '/public_nav_dark.php')) { include __DIR__ . '/public_nav_dark.php'; } else { ?>
  <nav class="top-nav">
    <span>Angemeldet als <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Gast' ?></span>
    <a href="/portfolio_with_backend/areas.php">Alle Bereiche</a>
    <?php if (file_exists(__DIR__ . '/nav_pages_auto.php')) include __DIR__ . '/nav_pages_auto.php'; ?>
    <?php if (!empty($_SESSION['uid'])): ?><a href="/portfolio_with_backend/logout.php">Logout</a><?php else: ?><a href="/portfolio_with_backend/index.php">Login</a><?php endif; ?>
  </nav>
<?php } ?>
<header class="hero">
  <div class="container">
    <h1><?= htmlspecialchars($title) ?></h1>
    <?php if (!empty($page) && isset($page['updated_at'])): ?><p style="opacity:.7">Aktualisiert: <?= htmlspecialchars($page['updated_at']) ?></p><?php endif; ?>
  </div>
</header>
<main class="container">
  <div class="card"><?= $content ?></div>
</main>
<footer class="container" style="opacity:.7;padding:24px 0 48px"><small>© <?= date('Y') ?> C‑Portfolio</small></footer>
</body>
</html>