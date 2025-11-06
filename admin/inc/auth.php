<?php
// inc/auth.php
// Start session and enforce login & admin where needed
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config.php';

function require_login() {
  if (empty($_SESSION['uid'])) {
    header('Location: /portfolio_with_backend/admin/login.php');
    exit;
  }
}
function require_admin() {
  require_login();
  if (empty($_SESSION['is_admin'])) {
    http_response_code(403);
    echo '<h1 style="color:#fff">403 Forbidden</h1><p style="color:#aaa">Admin rights required.</p>';
    exit;
  }
}
?>