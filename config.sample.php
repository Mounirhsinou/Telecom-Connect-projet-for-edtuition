<?php
/**
 * Configuration File - Sample
 * 
 * Copy this file to config.php and update with your actual credentials.
 * NEVER commit config.php to version control!
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

/** Database host (usually 'localhost' for local development) */
define('DB_HOST', 'localhost');

/** Database name */
define('DB_NAME', 'telecom_db');

/** Database username */
define('DB_USER', 'root');

/** Database password (empty for XAMPP default, set your password for production) */
define('DB_PASS', '');

/** Database charset */
define('DB_CHARSET', 'utf8mb4');

// ============================================
// SITE CONFIGURATION
// ============================================

/** Site name */
define('SITE_NAME', 'Telecom Connect');

/** Site URL (update for production) */
define('SITE_URL', 'http://localhost/telecom/public');

/** Admin URL */
define('ADMIN_URL', 'http://localhost/telecom/admin');

/** Site email (for notifications) */
define('SITE_EMAIL', 'contact@telecomconnect.com');

// ============================================
// SECURITY CONFIGURATION
// ============================================

/** 
 * Secret key for CSRF token generation
 * IMPORTANT: Generate a unique random string for production!
 * You can use: bin2hex(random_bytes(32))
 */
define('SECRET_KEY', 'your-secret-key-change-this-in-production-use-random-string');

/** Session name (change for additional security) */
define('SESSION_NAME', 'telecom_session');

/** Session lifetime in seconds (2 hours) */
define('SESSION_LIFETIME', 7200);

/** Maximum login attempts before lockout */
define('MAX_LOGIN_ATTEMPTS', 5);

/** Login lockout duration in seconds (15 minutes) */
define('LOCKOUT_DURATION', 900);

// ============================================
// ENVIRONMENT CONFIGURATION
// ============================================

/** 
 * Environment: 'development' or 'production'
 * Set to 'production' when deploying to live server
 */
define('ENVIRONMENT', 'development');

/** Display errors (true for development, false for production) */
define('DISPLAY_ERRORS', ENVIRONMENT === 'development');

/** Error logging (always recommended) */
define('LOG_ERRORS', true);

/** Error log file path */
define('ERROR_LOG_PATH', __DIR__ . '/logs/error.log');

// ============================================
// EMAIL CONFIGURATION (Optional - for notifications)
// ============================================

/** Enable email notifications */
define('ENABLE_EMAIL', false);

/** SMTP Host (e.g., smtp.gmail.com) */
define('SMTP_HOST', 'smtp.gmail.com');

/** SMTP Port (587 for TLS, 465 for SSL) */
define('SMTP_PORT', 587);

/** SMTP Encryption (tls or ssl) */
define('SMTP_ENCRYPTION', 'tls');

/** SMTP Username */
define('SMTP_USER', 'your-email@gmail.com');

/** SMTP Password (use app-specific password for Gmail) */
define('SMTP_PASS', 'your-app-password');

/** Email from address */
define('EMAIL_FROM', 'noreply@telecomconnect.com');

/** Email from name */
define('EMAIL_FROM_NAME', 'Telecom Connect');

// ============================================
// RATE LIMITING (Anti-spam)
// ============================================

/** Enable rate limiting for contact form */
define('ENABLE_RATE_LIMIT', true);

/** Maximum submissions per IP within time window */
define('RATE_LIMIT_MAX', 3);

/** Rate limit time window in seconds (1 hour) */
define('RATE_LIMIT_WINDOW', 3600);

// ============================================
// FILE UPLOAD CONFIGURATION (if needed in future)
// ============================================

/** Maximum file upload size in bytes (2MB) */
define('MAX_UPLOAD_SIZE', 2097152);

/** Allowed file extensions */
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

/** Upload directory */
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// ============================================
// PAGINATION CONFIGURATION
// ============================================

/** Items per page in admin dashboard */
define('ITEMS_PER_PAGE', 20);

// ============================================
// DEFAULT ADMIN CREDENTIALS
// ============================================

/**
 * Default admin account (created by schema.sql)
 * 
 * Username: admin
 * Password: Admin@2025!
 * 
 * ⚠️ CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN!
 * 
 * To create a new admin user:
 * 1. Generate password hash: password_hash('YourPassword', PASSWORD_BCRYPT)
 * 2. Insert into database:
 *    INSERT INTO admin_users (username, password_hash) 
 *    VALUES ('username', 'hash_from_step_1');
 */

// ============================================
// TIMEZONE CONFIGURATION
// ============================================

/** Default timezone */
define('TIMEZONE', 'Africa/Casablanca'); // Change to your timezone

// Set timezone
date_default_timezone_set(TIMEZONE);

// ============================================
// ERROR REPORTING
// ============================================

if (DISPLAY_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

if (LOG_ERRORS) {
    ini_set('log_errors', 1);
    if (defined('ERROR_LOG_PATH')) {
        ini_set('error_log', ERROR_LOG_PATH);
    }
}

// ============================================
// SECURITY HEADERS (Recommended for production)
// ============================================

/**
 * Uncomment these in production for enhanced security
 */

// Prevent clickjacking
// header('X-Frame-Options: SAMEORIGIN');

// Prevent MIME type sniffing
// header('X-Content-Type-Options: nosniff');

// Enable XSS protection
// header('X-XSS-Protection: 1; mode=block');

// Referrer policy
// header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy (adjust as needed)
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");

// ============================================
// CONSTANTS FOR PATHS
// ============================================

/** Root directory */
define('ROOT_DIR', __DIR__);

/** Public directory */
define('PUBLIC_DIR', ROOT_DIR . '/public');

/** Source directory */
define('SRC_DIR', ROOT_DIR . '/src');

/** Admin directory */
define('ADMIN_DIR', ROOT_DIR . '/admin');

// ============================================
// DEBUGGING (Development only)
// ============================================

/**
 * Set to true to enable debug mode
 * Shows detailed error messages and query logs
 * NEVER enable in production!
 */
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ============================================
// END OF CONFIGURATION
// ============================================

/**
 * DO NOT EDIT BELOW THIS LINE
 */

// Verify configuration
if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER')) {
    die('Configuration error: Database credentials not set. Please check config.php');
}

if (ENVIRONMENT === 'production' && SECRET_KEY === 'your-secret-key-change-this-in-production-use-random-string') {
    die('Security error: Please change SECRET_KEY in config.php before deploying to production!');
}
