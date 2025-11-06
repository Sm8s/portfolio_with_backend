<?php
// /admin/tools/check_login_test.php
// Tests password_verify() with current DB hash for user 'admin'
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../../config.php';

$username = $_GET['u'] ?? 'admin';
$pass = $_GET['p'] ?? 'admin123';

$stmt = $pdo->prepare('SELECT id, username, password, is_admin FROM users WHERE username=?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
  echo "User not found: $username\n";
  exit;
}

$hash = $user['password'];
echo "User: {$user['username']} (id {$user['id']}), is_admin={$user['is_admin']}\n";
echo "Hash length: ".strlen($hash)."\n";
echo "Hash: $hash\n";

$ok = password_verify($pass, $hash);
echo "password_verify('{$pass}', hash) => ".($ok ? "TRUE" : "FALSE")."\n";

// Also try rehash info
if (password_needs_rehash($hash, PASSWORD_BCRYPT)) {
  echo "Note: hash needs rehash.\n";
}