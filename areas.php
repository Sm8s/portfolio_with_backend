<?php
session_start();
require 'config.php';

// Leite nicht angemeldete Benutzer auf die Startseite um
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Hole alle Bereiche aus der Datenbank
$stmt = $pdo->query('SELECT * FROM areas ORDER BY id');
$areas = $stmt->fetchAll();

// Lade den Fortschritt des Benutzers (gelöste Aufgaben)
$stmtProgress = $pdo->prepare('SELECT task_id, solved FROM progress WHERE user_id = ?');
$stmtProgress->execute([$_SESSION['user_id']]);
$progressRows = $stmtProgress->fetchAll();
$progressMap = [];
foreach ($progressRows as $p) {
    $progressMap[$p['task_id']] = $p['solved'];
}

// Sammle Fortschrittsinformationen pro Bereich
$areaStats = [];
foreach ($areas as $area) {
    // Zähle Aufgaben dieses Bereichs
    $stmtTasks = $pdo->prepare('SELECT id FROM tasks WHERE area_id = ?');
    $stmtTasks->execute([$area['id']]);
    $tasks = $stmtTasks->fetchAll();
    $taskCount = count($tasks);
    $solvedCount = 0;
    foreach ($tasks as $t) {
        if (!empty($progressMap[$t['id']])) {
            $solvedCount++;
        }
    }
    $percent = $taskCount > 0 ? floor(($solvedCount / $taskCount) * 100) : 0;
    $areaStats[$area['id']] = [
        'total' => $taskCount,
        'solved' => $solvedCount,
        'percent' => $percent
    ];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bereiche – C‑Portfolio</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .areas-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 2rem;
    }
    .area-card {
      background-color: #112240;
      border-radius: 6px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.3);
      transition: transform 0.2s;
    }
    .area-card:hover {
      transform: translateY(-2px);
    }
    .area-card h3 {
      margin-top: 0;
      color: #64ffda;
    }
    .area-card p {
      color: #8892b0;
      margin-bottom: 0.5rem;
    }
    .area-card .btn {
      display: inline-block;
      margin-top: 0.5rem;
      background-color: #64ffda;
      color: #0a192f;
      padding: 0.4rem 0.8rem;
      border-radius: 4px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .area-card .btn:hover {
      background-color: #52cca8;
    }
    .area-progress-container {
      width: 100%;
      height: 8px;
      background-color: #233554;
      border-radius: 4px;
      overflow: hidden;
      margin-top: 0.5rem;
    }
    .area-progress-bar {
      height: 100%;
      background-color: #64ffda;
      transition: width 0.4s ease;
    }
    .area-progress-text {
      font-size: 0.8rem;
      color: #8892b0;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <span>Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
  <header>
    <div class="hero-content">
      <h1>Alle Bereiche</h1>
      <p>Wähle einen Bereich aus und tauche tief in die Welt der Programmierung, Spieleentwicklung oder des Webs ein. Jeder Bereich beinhaltet detaillierte Lerninhalte und Aufgaben von leicht bis ultra schwer.</p>
    </div>
  </header>
  <main class="areas-container">
    <?php foreach ($areas as $area): ?>
      <?php
        $stat = $areaStats[$area['id']];
        // Kürze die Beschreibung für die Übersicht
        $desc = strip_tags($area['description']);
        $shortDesc = mb_substr($desc, 0, 200) . (mb_strlen($desc) > 200 ? '…' : '');
      ?>
      <div class="area-card">
        <h3><?php echo htmlspecialchars($area['name']); ?></h3>
        <p><?php echo htmlspecialchars($shortDesc); ?></p>
        <div class="area-progress-container">
          <div class="area-progress-bar" style="width: <?php echo $stat['percent']; ?>%;"></div>
        </div>
        <p class="area-progress-text">Fortschritt: <?php echo $stat['solved']; ?> von <?php echo $stat['total']; ?> Aufgaben (<?php echo $stat['percent']; ?>%)</p>
        <a class="btn" href="area.php?id=<?php echo $area['id']; ?>">Bereich öffnen</a>
      </div>
    <?php endforeach; ?>
  </main>
  <footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> C‑Portfolio. Viel Erfolg beim Lernen!</p>
  </footer>
</body>
</html>