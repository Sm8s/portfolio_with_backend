<?php
// inc/security.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Session Timeout (30 min)
$SESSION_TIMEOUT = 60*30;
if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $SESSION_TIMEOUT)) {
  session_unset(); session_destroy(); session_start();
}
$_SESSION['last_activity'] = time();

// Simple Rate Limit for login: 5 attempts per 10 minutes per IP
function login_rate_check() {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $key = 'login_attempts_'.$ip;
  if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count'=>0,'ts'=>time()];
  $win = 600; // 10 min
  if (time() - $_SESSION[$key]['ts'] > $win) { $_SESSION[$key] = ['count'=>0,'ts'=>time()]; }
  if ($_SESSION[$key]['count'] >= 5) return false;
  return true;
}
function login_rate_bump($ok) {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $key = 'login_attempts_'.$ip;
  if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count'=>0,'ts'=>time()];
  if ($ok) { $_SESSION[$key] = ['count'=>0,'ts'=>time()]; }
  else { $_SESSION[$key]['count'] += 1; }
}

// Remember-me cookie (7 days) — stores a signed token to auto-login
function remember_me_set($user_id) {
  $secret = hash('sha256', __FILE__); // project-level secret-ish
  $token = base64_encode($user_id . ':' . hash('sha256', $user_id.$secret));
  setcookie('remember_me', $token, [
    'expires'=> time()+60*60*24*7,
    'path'=>'/',
    'httponly'=>true,
    'samesite'=>'Lax',
  ]);
}
function remember_me_clear() {
  setcookie('remember_me', '', time()-3600, '/');
}
function remember_me_try(&$pdo) {
  if (!empty($_SESSION['uid'])) return;
  if (empty($_COOKIE['remember_me'])) return;
  $secret = hash('sha256', __FILE__);
  $decoded = base64_decode($_COOKIE['remember_me']);
  if (!$decoded || strpos($decoded, ':')===false) return;
  list($uid, $sig) = explode(':', $decoded, 2);
  $check = hash('sha256', $uid.$secret);
  if (hash_equals($check, $sig)) {
    $st = $pdo->prepare('SELECT id, username, is_admin FROM users WHERE id=?');
    $st->execute([(int)$uid]);
    $u = $st->fetch();
    if ($u) {
      $_SESSION['uid']=$u['id']; $_SESSION['username']=$u['username']; $_SESSION['is_admin']=(int)$u['is_admin']===1;
    }
  }
}