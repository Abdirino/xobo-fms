ALTER TABLE `upload` 
ADD COLUMN `category` ENUM('purchase_receipts', 'sales_invoices', 'petty_cash_reports', 'client_agreements', 'partner_agreements') NOT NULL,
ADD COLUMN `year` YEAR DEFAULT NULL,
ADD INDEX `category_year_idx` (`category`, `year`);

-- Update existing records to have a default category if needed
UPDATE `upload` SET `category` = 'purchase_receipts' WHERE `category` IS NULL;
