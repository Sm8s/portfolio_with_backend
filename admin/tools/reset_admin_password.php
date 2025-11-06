<?php
// /admin/tools/reset_admin_password.php
// Force reset admin password to a provided value (default: admin123).
// SECURITY: delete this file after use.
require_once __DIR__ . '/../../config.php';
header('Content-Type: text/plain; charset=utf-8');

$new = $_GET['p'] ?? 'admin123';
$username = $_GET['u'] ?? 'admin';
$hash = password_hash($new, PASSWORD_BCRYPT);

$st = $pdo->prepare('UPDATE users SET password=?, is_admin=1 WHERE username=?');
$st->execute([$hash, $username]);

echo "Password for '{$username}' set. Try login with: {$new}\n";
echo "Hash len: ".strlen($hash)."\n";