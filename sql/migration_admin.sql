-- Admin Panel Migration for c_portfolio
USE c_portfolio;

-- 1) Add admin flag if not exists
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password;

-- 2) Create pages table if not exists
CREATE TABLE IF NOT EXISTS pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional: Seed an admin (password: admin123) if none exists
INSERT INTO users (username, password, is_admin)
SELECT 'admin', '$2y$10$U3x6oM8r2o9r8rS9s6T0pO1nqQJtS2i9bXkQ7oD0eA4zjQK7Wv0rC', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='admin');