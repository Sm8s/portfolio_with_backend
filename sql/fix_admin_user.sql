
USE c_portfolio;

-- Ensure column is_admin exists (compatible for older MariaDB: do it conditionally)
-- If this fails because column already exists, it's okay to ignore.
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password;

-- Upsert-style: create admin user with password 'admin123' (bcrypt hash) and admin flag.
INSERT INTO users (username, password, is_admin)
VALUES ('admin', '$2y$10$U3x6oM8r2o9r8rS9s6T0pO1nqQJtS2i9bXkQ7oD0eA4zjQK7Wv0rC', 1)
ON DUPLICATE KEY UPDATE password=VALUES(password), is_admin=VALUES(is_admin);
