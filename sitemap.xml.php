<?php
header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/config.php';
$base = (isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'] : 'http://localhost') . '/portfolio_with_backend';
$pages = $pdo->query('SELECT slug, updated_at FROM pages WHERE deleted_at IS NULL ORDER BY updated_at DESC')->fetchAll(PDO::FETCH_ASSOC);
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc><?=$base?>/</loc></url>
  <?php foreach($pages as $p): ?>
  <url>
    <loc><?=$base?>/page/<?=urlencode($p['slug'])?></loc>
    <lastmod><?=substr($p['updated_at'],0,10)?></lastmod>
  </url>
  <?php endforeach; ?>
</urlset>