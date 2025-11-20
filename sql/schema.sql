-- ============================================
-- Telecom Website Database Schema
-- Version: 1.0.0
-- Description: Production-ready schema for telecom website
-- ============================================

-- Set charset and collation
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ============================================
-- Create database (if not exists)
-- ============================================
CREATE DATABASE IF NOT EXISTS `telecom_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `telecom_db`;

-- ============================================
-- Table: contacts
-- Description: Stores contact form submissions
-- ============================================
DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL COMMENT 'Contact person full name',
  `email` VARCHAR(150) NOT NULL COMMENT 'Contact email address',
  `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Contact phone number',
  `subject` VARCHAR(200) NOT NULL COMMENT 'Message subject',
  `message` TEXT NOT NULL COMMENT 'Message content',
  `plan_interest` VARCHAR(100) DEFAULT NULL COMMENT 'Interested plan/offer',
  `ip_address` VARCHAR(45) NOT NULL COMMENT 'Submitter IP address (IPv4 or IPv6)',
  `user_agent` VARCHAR(255) DEFAULT NULL COMMENT 'Browser user agent',
  `status` ENUM('new', 'replied', 'closed') NOT NULL DEFAULT 'new' COMMENT 'Message status',
  `admin_notes` TEXT DEFAULT NULL COMMENT 'Internal notes from admin',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Submission timestamp',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contact form submissions';

-- ============================================
-- Table: admin_users
-- Description: Admin user accounts
-- ============================================
DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Admin username (unique)',
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'Bcrypt password hash',
  `email` VARCHAR(150) DEFAULT NULL COMMENT 'Admin email (optional)',
  `full_name` VARCHAR(100) DEFAULT NULL COMMENT 'Admin full name',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Account active status',
  `last_login_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Last successful login',
  `last_login_ip` VARCHAR(45) DEFAULT NULL COMMENT 'Last login IP address',
  `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Failed login counter',
  `locked_until` TIMESTAMP NULL DEFAULT NULL COMMENT 'Account lock expiry time',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation timestamp',
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  INDEX `idx_username` (`username`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin user accounts';

-- ============================================
-- Table: login_attempts
-- Description: Track login attempts for security
-- ============================================
DROP TABLE IF EXISTS `login_attempts`;

CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL COMMENT 'Attempted username',
  `ip_address` VARCHAR(45) NOT NULL COMMENT 'IP address of attempt',
  `user_agent` VARCHAR(255) DEFAULT NULL COMMENT 'Browser user agent',
  `success` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Login success (1) or failure (0)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Attempt timestamp',
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_ip_address` (`ip_address`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Login attempt tracking';

-- ============================================
-- Table: rate_limits
-- Description: Rate limiting for contact form (anti-spam)
-- ============================================
DROP TABLE IF EXISTS `rate_limits`;

CREATE TABLE `rate_limits` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL COMMENT 'IP address',
  `action` VARCHAR(50) NOT NULL COMMENT 'Action type (e.g., contact_form)',
  `attempts` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Number of attempts',
  `window_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Rate limit window start',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Rate limit expiry',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ip_action` (`ip_address`, `action`),
  INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rate limiting for spam prevention';

-- ============================================
-- Insert default admin user
-- Username: admin
-- Password: admin
-- ⚠️ FOR TESTING ONLY - CHANGE THIS PASSWORD IN PRODUCTION!
-- ============================================
INSERT INTO `admin_users` (`username`, `password_hash`, `email`, `full_name`, `is_active`) 
VALUES (
  'admin',
  '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', -- Password: admin
  'admin@telecomconnect.com',
  'System Administrator',
  1
);

-- ============================================
-- Insert sample contact (for testing)
-- ============================================
INSERT INTO `contacts` (`name`, `email`, `phone`, `subject`, `message`, `plan_interest`, `ip_address`, `status`) 
VALUES (
  'John Doe',
  'john.doe@example.com',
  '+212 600 123 456',
  'Inquiry about Business Plan',
  'Hello, I would like to know more about your business fiber plans. What speeds are available in Casablanca?',
  'Business Fiber 500',
  '127.0.0.1',
  'new'
);

-- ============================================
-- Create views for admin dashboard
-- ============================================

-- View: Recent contacts (last 30 days)
CREATE OR REPLACE VIEW `view_recent_contacts` AS
SELECT 
  `id`,
  `name`,
  `email`,
  `phone`,
  `subject`,
  LEFT(`message`, 100) AS `message_preview`,
  `plan_interest`,
  `status`,
  `created_at`
FROM `contacts`
WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY `created_at` DESC;

-- View: Contact statistics
CREATE OR REPLACE VIEW `view_contact_stats` AS
SELECT 
  COUNT(*) AS `total_contacts`,
  SUM(CASE WHEN `status` = 'new' THEN 1 ELSE 0 END) AS `new_contacts`,
  SUM(CASE WHEN `status` = 'replied' THEN 1 ELSE 0 END) AS `replied_contacts`,
  SUM(CASE WHEN `status` = 'closed' THEN 1 ELSE 0 END) AS `closed_contacts`,
  SUM(CASE WHEN DATE(`created_at`) = CURDATE() THEN 1 ELSE 0 END) AS `today_contacts`,
  SUM(CASE WHEN `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS `week_contacts`
FROM `contacts`;

-- ============================================
-- Stored procedures (optional, for advanced usage)
-- ============================================

-- Procedure: Clean old login attempts (run periodically)
DELIMITER $$
CREATE PROCEDURE `sp_clean_old_login_attempts`()
BEGIN
  DELETE FROM `login_attempts` 
  WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 30 DAY);
END$$
DELIMITER ;

-- Procedure: Clean expired rate limits
DELIMITER $$
CREATE PROCEDURE `sp_clean_expired_rate_limits`()
BEGIN
  DELETE FROM `rate_limits` 
  WHERE `expires_at` < NOW();
END$$
DELIMITER ;

-- ============================================
-- Triggers
-- ============================================

-- Trigger: Auto-update updated_at on contacts
DELIMITER $$
CREATE TRIGGER `trg_contacts_updated_at` 
BEFORE UPDATE ON `contacts`
FOR EACH ROW
BEGIN
  SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- ============================================
-- Events (for automatic cleanup - requires event scheduler)
-- ============================================

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Event: Clean old login attempts daily
CREATE EVENT IF NOT EXISTS `evt_clean_login_attempts`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
  CALL sp_clean_old_login_attempts();

-- Event: Clean expired rate limits hourly
CREATE EVENT IF NOT EXISTS `evt_clean_rate_limits`
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
  CALL sp_clean_expired_rate_limits();

-- ============================================
-- Indexes for performance optimization
-- ============================================

-- Additional composite indexes for common queries
ALTER TABLE `contacts` 
ADD INDEX `idx_status_created` (`status`, `created_at`);

ALTER TABLE `login_attempts` 
ADD INDEX `idx_username_created` (`username`, `created_at`);

-- ============================================
-- Grant privileges (adjust as needed)
-- ============================================

-- For development (localhost only)
-- GRANT ALL PRIVILEGES ON telecom_db.* TO 'root'@'localhost';

-- For production (create dedicated user)
-- CREATE USER 'telecom_user'@'localhost' IDENTIFIED BY 'strong_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON telecom_db.* TO 'telecom_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- Schema version tracking
-- ============================================
CREATE TABLE IF NOT EXISTS `schema_version` (
  `version` VARCHAR(10) NOT NULL,
  `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `schema_version` (`version`) VALUES ('1.0.0');

-- ============================================
-- End of schema
-- ============================================

-- Display success message
SELECT 'Database schema created successfully!' AS 'Status',
       'Default admin username: admin' AS 'Info',
       'Default admin password: admin' AS 'Warning',
       'FOR TESTING ONLY - CHANGE IN PRODUCTION!' AS 'Action Required';
