-- Adds an "is_active" flag to the tasks table for filtering active/inactive entries.
-- Run this migration once after deploying the new admin features.

ALTER TABLE tasks ADD COLUMN IF NOT EXISTS is_active BOOLEAN NOT NULL DEFAULT TRUE;
UPDATE tasks SET is_active = TRUE WHERE is_active IS NULL;
