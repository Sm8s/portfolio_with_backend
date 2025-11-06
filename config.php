<?php
// Flexible database configuration supporting Netlify Neon (PostgreSQL) and local MySQL fallback.

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$databaseUrl = getenv('NETLIFY_DATABASE_URL') ?: getenv('DATABASE_URL');

if ($databaseUrl) {
    // Expecting a Postgres style URL, e.g. postgres://user:pass@host:port/dbname
    $parts = parse_url($databaseUrl);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host']) || empty($parts['path'])) {
        die('Ungültige NETLIFY_DATABASE_URL-Konfiguration.');
    }

    $host = $parts['host'];
    $port = $parts['port'] ?? 5432;
    $user = urldecode($parts['user'] ?? '');
    $pass = urldecode($parts['pass'] ?? '');
    $db   = ltrim($parts['path'], '/');

    // Allow overriding sslmode via query string (?sslmode=disable)
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    $sslMode = $query['sslmode'] ?? 'require';

    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=%s', $host, $port, $db, $sslMode);

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Verbindung zur Netlify Neon Datenbank fehlgeschlagen: ' . $e->getMessage());
    }
} else {
    // Local development fallback (e.g. XAMPP MySQL)
    $host = 'localhost';
    $db   = 'c_portfolio';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Datenbankverbindung fehlgeschlagen. Bitte prüfe deine lokale Datenbankkonfiguration.');
    }
}
?>
