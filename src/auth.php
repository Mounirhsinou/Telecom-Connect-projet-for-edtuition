<?php
/**
 * Authentication Functions
 * 
 * Handles user authentication, session management, and security.
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';

/**
 * Start secure session
 * Configures session with security best practices
 */
function startSecureSession()
{
    // Session already started
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Session configuration for security
    ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access
    ini_set('session.use_only_cookies', 1); // Only use cookies
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // HTTPS only if available
    ini_set('session.cookie_samesite', 'Strict'); // CSRF protection

    // Set session name
    session_name(SESSION_NAME);

    // Set session lifetime
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);

    // Start session
    session_start();

    // Regenerate session ID periodically to prevent fixation
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerate every 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool Login status
 */
function isLoggedIn()
{
    startSecureSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require authentication (redirect if not logged in)
 * 
 * @param string $redirect_url URL to redirect to if not logged in
 */
function requireAuth($redirect_url = 'login.php')
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Authenticate admin user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return array Result array with 'success' and 'message' keys
 */
function authenticateAdmin($username, $password)
{
    $db = Database::getInstance();

    // Get client IP
    $ip_address = getClientIP();

    // Check if IP is locked out
    if (isIPLockedOut($ip_address)) {
        logLoginAttempt($username, $ip_address, false);
        return [
            'success' => false,
            'message' => 'Too many failed attempts. Please try again in ' . LOCKOUT_DURATION / 60 . ' minutes.'
        ];
    }

    // Validate input
    if (empty($username) || empty($password)) {
        return [
            'success' => false,
            'message' => 'Username and password are required.'
        ];
    }

    try {
        // Get user from database
        $sql = "SELECT id, username, password_hash, is_active, failed_login_attempts, locked_until 
                FROM admin_users 
                WHERE username = :username 
                LIMIT 1";

        $user = $db->queryOne($sql, ['username' => $username]);

        // User not found
        if (!$user) {
            logLoginAttempt($username, $ip_address, false);
            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }

        // Check if account is active
        if (!$user['is_active']) {
            logLoginAttempt($username, $ip_address, false);
            return [
                'success' => false,
                'message' => 'Account is disabled. Please contact administrator.'
            ];
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            logLoginAttempt($username, $ip_address, false);
            return [
                'success' => false,
                'message' => 'Account is temporarily locked. Please try again later.'
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Increment failed attempts
            incrementFailedAttempts($user['id']);
            logLoginAttempt($username, $ip_address, false);

            return [
                'success' => false,
                'message' => 'Invalid username or password.'
            ];
        }

        // Password is correct - reset failed attempts
        resetFailedAttempts($user['id']);

        // Update last login
        updateLastLogin($user['id'], $ip_address);

        // Log successful attempt
        logLoginAttempt($username, $ip_address, true);

        // Set session variables
        startSecureSession();
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $ip_address;

        return [
            'success' => true,
            'message' => 'Login successful.'
        ];

    } catch (Exception $e) {
        error_log('Authentication error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred. Please try again.'
        ];
    }
}

/**
 * Logout admin user
 */
function logout()
{
    startSecureSession();

    // Unset all session variables
    $_SESSION = [];

    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();
}

/**
 * Get current admin user info
 * 
 * @return array|null User info or null if not logged in
 */
function getCurrentAdmin()
{
    if (!isLoggedIn()) {
        return null;
    }

    $db = Database::getInstance();

    $sql = "SELECT id, username, email, full_name, last_login_at 
            FROM admin_users 
            WHERE id = :id 
            LIMIT 1";

    return $db->queryOne($sql, ['id' => $_SESSION['admin_id']]);
}

/**
 * Increment failed login attempts
 * 
 * @param int $user_id User ID
 */
function incrementFailedAttempts($user_id)
{
    $db = Database::getInstance();

    $sql = "UPDATE admin_users 
            SET failed_login_attempts = failed_login_attempts + 1,
                locked_until = CASE 
                    WHEN failed_login_attempts + 1 >= :max_attempts 
                    THEN DATE_ADD(NOW(), INTERVAL :lockout_duration SECOND)
                    ELSE NULL 
                END
            WHERE id = :user_id";

    $db->execute($sql, [
        'user_id' => $user_id,
        'max_attempts' => MAX_LOGIN_ATTEMPTS,
        'lockout_duration' => LOCKOUT_DURATION
    ]);
}

/**
 * Reset failed login attempts
 * 
 * @param int $user_id User ID
 */
function resetFailedAttempts($user_id)
{
    $db = Database::getInstance();

    $sql = "UPDATE admin_users 
            SET failed_login_attempts = 0, 
                locked_until = NULL 
            WHERE id = :user_id";

    $db->execute($sql, ['user_id' => $user_id]);
}

/**
 * Update last login timestamp and IP
 * 
 * @param int $user_id User ID
 * @param string $ip_address IP address
 */
function updateLastLogin($user_id, $ip_address)
{
    $db = Database::getInstance();

    $sql = "UPDATE admin_users 
            SET last_login_at = NOW(), 
                last_login_ip = :ip_address 
            WHERE id = :user_id";

    $db->execute($sql, [
        'user_id' => $user_id,
        'ip_address' => $ip_address
    ]);
}

/**
 * Log login attempt
 * 
 * @param string $username Username
 * @param string $ip_address IP address
 * @param bool $success Success status
 */
function logLoginAttempt($username, $ip_address, $success)
{
    $db = Database::getInstance();

    $sql = "INSERT INTO login_attempts (username, ip_address, user_agent, success) 
            VALUES (:username, :ip_address, :user_agent, :success)";

    $db->execute($sql, [
        'username' => $username,
        'ip_address' => $ip_address,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        'success' => $success ? 1 : 0
    ]);
}

/**
 * Check if IP is locked out due to too many failed attempts
 * 
 * @param string $ip_address IP address
 * @return bool Lockout status
 */
function isIPLockedOut($ip_address)
{
    $db = Database::getInstance();

    $sql = "SELECT COUNT(*) as failed_count 
            FROM login_attempts 
            WHERE ip_address = :ip_address 
            AND success = 0 
            AND created_at > DATE_SUB(NOW(), INTERVAL :lockout_duration SECOND)";

    $result = $db->queryOne($sql, [
        'ip_address' => $ip_address,
        'lockout_duration' => LOCKOUT_DURATION
    ]);

    return $result['failed_count'] >= MAX_LOGIN_ATTEMPTS;
}

/**
 * Get client IP address (handles proxies)
 * 
 * @return string IP address
 */
function getClientIP()
{
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER)) {
            $ip_list = explode(',', $_SERVER[$key]);
            $ip = trim($ip_list[0]);

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return '0.0.0.0';
}
