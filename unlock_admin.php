<?php
/**
 * Admin Account Unlock Utility
 * This script unlocks the admin account and clears all lockouts
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/db.php';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Unlock Admin Account</title>";
echo "<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    .success {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        margin: 20px 0;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        margin: 20px 0;
    }
    .info {
        background: #d1ecf1;
        color: #0c5460;
        padding: 15px;
        border: 1px solid #bee5eb;
        border-radius: 5px;
        margin: 20px 0;
    }
    h1 { color: #2C7A7B; }
    h2 { color: #333; margin-top: 30px; }
    .btn {
        display: inline-block;
        padding: 12px 24px;
        background: #2C7A7B;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin: 10px 5px;
        font-weight: bold;
    }
    .btn:hover {
        background: #1f5a5b;
    }
    .btn-danger {
        background: #dc3545;
    }
    .btn-danger:hover {
        background: #c82333;
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
</style>";
echo "</head><body>";

echo "<h1>🔓 Admin Account Unlock Utility</h1>";
echo "<hr>";

try {
    $db = Database::getInstance();

    // Check if unlock is requested
    if (isset($_GET['action']) && $_GET['action'] === 'unlock') {

        echo "<h2>Unlocking Admin Account...</h2>";

        // Step 1: Unlock admin account
        echo "<p>Step 1: Unlocking admin account...</p>";
        $sql1 = "UPDATE admin_users 
                 SET locked_until = NULL, 
                     failed_login_attempts = 0 
                 WHERE username = 'admin'";
        $db->execute($sql1);
        echo "<div class='success'>✅ Admin account unlocked successfully!</div>";

        // Step 2: Clear all failed login attempts
        echo "<p>Step 2: Clearing all failed login attempts...</p>";
        $sql2 = "DELETE FROM login_attempts WHERE success = 0";
        $result = $db->execute($sql2);
        echo "<div class='success'>✅ Cleared all failed login attempts!</div>";

        // Step 3: Clear IP-based rate limits
        echo "<p>Step 3: Clearing IP-based rate limits...</p>";
        $sql3 = "DELETE FROM rate_limits";
        $db->execute($sql3);
        echo "<div class='success'>✅ Cleared all rate limits!</div>";

        // Step 4: Ensure account is active
        echo "<p>Step 4: Ensuring account is active...</p>";
        $sql4 = "UPDATE admin_users 
                 SET is_active = 1 
                 WHERE username = 'admin'";
        $db->execute($sql4);
        echo "<div class='success'>✅ Account activated!</div>";

        // Verify the changes
        echo "<h2>Verification</h2>";
        $sql_verify = "SELECT username, is_active, failed_login_attempts, locked_until 
                       FROM admin_users 
                       WHERE username = 'admin'";
        $user = $db->queryOne($sql_verify);

        if ($user) {
            echo "<div class='info'>";
            echo "<strong>Current Account Status:</strong><br>";
            echo "Username: <code>{$user['username']}</code><br>";
            echo "Active: <code>" . ($user['is_active'] ? 'Yes ✅' : 'No ❌') . "</code><br>";
            echo "Failed Attempts: <code>{$user['failed_login_attempts']}</code><br>";
            echo "Locked Until: <code>" . ($user['locked_until'] ? $user['locked_until'] : 'Not locked ✅') . "</code><br>";
            echo "</div>";

            if ($user['is_active'] && $user['failed_login_attempts'] == 0 && !$user['locked_until']) {
                echo "<div class='success'>";
                echo "<h3>🎉 Success! Account is Ready!</h3>";
                echo "<p>You can now login with:</p>";
                echo "<ul>";
                echo "<li>Username: <code>admin</code></li>";
                echo "<li>Password: <code>admin</code></li>";
                echo "</ul>";
                echo "</div>";
            }
        }

        echo "<hr>";
        echo "<a href='admin/login.php' class='btn'>Go to Login Page</a>";
        echo "<a href='unlock_admin.php' class='btn'>Check Status Again</a>";

    } else {
        // Show current status and unlock button
        echo "<h2>Current Account Status</h2>";

        $sql = "SELECT username, is_active, failed_login_attempts, locked_until 
                FROM admin_users 
                WHERE username = 'admin'";
        $user = $db->queryOne($sql);

        if ($user) {
            $is_locked = $user['locked_until'] && strtotime($user['locked_until']) > time();

            echo "<div class='" . ($is_locked ? 'error' : 'info') . "'>";
            echo "<strong>Account Details:</strong><br>";
            echo "Username: <code>{$user['username']}</code><br>";
            echo "Active: <code>" . ($user['is_active'] ? 'Yes ✅' : 'No ❌') . "</code><br>";
            echo "Failed Login Attempts: <code>{$user['failed_login_attempts']}</code><br>";
            echo "Locked Until: <code>" . ($user['locked_until'] ? $user['locked_until'] : 'Not locked ✅') . "</code><br>";

            if ($is_locked) {
                $time_remaining = strtotime($user['locked_until']) - time();
                $minutes = ceil($time_remaining / 60);
                echo "<br><strong>⚠️ Account is LOCKED for {$minutes} more minute(s)</strong>";
            }
            echo "</div>";

            // Check IP lockouts
            echo "<h2>IP Lockout Status</h2>";
            $sql_ip = "SELECT ip_address, COUNT(*) as failed_count 
                       FROM login_attempts 
                       WHERE success = 0 
                       AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                       GROUP BY ip_address";
            $ip_lockouts = $db->query($sql_ip);

            if (count($ip_lockouts) > 0) {
                echo "<div class='error'>";
                echo "<strong>IPs with Failed Attempts:</strong><br>";
                foreach ($ip_lockouts as $ip) {
                    echo "IP: <code>{$ip['ip_address']}</code> - Failed attempts: <code>{$ip['failed_count']}</code><br>";
                }
                echo "</div>";
            } else {
                echo "<div class='success'>✅ No IP lockouts detected</div>";
            }

            // Show unlock button
            echo "<hr>";
            echo "<h2>Actions</h2>";

            if ($is_locked || $user['failed_login_attempts'] > 0 || count($ip_lockouts) > 0) {
                echo "<div class='info'>";
                echo "<p><strong>Click the button below to unlock the account and clear all lockouts:</strong></p>";
                echo "</div>";
                echo "<a href='unlock_admin.php?action=unlock' class='btn btn-danger'>🔓 Unlock Admin Account Now</a>";
            } else {
                echo "<div class='success'>";
                echo "<p>✅ Account is not locked. You should be able to login!</p>";
                echo "</div>";
                echo "<a href='admin/login.php' class='btn'>Go to Login Page</a>";
            }

            echo "<a href='test_password.php' class='btn'>Run Full Diagnostic</a>";

        } else {
            echo "<div class='error'>";
            echo "❌ <strong>ERROR:</strong> Admin user not found in database!<br>";
            echo "Please import the database schema first.";
            echo "</div>";
            echo "<a href='test_password.php' class='btn'>Run Full Diagnostic</a>";
        }
    }

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>ERROR:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<h2>Manual SQL Commands (Alternative Method)</h2>";
echo "<p>If you prefer to run SQL commands manually in phpMyAdmin, use these:</p>";
echo "<pre>";
echo "-- Unlock admin account\n";
echo "UPDATE admin_users \n";
echo "SET locked_until = NULL, \n";
echo "    failed_login_attempts = 0,\n";
echo "    is_active = 1\n";
echo "WHERE username = 'admin';\n\n";
echo "-- Clear failed login attempts\n";
echo "DELETE FROM login_attempts WHERE success = 0;\n\n";
echo "-- Clear rate limits\n";
echo "DELETE FROM rate_limits;\n";
echo "</pre>";

echo "</body></html>";
?>