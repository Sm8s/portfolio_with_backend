<?php
session_start();
require 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Hole alle Aufgaben zusammen mit ihrem Bereich (Area) und sortiere sie nach Bereich und Schwierigkeit
$stmt = $pdo->prepare('SELECT t.*, a.name AS area_name, a.strengths, a.weaknesses FROM tasks t JOIN areas a ON t.area_id = a.id ORDER BY a.id, t.difficulty, t.id');
$stmt->execute();
$tasks = $stmt->fetchAll();

// Lade den Fortschritt des Benutzers (gelöste Aufgaben)
$stmtProgress = $pdo->prepare('SELECT task_id, solved FROM progress WHERE user_id = ?');
$stmtProgress->execute([$_SESSION['user_id']]);
$progressRows = $stmtProgress->fetchAll();
$progressMap = [];
foreach ($progressRows as $p) {
    $progressMap[$p['task_id']] = $p['solved'];
}

// Organisiere Aufgaben nach Bereich
$areas = [];
$totalTasks = count($tasks);
$solvedTasks = 0;
foreach ($tasks as $t) {
    $areaId = $t['area_id'];
    if (!isset($areas[$areaId])) {
        $areas[$areaId] = [
            'name' => $t['area_name'],
            'strengths' => $t['strengths'],
            'weaknesses' => $t['weaknesses'],
            'tasks' => []
        ];
    }
    // Markiere, ob Aufgabe gelöst ist
    $t['solved'] = !empty($progressMap[$t['id']]);
    if ($t['solved']) {
        $solvedTasks++;
    }
    $areas[$areaId]['tasks'][] = $t;
}

// Gesamter Fortschritt in Prozent
$progressPercent = $totalTasks > 0 ? floor(($solvedTasks / $totalTasks) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – C‑Portfolio</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="top-nav">
    <span>Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <a href="areas.php">Alle Bereiche</a>

     <?php include __DIR__ . '/nav_pages_auto.php'; ?>

    <a href="logout.php">Logout</a>
  </nav>
  <header>
    <div class="hero-content">
      <h1>Dein Dashboard</h1>
      <p>Wähle einen Bereich und löse Aufgaben vom Anfänger bis zum Profi – verfolge deinen Fortschritt für jeden Bereich.</p>
    </div>
    <div class="progress-container">
      <div class="progress-bar" style="width: <?php echo $progressPercent; ?>%;"></div>
    </div>
    <p class="progress-text">Fortschritt: <?php echo $solvedTasks; ?> von <?php echo $totalTasks; ?> Aufgaben erledigt (<?php echo $progressPercent; ?>%)</p>
  </header>
  <main>
    <?php foreach ($areas as $area): ?>
      <section class="area-section">
        <div class="area-header">
          <h2><?php echo htmlspecialchars($area['name']); ?></h2>
          <?php if (!empty($area['strengths'])): ?>
            <p class="area-desc"><strong>Stärken:</strong> <?php echo htmlspecialchars($area['strengths']); ?></p>
          <?php endif; ?>
          <?php if (!empty($area['weaknesses'])): ?>
            <p class="area-desc"><strong>Schwächen:</strong> <?php echo htmlspecialchars($area['weaknesses']); ?></p>
          <?php endif; ?>
          <?php
            // Berechne Fortschritt in diesem Bereich
            $areaTotal = count($area['tasks']);
            $areaSolved = 0;
            foreach ($area['tasks'] as $at) {
              if ($at['solved']) $areaSolved++;
            }
            $areaPercent = $areaTotal > 0 ? floor(($areaSolved / $areaTotal) * 100) : 0;
          ?>
          <div class="area-progress-container">
            <div class="area-progress-bar" style="width: <?php echo $areaPercent; ?>%;"></div>
          </div>
          <p class="progress-text">Fortschritt: <?php echo $areaSolved; ?> von <?php echo $areaTotal; ?> Aufgaben (<?php echo $areaPercent; ?>%)</p>
        </div>
        <div class="task-list">
          <?php foreach ($area['tasks'] as $task): ?>
            <?php $solvedClass = $task['solved'] ? 'task-solved' : ''; ?>
            <div class="task-card <?php echo $solvedClass; ?>" style="animation: fadeIn 0.5s ease forwards;">
              <span class="task-badge">Schwierigkeit <?php echo htmlspecialchars($task['difficulty']); ?></span>
              <h3><?php echo htmlspecialchars($task['title']); ?></h3>
              <p><?php echo htmlspecialchars($task['description']); ?></p>
              <a class="task-btn" href="task.php?id=<?php echo $task['id']; ?>">
                <?php echo $task['solved'] ? 'Erneut ansehen' : 'Lösen'; ?>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </main>
  <footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> C‑Portfolio. Viel Erfolg beim Programmieren!</p>
  </footer>
</body>
</html>