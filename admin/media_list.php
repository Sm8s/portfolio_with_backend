<?php
// admin/media_list.php
require_once __DIR__ . '/inc/auth.php';
require_admin();
header('Content-Type: application/json; charset=utf-8');
$dir = __DIR__ . '/../uploads';
@mkdir($dir, 0777, true);
$files = array_values(array_filter(scandir($dir), function($f){ return $f[0]!=='.'; }));
$out = [];
foreach ($files as $f) {
  $path = $dir.'/'.$f;
  if (is_file($path)) {
    $out[] = [
      'file' => $f,
      'url' => '/portfolio_with_backend/uploads/'.$f,
      'size' => filesize($path),
    ];
  }
}
echo json_encode(['ok'=>true,'files'=>$out]);
