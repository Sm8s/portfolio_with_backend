<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php';
csrf_check();

$dir = __DIR__ . '/../uploads';
@mkdir($dir, 0777, true);
$msg='';

if (($_POST['action'] ?? '') === 'delete') {
  $file = basename($_POST['file'] ?? '');
  $path = $dir . '/' . $file;
  if (is_file($path)) { @unlink($path); $msg='Datei gelöscht.'; }
}

$files = array_values(array_filter(scandir($dir), function($f){ return $f[0]!=='.'; }));

include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Mediathek</h1>
  <a class="btn btn-outline-light" href="/portfolio_with_backend/ uploads" target="_blank">Ordner öffnen</a>
</div>
<?php if($msg):?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="card p-0">
<table class="table table-dark table-hover mb-0">
  <thead><tr><th>Datei</th><th>Größe</th><th>Aktion</th></tr></thead>
  <tbody>
    <?php foreach($files as $f): $p=$dir.'/'.$f; ?>
      <tr>
        <td><a href="/portfolio_with_backend/uploads/<?=$f?>" target="_blank"><?=$f?></a></td>
        <td><?= number_format(filesize($p)/1024,1) ?> KB</td>
        <td>
          <form method="post" onsubmit="return confirm('Datei wirklich löschen?')" class="d-inline">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="file" value="<?=$f?>">
            <button class="btn btn-sm btn-outline-danger">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php include __DIR__ . '/inc/footer.php'; ?>