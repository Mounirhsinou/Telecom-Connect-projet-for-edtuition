<?php
/**
 * Password Testing Utility
 * This script helps verify password hashing and database connectivity
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/db.php';

echo "<h1>Password & Database Test</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $db = Database::getInstance();
    echo "✅ <strong>SUCCESS:</strong> Database connected successfully!<br>";
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    die();
}

// Test 2: Check if admin_users table exists
echo "<h2>2. Admin Users Table Test</h2>";
try {
    $sql = "SELECT COUNT(*) as count FROM admin_users";
    $result = $db->queryOne($sql);
    echo "✅ <strong>SUCCESS:</strong> Found {$result['count']} admin user(s) in database<br>";
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    die();
}

// Test 3: Get admin user details
echo "<h2>3. Admin User Details</h2>";
try {
    $sql = "SELECT id, username, password_hash, is_active, failed_login_attempts, locked_until FROM admin_users WHERE username = 'admin'";
    $user = $db->queryOne($sql);

    if ($user) {
        echo "✅ <strong>User Found:</strong><br>";
        echo "- ID: {$user['id']}<br>";
        echo "- Username: {$user['username']}<br>";
        echo "- Password Hash: " . substr($user['password_hash'], 0, 30) . "...<br>";
        echo "- Is Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
        echo "- Failed Attempts: {$user['failed_login_attempts']}<br>";
        echo "- Locked Until: " . ($user['locked_until'] ?? 'Not locked') . "<br>";
    } else {
        echo "❌ <strong>ERROR:</strong> Admin user not found in database!<br>";
        echo "<strong>Solution:</strong> Run the SQL schema again to create the default admin user.<br>";
        die();
    }
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    die();
}

// Test 4: Password Verification
echo "<h2>4. Password Verification Test</h2>";
$test_password = 'admin';
$stored_hash = $user['password_hash'];

echo "Testing password: '<strong>admin</strong>'<br>";

if (password_verify($test_password, $stored_hash)) {
    echo "✅ <strong>SUCCESS:</strong> Password 'admin' matches the stored hash!<br>";
    echo "<strong>You should be able to login with:</strong><br>";
    echo "- Username: <code>admin</code><br>";
    echo "- Password: <code>admin</code><br>";
} else {
    echo "❌ <strong>ERROR:</strong> Password does not match!<br>";
    echo "<strong>Solution:</strong> Update the password hash in the database.<br>";

    // Generate new hash
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    echo "<br><strong>Run this SQL to fix:</strong><br>";
    echo "<pre>UPDATE admin_users SET password_hash = '{$new_hash}' WHERE username = 'admin';</pre>";
}

// Test 5: Check for account locks
echo "<h2>5. Account Lock Status</h2>";
if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
    echo "⚠️ <strong>WARNING:</strong> Account is locked until {$user['locked_until']}<br>";
    echo "<strong>Solution:</strong> Run this SQL to unlock:<br>";
    echo "<pre>UPDATE admin_users SET locked_until = NULL, failed_login_attempts = 0 WHERE username = 'admin';</pre>";
} else {
    echo "✅ <strong>SUCCESS:</strong> Account is not locked<br>";
}

// Test 6: Check IP lockout
echo "<h2>6. IP Lockout Check</h2>";
try {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $sql = "SELECT COUNT(*) as failed_count 
            FROM login_attempts 
            WHERE ip_address = :ip_address 
            AND success = 0 
            AND created_at > DATE_SUB(NOW(), INTERVAL 1800 SECOND)";

    $result = $db->queryOne($sql, ['ip_address' => $ip_address]);

    echo "Your IP: {$ip_address}<br>";
    echo "Failed attempts in last 30 minutes: {$result['failed_count']}<br>";

    if ($result['failed_count'] >= 5) {
        echo "⚠️ <strong>WARNING:</strong> Your IP is locked out!<br>";
        echo "<strong>Solution:</strong> Wait 30 minutes or run this SQL:<br>";
        echo "<pre>DELETE FROM login_attempts WHERE ip_address = '{$ip_address}';</pre>";
    } else {
        echo "✅ <strong>SUCCESS:</strong> IP is not locked out<br>";
    }
} catch (Exception $e) {
    echo "⚠️ <strong>WARNING:</strong> Could not check IP lockout: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 7: Generate new password hash (if needed)
echo "<h2>7. Password Hash Generator</h2>";
echo "If you need to create a new password, here are some hashes:<br><br>";

$passwords = ['admin', 'Admin@2025!', 'password123'];
foreach ($passwords as $pwd) {
    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    echo "Password: <code>{$pwd}</code><br>";
    echo "Hash: <code>{$hash}</code><br><br>";
}

echo "<hr>";
echo "<h2>Quick Fix SQL Commands</h2>";
echo "<p>If login still doesn't work, run these SQL commands in phpMyAdmin:</p>";
echo "<pre>";
echo "-- Reset admin password to 'admin'\n";
echo "UPDATE admin_users SET password_hash = '\$2y\$10\$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy' WHERE username = 'admin';\n\n";
echo "-- Unlock account\n";
echo "UPDATE admin_users SET locked_until = NULL, failed_login_attempts = 0 WHERE username = 'admin';\n\n";
echo "-- Clear IP lockouts\n";
echo "DELETE FROM login_attempts WHERE success = 0;\n\n";
echo "-- Activate account\n";
echo "UPDATE admin_users SET is_active = 1 WHERE username = 'admin';\n";
echo "</pre>";

echo "<hr>";
echo "<p><a href='admin/login.php'>Go to Login Page</a></p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }

    h1 {
        color: #2C7A7B;
    }

    h2 {
        color: #333;
        margin-top: 30px;
        padding: 10px;
        background: #e0e0e0;
        border-left: 4px solid #2C7A7B;
    }

    pre {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow-x: auto;
    }

    code {
        background: #fff;
        padding: 2px 6px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }

    a {
        display: inline-block;
        padding: 10px 20px;
        background: #2C7A7B;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    a:hover {
        background: #1f5a5b;
    }
</style>