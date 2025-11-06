<?php
// /admin/check.php — Diagnose: DB, Tabellen, Admin-User
header('Content-Type: text/html; charset=utf-8');
echo "<style>body{font-family:system-ui;background:#0b0b12;color:#ddd;padding:20px} .ok{color:#7cdd8c} .bad{color:#ff6b6b} table{border-collapse:collapse;margin-top:10px} td,th{border:1px solid #333;padding:6px 10px} code{background:#181825;padding:2px 6px;border-radius:6px}</style>";
echo "<h1>Admin · DB-Check</h1>";
require_once __DIR__ . '/../config.php';

function row($k,$v){ echo "<tr><th align='left'>$k</th><td>$v</td></tr>"; }

echo "<h2>System</h2><table>";
row('PHP Version', phpversion());
row('PDO vorhanden', class_exists('PDO') ? "<span class='ok'>ja</span>" : "<span class='bad'>nein</span>");
row('DSN', htmlspecialchars($dsn ?? 'mysql:*'));
echo "</table>";

echo "<h2>Datenbank</h2>";
try {
  // Verbindung kommt aus config.php ($pdo)
  $pdo->query('SELECT 1');
  echo "<p class='ok'>✔ Verbindung erfolgreich.</p>";
} catch (Throwable $e) {
  echo "<p class='bad'>✖ Verbindung fehlgeschlagen: ".htmlspecialchars($e->getMessage())."</p>";
  exit;
}

// Tabellen prüfen
$tables = ['users','areas','tasks','progress','pages'];
echo "<table><tr><th>Tabelle</th><th>Status</th><th>Count</th></tr>";
foreach ($tables as $t) {
  try {
    $c = $pdo->query("SELECT COUNT(*) AS c FROM `$t`")->fetchColumn();
    echo "<tr><td>$t</td><td class='ok'>vorhanden</td><td>$c</td></tr>";
  } catch (Throwable $e) {
    echo "<tr><td>$t</td><td class='bad'>fehlt</td><td>-</td></tr>";
  }
}
echo "</table>";

// Admin-User auflisten
echo "<h2>Admins</h2>";
try {
  $rows = $pdo->query("SELECT id, username, is_admin FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
  if (!$rows) { echo "<p class='bad'>Keine Benutzer in users.</p>"; }
  else {
    echo "<table><tr><th>ID</th><th>Username</th><th>Admin</th></tr>";
    foreach ($rows as $r) {
      $a = !empty($r['is_admin']) ? 'ja' : 'nein';
      $cls = !empty($r['is_admin']) ? 'ok' : 'bad';
      echo "<tr><td>{$r['id']}</td><td>".htmlspecialchars($r['username'])."</td><td class='$cls'>$a</td></tr>";
    }
    echo "</table>";
  }
} catch (Throwable $e) {
  echo "<p class='bad'>Fehler beim Auslesen der users-Tabelle: ".htmlspecialchars($e->getMessage())."</p>";
}

echo "<p>Wenn <code>is_admin</code> oder <code>pages</code> fehlt: bitte die SQL-Skripte importieren (siehe ZIP unten).</p>";
?>