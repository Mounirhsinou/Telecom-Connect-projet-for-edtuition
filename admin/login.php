<?php
/**
 * Admin Login Page
 * 
 * Secure admin authentication
 * 
 * @package TelecomWebsite
 * @version 2.0.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

startSecureSession();

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Initialize variables
$errors = [];
$username = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors['csrf'] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Attempt authentication
        $result = authenticateAdmin($username, $password);

        if ($result['success']) {
            // Redirect to dashboard
            redirect('dashboard.php');
        } else {
            $errors['login'] = $result['message'];
        }
    }
}

$page_title = 'Admin Login - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e($page_title); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔐</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="../public/assets/css/style.css">
    
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: var(--spacing-lg);
        }

        .login-card {
            background-color: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-xl);
        }

        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .login-logo {
            font-size: 64px;
            margin-bottom: var(--spacing-md);
        }

        .login-title {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--spacing-sm);
            color: var(--text-primary);
        }

        .login-subtitle {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .back-link {
            text-align: center;
            margin-top: var(--spacing-lg);
        }

        .back-link a {
            color: white;
            text-decoration: none;
            font-size: var(--font-size-sm);
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">🔐</div>
                <h1 class="login-title">Admin Login</h1>
                <p class="login-subtitle"><?php echo e(SITE_NAME); ?> Administration</p>
            </div>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert--error">
                <?php 
                if (!empty($errors['csrf'])) echo e($errors['csrf']);
                elseif (!empty($errors['login'])) echo e($errors['login']);
                ?>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="login.php" class="form">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo e(generateCSRFToken()); ?>">

                <!-- Username -->
                <div class="form__group">
                    <label for="username" class="form__label form__label--required">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form__input"
                        value="<?php echo e($username); ?>"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>

                <!-- Password -->
                <div class="form__group">
                    <label for="password" class="form__label form__label--required">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form__input"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn--primary" style="width: 100%;">
                    Login
                </button>
            </form>

            <!-- Default Credentials Info (Development Only) -->
            <?php if (ENVIRONMENT === 'development'): ?>
            <div class="alert alert--info" style="margin-top: var(--spacing-lg); font-size: var(--font-size-sm);">
                <strong>Default Credentials (Testing Only):</strong><br>
                Username: <code>admin</code><br>
                Password: <code>admin</code><br>
                <small>⚠️ Change these in production!</small>
            </div>
            <?php endif; ?>
        </div>

        <!-- Back to Site Link -->
        <div class="back-link">
            <a href="../public/index.php">← Back to Website</a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../public/assets/js/main.js"></script>
</body>
</html>