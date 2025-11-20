<?php
/**
 * Helper Functions
 * 
 * Common utility functions used throughout the application.
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool Verification result
 */
function verifyCSRFToken($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input string
 * 
 * @param string $input Input string
 * @return string Sanitized string
 */
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    return $input;
}

/**
 * Escape output for HTML display (XSS prevention)
 * 
 * @param string $string String to escape
 * @return string Escaped string
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * 
 * @param string $email Email address
 * @return bool Validation result
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (international format)
 * 
 * @param string $phone Phone number
 * @return bool Validation result
 */
function validatePhone($phone)
{
    // Remove spaces, dashes, parentheses
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

    // Check if it contains only digits and optional + at start
    return preg_match('/^\+?[0-9]{8,15}$/', $phone);
}

/**
 * Format phone number for display
 * 
 * @param string $phone Phone number
 * @return string Formatted phone number
 */
function formatPhone($phone)
{
    // Remove all non-numeric characters except +
    $phone = preg_replace('/[^0-9\+]/', '', $phone);
    return $phone;
}

/**
 * Validate contact form data
 * 
 * @param array $data Form data
 * @return array Validation result with 'valid' and 'errors' keys
 */
function validateContactForm($data)
{
    $errors = [];

    // Name validation
    if (empty($data['name'])) {
        $errors['name'] = 'Name is required.';
    } elseif (strlen($data['name']) < 2) {
        $errors['name'] = 'Name must be at least 2 characters.';
    } elseif (strlen($data['name']) > 100) {
        $errors['name'] = 'Name must not exceed 100 characters.';
    }

    // Email validation
    if (empty($data['email'])) {
        $errors['email'] = 'Email is required.';
    } elseif (!validateEmail($data['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Phone validation (optional but validate if provided)
    if (!empty($data['phone']) && !validatePhone($data['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    // Subject validation
    if (empty($data['subject'])) {
        $errors['subject'] = 'Subject is required.';
    } elseif (strlen($data['subject']) < 3) {
        $errors['subject'] = 'Subject must be at least 3 characters.';
    } elseif (strlen($data['subject']) > 200) {
        $errors['subject'] = 'Subject must not exceed 200 characters.';
    }

    // Message validation
    if (empty($data['message'])) {
        $errors['message'] = 'Message is required.';
    } elseif (strlen($data['message']) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    } elseif (strlen($data['message']) > 5000) {
        $errors['message'] = 'Message must not exceed 5000 characters.';
    }

    // Honeypot check (anti-spam)
    if (!empty($data['website'])) {
        $errors['spam'] = 'Spam detected.';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Check rate limit for contact form submissions
 * 
 * @param string $ip_address IP address
 * @return bool True if within limit, false if exceeded
 */
function checkRateLimit($ip_address)
{
    if (!ENABLE_RATE_LIMIT) {
        return true;
    }

    $db = Database::getInstance();

    // Clean expired rate limits first
    $sql = "DELETE FROM rate_limits WHERE expires_at < NOW()";
    $db->execute($sql);

    // Check current rate limit
    $sql = "SELECT attempts FROM rate_limits 
            WHERE ip_address = :ip_address 
            AND action = 'contact_form' 
            AND expires_at > NOW()";

    $result = $db->queryOne($sql, ['ip_address' => $ip_address]);

    if ($result) {
        // Update existing record
        if ($result['attempts'] >= RATE_LIMIT_MAX) {
            return false; // Rate limit exceeded
        }

        $sql = "UPDATE rate_limits 
                SET attempts = attempts + 1 
                WHERE ip_address = :ip_address 
                AND action = 'contact_form'";

        $db->execute($sql, ['ip_address' => $ip_address]);
    } else {
        // Create new record
        $sql = "INSERT INTO rate_limits (ip_address, action, attempts, expires_at) 
                VALUES (:ip_address, 'contact_form', 1, DATE_ADD(NOW(), INTERVAL :window SECOND))";

        $db->execute($sql, [
            'ip_address' => $ip_address,
            'window' => RATE_LIMIT_WINDOW
        ]);
    }

    return true;
}

/**
 * Save contact form submission to database
 * 
 * @param array $data Form data
 * @return int|false Insert ID or false on failure
 */
function saveContactSubmission($data)
{
    $db = Database::getInstance();

    try {
        $sql = "INSERT INTO contacts (
                    name, email, phone, subject, message, 
                    plan_interest, ip_address, user_agent
                ) VALUES (
                    :name, :email, :phone, :subject, :message, 
                    :plan_interest, :ip_address, :user_agent
                )";

        $params = [
            'name' => sanitizeInput($data['name']),
            'email' => sanitizeInput($data['email']),
            'phone' => !empty($data['phone']) ? formatPhone($data['phone']) : null,
            'subject' => sanitizeInput($data['subject']),
            'message' => sanitizeInput($data['message']),
            'plan_interest' => !empty($data['plan_interest']) ? sanitizeInput($data['plan_interest']) : null,
            'ip_address' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];

        $db->execute($sql, $params);
        return $db->lastInsertId();

    } catch (Exception $e) {
        error_log('Error saving contact submission: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get all contacts with optional filtering and pagination
 * 
 * @param array $filters Filters (status, search, etc.)
 * @param int $page Page number
 * @param int $per_page Items per page
 * @return array Contacts and pagination info
 */
function getContacts($filters = [], $page = 1, $per_page = ITEMS_PER_PAGE)
{
    $db = Database::getInstance();

    $where = [];
    $params = [];

    // Status filter
    if (!empty($filters['status'])) {
        $where[] = "status = :status";
        $params['status'] = $filters['status'];
    }

    // Search filter
    if (!empty($filters['search'])) {
        $search = '%' . $db->escapeLike($filters['search']) . '%';
        $where[] = "(name LIKE :search OR email LIKE :search OR subject LIKE :search OR message LIKE :search)";
        $params['search'] = $search;
    }

    // Build WHERE clause
    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $count_sql = "SELECT COUNT(*) as total FROM contacts $where_sql";
    $count_result = $db->queryOne($count_sql, $params);
    $total = $count_result['total'];

    // Calculate pagination
    $total_pages = ceil($total / $per_page);
    $offset = ($page - 1) * $per_page;

    // Get contacts
    $sql = "SELECT * FROM contacts 
            $where_sql 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset";

    $params['limit'] = $per_page;
    $params['offset'] = $offset;

    $contacts = $db->query($sql, $params);

    return [
        'contacts' => $contacts,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => $total_pages
    ];
}

/**
 * Get contact by ID
 * 
 * @param int $id Contact ID
 * @return array|false Contact data or false
 */
function getContactById($id)
{
    $db = Database::getInstance();

    $sql = "SELECT * FROM contacts WHERE id = :id LIMIT 1";
    return $db->queryOne($sql, ['id' => $id]);
}

/**
 * Update contact status
 * 
 * @param int $id Contact ID
 * @param string $status New status
 * @return bool Success status
 */
function updateContactStatus($id, $status)
{
    $db = Database::getInstance();

    $allowed_statuses = ['new', 'replied', 'closed'];
    if (!in_array($status, $allowed_statuses)) {
        return false;
    }

    $sql = "UPDATE contacts SET status = :status WHERE id = :id";
    return $db->execute($sql, ['id' => $id, 'status' => $status]);
}

/**
 * Delete contact
 * 
 * @param int $id Contact ID
 * @return bool Success status
 */
function deleteContact($id)
{
    $db = Database::getInstance();

    $sql = "DELETE FROM contacts WHERE id = :id";
    return $db->execute($sql, ['id' => $id]);
}

/**
 * Get contact statistics
 * 
 * @return array Statistics
 */
function getContactStats()
{
    $db = Database::getInstance();

    $sql = "SELECT * FROM view_contact_stats LIMIT 1";
    return $db->queryOne($sql);
}

/**
 * Export contacts to CSV
 * 
 * @param array $filters Filters
 * @return string CSV content
 */
function exportContactsToCSV($filters = [])
{
    $result = getContacts($filters, 1, 10000); // Get all matching contacts
    $contacts = $result['contacts'];

    // Create CSV content
    $csv = "ID,Name,Email,Phone,Subject,Plan Interest,Status,Created At\n";

    foreach ($contacts as $contact) {
        $csv .= sprintf(
            "%d,%s,%s,%s,%s,%s,%s,%s\n",
            $contact['id'],
            '"' . str_replace('"', '""', $contact['name']) . '"',
            '"' . str_replace('"', '""', $contact['email']) . '"',
            '"' . str_replace('"', '""', $contact['phone'] ?? '') . '"',
            '"' . str_replace('"', '""', $contact['subject']) . '"',
            '"' . str_replace('"', '""', $contact['plan_interest'] ?? '') . '"',
            $contact['status'],
            $contact['created_at']
        );
    }

    return $csv;
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y H:i')
{
    return date($format, strtotime($date));
}

/**
 * Get time ago string (e.g., "2 hours ago")
 * 
 * @param string $datetime Datetime string
 * @return string Time ago string
 */
function timeAgo($datetime)
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, info, warning)
 * @param string $message Message text
 */
function setFlashMessage($type, $message)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message or null
 */
function getFlashMessage()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }

    return null;
}

/**
 * Get client IP address (handles proxies)
 * Duplicate from auth.php for convenience
 * 
 * @return string IP address
 */
if (!function_exists('getClientIP')) {
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
}

/**
 * Generate pagination HTML
 * 
 * @param int $current_page Current page
 * @param int $total_pages Total pages
 * @param string $base_url Base URL for pagination links
 * @return string Pagination HTML
 */
function generatePagination($current_page, $total_pages, $base_url)
{
    if ($total_pages <= 1) {
        return '';
    }

    $html = '<div class="pagination">';

    // Previous button
    if ($current_page > 1) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page - 1) . '" class="pagination__btn">← Previous</a>';
    }

    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $current_page ? ' pagination__btn--active' : '';
        $html .= '<a href="' . $base_url . '&page=' . $i . '" class="pagination__btn' . $active . '">' . $i . '</a>';
    }

    // Next button
    if ($current_page < $total_pages) {
        $html .= '<a href="' . $base_url . '&page=' . ($current_page + 1) . '" class="pagination__btn">Next →</a>';
    }

    $html .= '</div>';

    return $html;
}
