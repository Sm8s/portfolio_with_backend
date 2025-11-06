<?php
session_start();
require 'config.php';

// Sicherstellen, dass der Benutzer angemeldet ist
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// ID des Bereichs aus Query ermitteln
$areaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($areaId <= 0) {
    header('Location: areas.php');
    exit;
}

// Bereichsdaten abrufen
$stmtArea = $pdo->prepare('SELECT * FROM areas WHERE id = ?');
$stmtArea->execute([$areaId]);
$area = $stmtArea->fetch();
if (!$area) {
    header('Location: areas.php');
    exit;
}

// Aufgaben dieses Bereichs laden
$stmtTasks = $pdo->prepare('SELECT * FROM tasks WHERE area_id = ? AND (is_active IS NULL OR is_active = TRUE) ORDER BY difficulty, id');
$stmtTasks->execute([$areaId]);
$tasks = $stmtTasks->fetchAll();

// Fortschritt des Benutzers laden
$stmtProgress = $pdo->prepare('SELECT task_id, solved FROM progress WHERE user_id = ?');
$stmtProgress->execute([$_SESSION['user_id']]);
$progressRows = $stmtProgress->fetchAll();
$progressMap = [];
foreach ($progressRows as $p) {
    $progressMap[$p['task_id']] = $p['solved'];
}

// Bereichsfortschritt berechnen
$totalTasks = count($tasks);
$solvedTasks = 0;
foreach ($tasks as &$t) {
    $t['solved'] = !empty($progressMap[$t['id']]);
    if ($t['solved']) $solvedTasks++;
}
$progressPercent = $totalTasks > 0 ? floor(($solvedTasks / $totalTasks) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($area['name']); ?> – C‑Portfolio</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .area-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 2rem;
    }
    .area-header {
      margin-bottom: 2rem;
    }
    .area-header h1 {
      margin-top: 0;
      color: #64ffda;
    }
    .area-header p {
      color: #8892b0;
      line-height: 1.5;
    }
    .desc-box {
      background-color: #112240;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }
    .desc-box strong {
      display: block;
      margin-top: 0.5rem;
    }
    .task-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .task-card {
      background-color: #112240;
      border-radius: 6px;
      padding: 1rem;
      flex: 1 1 300px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s;
    }
    .task-card:hover {
      transform: translateY(-2px);
    }
    .task-card h3 {
      margin: 0;
      color: #e6f1ff;
    }
    .task-card p {
      color: #8892b0;
      flex-grow: 1;
    }
    .task-card .task-badge {
      display: inline-block;
      background-color: #233554;
      color: #8892b0;
      border-radius: 4px;
      padding: 0.1rem 0.4rem;
      font-size: 0.8rem;
      margin-bottom: 0.5rem;
    }
    .task-card.task-solved {
      border-left: 4px solid #64ffda;
    }
    .task-card .task-btn {
      align-self: flex-start;
      margin-top: 1rem;
      background-color: #64ffda;
      color: #0a192f;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      text-decoration: none;
      transition: background-color 0.3s;
    }
    .task-card .task-btn:hover {
      background-color: #52cca8;
    }
    .area-progress-container {
      width: 100%;
      height: 8px;
      background-color: #233554;
      border-radius: 4px;
      overflow: hidden;
    }
    .area-progress-bar {
      height: 100%;
      background-color: #64ffda;
      width: <?php echo $progressPercent; ?>%;
      transition: width 0.4s ease;
    }
    .area-progress-text {
      color: #8892b0;
      font-size: 0.9rem;
      margin-top: 0.2rem;
    }
    .back-link {
      display: inline-block;
      margin-bottom: 1rem;
      color: #64ffda;
      text-decoration: none;
    }
    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <span>Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <a href="areas.php">Alle Bereiche</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
  <main class="area-container">
    <a class="back-link" href="areas.php">← Zurück zur Bereichsübersicht</a>
    <div class="area-header">
      <h1><?php echo htmlspecialchars($area['name']); ?></h1>
      <div class="area-progress-container">
        <div class="area-progress-bar"></div>
      </div>
      <p class="area-progress-text">Fortschritt: <?php echo $solvedTasks; ?> von <?php echo $totalTasks; ?> Aufgaben (<?php echo $progressPercent; ?>%)</p>
    </div>
    <?php if (!empty($area['description'])): ?>
      <div class="desc-box">
        <?php echo nl2br(htmlspecialchars($area['description'])); ?>
        <?php if (!empty($area['strengths'])): ?>
          <strong>Stärken:</strong> <?php echo htmlspecialchars($area['strengths']); ?>
        <?php endif; ?>
        <?php if (!empty($area['weaknesses'])): ?>
          <strong>Schwächen:</strong> <?php echo htmlspecialchars($area['weaknesses']); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <h2>Aufgaben</h2>
    <div class="task-grid">
      <?php foreach ($tasks as $task): ?>
        <?php $solvedClass = $task['solved'] ? 'task-solved' : ''; ?>
        <div class="task-card <?php echo $solvedClass; ?>">
          <span class="task-badge">Schwierigkeit <?php echo htmlspecialchars($task['difficulty']); ?></span>
          <h3><?php echo htmlspecialchars($task['title']); ?></h3>
          <p><?php echo htmlspecialchars($task['description']); ?></p>
          <a class="task-btn" href="task.php?id=<?php echo $task['id']; ?>">
            <?php echo $task['solved'] ? 'Erneut ansehen' : 'Lösen'; ?>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
  <footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> C‑Portfolio. Viel Erfolg beim Programmieren!</p>
  </footer>
</body>
</html>