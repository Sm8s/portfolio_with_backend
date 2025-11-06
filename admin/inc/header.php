<?php
// inc/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · C‑Portfolio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/portfolio_with_backend/admin/styles.css" rel="stylesheet">
</head>
<body class="bg-gradient-dark">
<nav class="navbar navbar-expand-lg navbar-dark glassy border-bottom border-secondary-subtle">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/portfolio_with_backend/admin/">⚙️ Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/pages.php">Seiten</a></li>
        <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/areas.php">Areas</a></li>
        <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/tasks.php">Aufgaben</a></li>
        <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/users.php">Benutzer</a></li>
        <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/backup.php">Backup</a></li>
      <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/media.php">Mediathek</a></li>
      <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/menus.php">Menü-Builder</a></li>
      <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/csv.php">CSV</a></li>
      <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/translate.php">Übersetzer</a></li>
      <li class="nav-item"><a class="nav-link" href="/portfolio_with_backend/admin/audit.php">Audit</a></li>
      </ul>
      <div class="d-flex gap-2 align-items-center">
        <?php if (!empty($_SESSION['username'])): ?>
          <span class="text-secondary small">👤 <?=$_SESSION['username']?></span>
          <a class="btn btn-sm btn-outline-light" href="/portfolio_with_backend/admin/logout.php">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="container my-4 fade-slide-in">