# Admin Panel (PHP/Bootstrap, Dark Elegant)

## Installation (XAMPP)
1. Entpacke den Ordner **admin** in: `htdocs/portfolio_with_backend/admin/`
2. Importiere `sql/migration_admin.sql` in phpMyAdmin (Datenbank `c_portfolio`).
3. Öffne im Browser: `http://localhost/portfolio_with_backend/admin/login.php`
4. Login mit: **admin / admin123** (bitte sofort Passwort ändern).
5. Navigiere über die Navbar zu *Seiten*, *Areas*, *Aufgaben*, *Benutzer*.

## Hinweise
- Nutzt `config.php` im Projektroot und speichert alles in MySQL.
- Seiteninhalte werden in der Tabelle `pages` gepflegt.
- Benutzerverwaltung nur für Admins sichtbar.
- Design: Dark Elegant mit leichten Animationen/Glas-Effekt.