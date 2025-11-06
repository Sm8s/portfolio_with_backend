<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();
require_once __DIR__ . '/inc/translate_cfg.php';

header('Content-Type: application/json; charset=utf-8');
$page_id = (int)($_GET['page_id'] ?? 0);
$target = $_GET['target'] ?? 'en';
if ($page_id<=0) { echo json_encode(['ok'=>false,'error'=>'page_id fehlt']); exit; }

$st = $pdo->prepare('SELECT * FROM pages WHERE id=?');
$st->execute([$page_id]);
$p = $st->fetch();
if (!$p) { echo json_encode(['ok'=>false,'error'=>'Seite nicht gefunden']); exit; }

// translate title and content
function lt($q, $source, $target, $endpoint, $key){
  $url = rtrim($endpoint,'/').'/translate';
  $data = ['q'=>$q, 'source'=>$source, 'target'=>$target, 'format'=>'html'];
  if ($key!=='') $data['api_key'] = $key;
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT => 30,
  ]);
  $resp = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  if ($resp === false || $code>=400) return [false, $err ?: ('HTTP '.$code)];
  $json = json_decode($resp, true);
  return [true, $json['translatedText'] ?? ''];
}
list($okT, $titleT) = lt($p['title'], 'auto', $target, $TRANSLATE_API_ENDPOINT, $TRANSLATE_API_KEY);
list($okC, $contentT) = lt($p['content'], 'auto', $target, $TRANSLATE_API_ENDPOINT, $TRANSLATE_API_KEY);
if (!$okT || !$okC) { echo json_encode(['ok'=>false,'error'=>'Übersetzung fehlgeschlagen']); exit; }

$slug_new = $p['slug'].'-'.$target;
try{
  $ins = $pdo->prepare('INSERT INTO pages(slug,title,content) VALUES (?,?,?)');
  $ins->execute([$slug_new, $titleT, $contentT]);
  echo json_encode(['ok'=>true,'slug'=>$slug_new]);
} catch (Throwable $e) {
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}