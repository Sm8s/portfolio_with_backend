<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();
header('Content-Type: application/json; charset=utf-8');
if (empty($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) { echo json_encode(['uploaded'=>0,'error'=>['message'=>'No file']]); exit; }
$dir = __DIR__ . '/../uploads'; @mkdir($dir, 0777, true);
$orig = basename($_FILES['upload']['name']); $safe = preg_replace('~[^A-Za-z0-9._-]+~','_', $orig);
$ext = strtolower(pathinfo($safe, PATHINFO_EXTENSION)); $allowed = ['png','jpg','jpeg','gif','webp','svg'];
if (!in_array($ext, $allowed)) { echo json_encode(['uploaded'=>0,'error'=>['message'=>'Unsupported type']]); exit; }
$target = $dir.'/'.time().'_'.$safe;
if (!move_uploaded_file($_FILES['upload']['tmp_name'], $target)) { echo json_encode(['uploaded'=>0,'error'=>['message'=>'Upload failed']]); exit; }
$url = '/portfolio_with_backend/uploads/'.basename($target);
echo json_encode(['uploaded'=>1,'fileName'=>basename($target),'url'=>$url]);
