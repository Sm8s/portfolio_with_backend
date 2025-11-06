<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();

$tables = ['users','areas','tasks','progress','pages'];
$now = date('Ymd_His');
$fname = "c_portfolio_backup_{$now}.sql";

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$fname.'"');

echo "-- c_portfolio backup generated at {$now}\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n";
echo "USE c_portfolio;\n\n";

foreach ($tables as $t) {
  // Schema
  $res = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
  if (!empty($res['Create Table'])) {
    echo "--\n-- Table structure for table `$t`\n--\n";
    echo "DROP TABLE IF EXISTS `$t`;\n";
    echo $res['Create Table'].";\n\n";
  }
  // Data
  $rows = $pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
  if ($rows) {
    echo "--\n-- Dumping data for table `$t`\n--\n";
    foreach ($rows as $r) {
      $cols = array_map(fn($c)=>"`$c`", array_keys($r));
      $vals = array_map(function($v) use ($pdo){
        if ($v === null) return "NULL";
        return $pdo->quote($v);
      }, array_values($r));
      echo "INSERT INTO `$t` (".implode(',', $cols).") VALUES (".implode(',', $vals).");\n";
    }
    echo "\n";
  }
}
echo "SET FOREIGN_KEY_CHECKS=1;\n";
exit;