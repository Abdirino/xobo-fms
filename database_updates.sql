-- Add created_at column to upload table if it doesn't exist
ALTER TABLE upload ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
-- Update existing records
UPDATE upload SET created_at = NOW() WHERE created_at IS NULL;
