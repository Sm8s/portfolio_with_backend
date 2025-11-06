<?php
require_once __DIR__ . '/config.php';
try {
  $rows = $pdo->query("SELECT slug, title FROM pages WHERE deleted_at IS NULL ORDER BY updated_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) { $rows = []; }
foreach ($rows as $p) {
  $href = "/portfolio_with_backend/page.php?slug=" . urlencode($p['slug']);
  echo '<li><a href="'.$href.'">'.htmlspecialchars($p['title']).'</a></li>';
}