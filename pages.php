<?php
require_once __DIR__ . '/config.php';
$rows = $pdo->query('SELECT slug, title, updated_at FROM pages ORDER BY updated_at DESC')->fetchAll();
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Seiten</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<main class="container my-4">
<h1>Seiten</h1>
<table class="table table-striped"><thead><tr><th>Slug</th><th>Titel</th><th>Aktualisiert</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r): ?>
<tr><td><?=$r['slug']?></td><td><?=$r['title']?></td><td><?=$r['updated_at']?></td><td><a class="btn btn-sm btn-primary" href="/portfolio_with_backend/page.php?slug=<?=$r['slug']?>">Ansehen</a></td></tr>
<?php endforeach; ?>
</tbody></table>
</main></body></html>