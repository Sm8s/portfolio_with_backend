<?php
// inc/audit.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

function audit_log($action, $entity, $entity_id = null, $detail = null) {
  global $pdo;
  $uid = $_SESSION['uid'] ?? null;
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  $st = $pdo->prepare('INSERT INTO audit_log(user_id, action, entity, entity_id, detail, ip) VALUES (?,?,?,?,?,?)');
  $st->execute([$uid, $action, $entity, $entity_id, $detail, $ip]);
}