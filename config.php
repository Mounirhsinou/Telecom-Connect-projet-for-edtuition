<?php
/**
 * Configuration File
 * 
 * IMPORTANT: This file contains your actual database credentials and secrets.
 * Copy from config.sample.php and update with your values.
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'telecom_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Update with your MySQL password
define('DB_CHARSET', 'utf8mb4');

// ============================================
// SITE CONFIGURATION
// ============================================

define('SITE_NAME', 'Telecom Connect');
define('SITE_URL', 'http://localhost/TELECOME%20TEST/public');
define('ADMIN_URL', 'http://localhost/TELECOME%20TEST/admin');
define('SITE_EMAIL', 'contact@telecomconnect.com');

// ============================================
// SECURITY CONFIGURATION
// ============================================

define('SECRET_KEY', bin2hex(random_bytes(32))); // Auto-generated secret key
define('SESSION_NAME', 'telecom_session');
define('SESSION_LIFETIME', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900);

// ============================================
// ENVIRONMENT CONFIGURATION
// ============================================

define('ENVIRONMENT', 'development'); // Change to 'production' for live site
define('DISPLAY_ERRORS', ENVIRONMENT === 'development');
define('LOG_ERRORS', true);
define('ERROR_LOG_PATH', __DIR__ . '/logs/error.log');
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ============================================
// EMAIL CONFIGURATION
// ============================================

define('ENABLE_EMAIL', false);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('EMAIL_FROM', 'noreply@telecomconnect.com');
define('EMAIL_FROM_NAME', 'Telecom Connect');

// ============================================
// RATE LIMITING
// ============================================

define('ENABLE_RATE_LIMIT', true);
define('RATE_LIMIT_MAX', 3);
define('RATE_LIMIT_WINDOW', 3600);

// ============================================
// PAGINATION
// ============================================

define('ITEMS_PER_PAGE', 20);

// ============================================
// TIMEZONE
// ============================================

define('TIMEZONE', 'Africa/Casablanca');
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
// PATHS
// ============================================

define('ROOT_DIR', __DIR__);
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('SRC_DIR', ROOT_DIR . '/src');
define('ADMIN_DIR', ROOT_DIR . '/admin');
