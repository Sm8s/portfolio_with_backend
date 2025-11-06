<?php
session_start();

// Redirect logged in users to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C‑Portfolio: Login oder Registrieren</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <header class="hero">
        <h1>Willkommen zum C‑Portfolio</h1>
        <p>Bitte melde dich an oder registriere dich, um die Aufgaben zu bearbeiten und deinen Fortschritt zu speichern.</p>
    </header>
    <main class="auth-container">
        <section class="auth-form">
            <h2>Login</h2>
            <?php if (isset($_GET['error']) && $_GET['error'] === '1'): ?>
                <p class="error">Benutzername oder Passwort falsch.</p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <label for="login-username">Benutzername</label>
                <input type="text" id="login-username" name="username" required>
                <label for="login-password">Passwort</label>
                <input type="password" id="login-password" name="password" required>
                <button type="submit">Anmelden</button>
            </form>
        </section>
        <section class="auth-form">
            <h2>Registrieren</h2>
            <?php if (isset($_GET['error']) && $_GET['error'] === '2'): ?>
                <p class="error">Benutzername existiert bereits. Bitte wähle einen anderen.</p>
            <?php endif; ?>
            <form action="register.php" method="post">
                <label for="reg-username">Benutzername</label>
                <input type="text" id="reg-username" name="username" required>
                <label for="reg-password">Passwort</label>
                <input type="password" id="reg-password" name="password" required>
                <button type="submit">Registrieren</button>
            </form>
        </section>
    </main>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> C‑Portfolio. Erstellt für Lern- und Bewerbungszwecke.</p>
    </footer>
</body>
</html>