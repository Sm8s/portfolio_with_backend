<?php
// /admin/api_translate.php — PHP-Proxy zu LibreTranslate-kompatibler API (POST)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/inc/translate_cfg.php';
require_once __DIR__ . '/inc/csrf.php'; csrf_check();

header('Content-Type: application/json; charset=utf-8');

$text = $_POST['q'] ?? '';
$source = $_POST['source'] ?? 'auto';
$target = $_POST['target'] ?? 'en';

if ($text === '' || $target === '') {
  http_response_code(422);
  echo json_encode(['ok'=>false,'error'=>'Missing text/target']); exit;
}

$url = rtrim($TRANSLATE_API_ENDPOINT,'/').'/translate';
$data = [
  'q' => $text,
  'source' => $source,
  'target' => $target,
  'format' => 'html',
];
if ($TRANSLATE_API_KEY !== '') { $data['api_key'] = $TRANSLATE_API_KEY; }

$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => http_build_query($data),
  CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
  CURLOPT_TIMEOUT => 20,
]);
$resp = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($resp === false || $code >= 400) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$err ?: ('HTTP '.$code)]); exit;
}
echo $resp;