USE c_portfolio;
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password;
UPDATE users SET is_admin = 1 WHERE LOWER(username) = 'admin';
