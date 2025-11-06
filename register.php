<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if username exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        // Username already exists
        header('Location: index.php?error=2');
        exit;
    }
    // Insert new user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    $userId = $pdo->lastInsertId();
    // Set session and redirect
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    header('Location: dashboard.php');
    exit;
}
// If not a POST request, redirect to index
header('Location: index.php');
exit;