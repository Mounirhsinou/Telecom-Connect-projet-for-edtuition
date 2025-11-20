<?php
/**
 * AJAX Get Contact Details
 * 
 * Returns contact details as JSON for admin dashboard modal
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Require authentication
startSecureSession();
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get contact ID
$contact_id = intval($_GET['id'] ?? 0);

if ($contact_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid contact ID']);
    exit;
}

// Get contact from database
$contact = getContactById($contact_id);

if (!$contact) {
    echo json_encode(['success' => false, 'message' => 'Contact not found']);
    exit;
}

// Return contact data
echo json_encode([
    'success' => true,
    'contact' => $contact
]);
