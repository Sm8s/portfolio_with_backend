<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get task ID from query
$taskId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($taskId <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Fetch task info including area name
$stmt = $pdo->prepare('SELECT t.*, a.name AS area_name FROM tasks t JOIN areas a ON t.area_id = a.id WHERE t.id = ?');
$stmt->execute([$taskId]);
$task = $stmt->fetch();
if (!$task) {
    header('Location: dashboard.php');
    exit;
}

// Check user progress for this task (also retrieve user_code if present)
$stmt = $pdo->prepare('SELECT solved, user_code FROM progress WHERE user_id = ? AND task_id = ?');
$stmt->execute([$_SESSION['user_id'], $taskId]);
$progress = $stmt->fetch();
$isSolved = $progress && $progress['solved'];

// If user clicked "solve", mark as solved and store submitted code
if (isset($_POST['mark_solved']) && !$isSolved) {
    // Sanitize user code
    $submittedCode = isset($_POST['code']) ? $_POST['code'] : '';
    if ($progress) {
        $stmt = $pdo->prepare('UPDATE progress SET solved = 1, user_code = ? WHERE user_id = ? AND task_id = ?');
        $stmt->execute([$submittedCode, $_SESSION['user_id'], $taskId]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO progress (user_id, task_id, solved, user_code) VALUES (?, ?, 1, ?)');
        $stmt->execute([$_SESSION['user_id'], $taskId, $submittedCode]);
    }
    $isSolved = true;
    // Update $progress for later use
    $progress = ['solved' => 1, 'user_code' => $submittedCode];
}

// Load solution content if solved and prepare diff
$solutionContent = '';
$comparisonHtml = '';
if ($isSolved && $task['solution_file']) {
    $filePath = __DIR__ . '/' . $task['solution_file'];
    if (file_exists($filePath)) {
        // Raw solution code (no escaping yet)
        $rawSolution = file_get_contents($filePath);
        $solutionLines = explode("\n", $rawSolution);
        // Escape for display
        $solutionContent = htmlspecialchars($rawSolution);
        // Compute comparison if user_code is available
        $userCode = isset($progress['user_code']) ? $progress['user_code'] : '';
        if ($userCode !== '') {
            $userLines = explode("\n", str_replace("\r", '', $userCode));
            $maxLines = max(count($solutionLines), count($userLines));
            $comparisonRows = [];
            for ($i = 0; $i < $maxLines; $i++) {
                $solLine = $i < count($solutionLines) ? $solutionLines[$i] : '';
                $usrLine = $i < count($userLines) ? $userLines[$i] : '';
                $escapedSol = htmlspecialchars($solLine);
                $escapedUsr = htmlspecialchars($usrLine);
                if (trim($solLine) === trim($usrLine)) {
                    $comparisonRows[] = '<tr><td><pre>' . $escapedUsr . '</pre></td><td><pre>' . $escapedSol . '</pre></td></tr>';
                } else {
                    $comparisonRows[] = '<tr><td><pre><span style="color:#e06c75">' . $escapedUsr . '</span></pre></td><td><pre>' . $escapedSol . '</pre></td></tr>';
                }
            }
            $comparisonHtml = '<table class="diff-table"><thead><tr><th>Dein Code</th><th>Lösung</th></tr></thead><tbody>' . implode("", $comparisonRows) . '</tbody></table>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($task['title']); ?> – C‑Portfolio</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .task-container { max-width: 800px; margin: auto; padding: 2rem; }
    .task-container h1 { margin-top: 0; color: #64ffda; }
    .hint { background-color: #112240; padding: 1rem; border-radius: 6px; margin: 1rem 0; display: none; }
    .solution { background-color: #112240; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
    textarea.code-input { width: 100%; height: 200px; background-color: #0a192f; color: #ccd6f6; border: 1px solid #233554; border-radius: 4px; padding: 1rem; }
    .btn { display: inline-block; margin-top: 1rem; padding: 0.5rem 1rem; background-color: #64ffda; color: #0a192f; border-radius: 4px; text-decoration: none; font-weight: bold; cursor: pointer; transition: background-color 0.3s; }
    .btn:hover { background-color: #52cca8; }

    /* Tabelle fuer Codevergleich */
    .diff-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .diff-table th, .diff-table td { vertical-align: top; padding: 0.5rem; border: 1px solid #233554; }
    .diff-table th { background-color: #0a192f; color: #64ffda; }
    .diff-table pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
  </style>
</head>
<body>
  <nav class="top-nav">
    <span>Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <a href="areas.php">Alle Bereiche</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </nav>
  <main class="task-container">
    <h1><?php echo htmlspecialchars($task['title']); ?></h1>
    <p><strong>Bereich:</strong> <?php echo htmlspecialchars($task['area_name']); ?> | <strong>Schwierigkeit:</strong> <?php echo htmlspecialchars($task['difficulty']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
    <?php if ($task['hint']): ?>
    <button class="btn" id="show-hint-btn">Tipp anzeigen</button>
    <div class="hint" id="hint-box">
      <strong>Tipp:</strong><br>
      <?php echo nl2br(htmlspecialchars($task['hint'])); ?>
    </div>
    <?php endif; ?>
    <?php if (!$isSolved): ?>
    <form method="post">
      <label for="code-input">Deine Lösung (nur für dich, wird nicht bewertet):</label><br>
      <textarea id="code-input" name="code" class="code-input" placeholder="Schreibe hier deinen C‑Code..."></textarea>
      <button type="submit" name="mark_solved" class="btn">Ich habe die Aufgabe gelöst</button>
    </form>
    <?php else: ?>
    <div class="solution">
      <?php if ($comparisonHtml): ?>
        <strong>Dein Code vs. Lösung (abweichende Zeilen rot markiert):</strong>
        <?php echo $comparisonHtml; ?>
      <?php endif; ?>
      <strong>Offizielle Lösung:</strong>
      <pre><?php echo $solutionContent; ?></pre>
    </div>
    <?php endif; ?>
  </main>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var hintBtn = document.getElementById('show-hint-btn');
    if(hintBtn) {
      hintBtn.addEventListener('click', function() {
        var hintBox = document.getElementById('hint-box');
        if(hintBox.style.display === 'block') {
          hintBox.style.display = 'none';
          hintBtn.textContent = 'Tipp anzeigen';
        } else {
          hintBox.style.display = 'block';
          hintBtn.textContent = 'Tipp verstecken';
        }
      });
    }
  });
  </script>
</body>
</html>