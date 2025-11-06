<?php
// inc/csrf.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['_csrf'])) {
  $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}
function csrf_token() { return $_SESSION['_csrf'] ?? ''; }
function csrf_field() { $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); echo "<input type='hidden' name='_csrf' value='{$t}'>"; }
function csrf_check() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = isset($_POST['_csrf']) && hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf']);
    if (!$ok) {
      http_response_code(419);
      die('<h1 style="color:#fff">CSRF verification failed</h1><p style="color:#aaa">Bitte Seite neu laden und erneut senden.</p>');
    }
  }
}