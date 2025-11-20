<?php
/**
 * Admin Dashboard
 * 
 * Manage contact form submissions
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

// Require authentication
requireAuth('login.php');

// Get current admin
$admin = getCurrentAdmin();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid security token.';
        $message_type = 'error';
    } else {
        $contact_id = intval($_POST['contact_id'] ?? 0);

        switch ($action) {
            case 'update_status':
                $status = $_POST['status'] ?? '';
                if (updateContactStatus($contact_id, $status)) {
                    $message = 'Status updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update status.';
                    $message_type = 'error';
                }
                break;

            case 'delete':
                if (deleteContact($contact_id)) {
                    $message = 'Contact deleted successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete contact.';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Handle logout
if ($action === 'logout') {
    logout();
    redirect('login.php');
}

// Handle CSV export
if ($action === 'export') {
    $filters = [
        'status' => $_GET['status_filter'] ?? '',
        'search' => $_GET['search'] ?? ''
    ];

    $csv = exportContactsToCSV($filters);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts_' . date('Y-m-d') . '.csv"');
    echo $csv;
    exit;
}

// Get filters
$status_filter = $_GET['status_filter'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));

// Get contacts
$filters = [];
if ($status_filter)
    $filters['status'] = $status_filter;
if ($search)
    $filters['search'] = $search;

$result = getContacts($filters, $page);
$contacts = $result['contacts'];
$total = $result['total'];
$total_pages = $result['total_pages'];

// Get statistics
$stats = getContactStats();

$page_title = 'Admin Dashboard - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e($page_title); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="../public/assets/css/style.css">

    <style>
        .admin-header {
            background-color: var(--bg-card);
            box-shadow: var(--shadow-sm);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
        }

        .admin-header__container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-title {
            font-size: var(--font-size-xl);
            margin: 0;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .stat-card {
            background-color: var(--bg-card);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .stat-card__value {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            color: var(--primary);
            margin-bottom: var(--spacing-xs);
        }

        .stat-card__label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .filters {
            background-color: var(--bg-card);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-xl);
            display: flex;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            align-items: end;
        }

        .filters__group {
            flex: 1;
            min-width: 200px;
        }

        .table-container {
            background-color: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: var(--spacing-md);
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: rgba(44, 122, 123, 0.1);
            font-weight: 600;
            color: var(--text-primary);
        }

        .table tr:hover {
            background-color: rgba(44, 122, 123, 0.05);
        }

        .badge {
            display: inline-block;
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .badge--new {
            background-color: #BEE3F8;
            color: #2C5282;
        }

        .badge--replied {
            background-color: #C6F6D5;
            color: #22543D;
        }

        .badge--closed {
            background-color: #E2E8F0;
            color: #4A5568;
        }

        [data-theme="dark"] .badge--new {
            background-color: rgba(49, 130, 206, 0.3);
            color: #BEE3F8;
        }

        [data-theme="dark"] .badge--replied {
            background-color: rgba(56, 161, 105, 0.3);
            color: #C6F6D5;
        }

        [data-theme="dark"] .badge--closed {
            background-color: rgba(74, 85, 104, 0.3);
            color: #E2E8F0;
        }

        .actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-lg);
        }

        .pagination__btn {
            padding: var(--spacing-sm) var(--spacing-md);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            background-color: var(--bg-card);
            color: var(--text-primary);
            text-decoration: none;
            transition: all var(--transition-fast);
        }

        .pagination__btn:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination__btn--active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: var(--z-modal);
            align-items: center;
            justify-content: center;
        }

        .modal--open {
            display: flex;
        }

        .modal__content {
            background-color: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: var(--spacing-2xl);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .filters {
                flex-direction: column;
            }

            .filters__group {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="admin-header__container">
            <h1 class="admin-title">📊 Admin Dashboard</h1>
            <div class="admin-user">
                <span>👤 <?php echo e($admin['username']); ?></span>
                <button class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-toggle__icon">🌙</span>
                </button>
                <a href="?action=logout" class="btn btn--small btn--outline">Logout</a>
            </div>
        </div>
    </header>

    <div class="container container--wide">
        <!-- Flash Message -->
        <?php if ($message): ?>
            <div class="alert alert--<?php echo e($message_type); ?>">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card__value"><?php echo e($stats['total_contacts'] ?? 0); ?></div>
                <div class="stat-card__label">Total Contacts</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?php echo e($stats['new_contacts'] ?? 0); ?></div>
                <div class="stat-card__label">New Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?php echo e($stats['replied_contacts'] ?? 0); ?></div>
                <div class="stat-card__label">Replied</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?php echo e($stats['today_contacts'] ?? 0); ?></div>
                <div class="stat-card__label">Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value"><?php echo e($stats['week_contacts'] ?? 0); ?></div>
                <div class="stat-card__label">This Week</div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="dashboard.php" class="filters">
            <div class="filters__group">
                <label for="search" class="form__label">Search</label>
                <input type="text" id="search" name="search" class="form__input" placeholder="Name, email, subject..."
                    value="<?php echo e($search); ?>">
            </div>

            <div class="filters__group">
                <label for="status_filter" class="form__label">Status</label>
                <select id="status_filter" name="status_filter" class="form__select">
                    <option value="">All Statuses</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>

            <div class="filters__group">
                <button type="submit" class="btn btn--primary">Filter</button>
                <a href="dashboard.php" class="btn btn--outline">Reset</a>
            </div>

            <div class="filters__group">
                <a href="?action=export&status_filter=<?php echo e($status_filter); ?>&search=<?php echo e($search); ?>"
                    class="btn btn--outline">
                    Export CSV
                </a>
            </div>
        </form>

        <!-- Contacts Table -->
        <div class="table-container">
            <?php if (empty($contacts)): ?>
                <div style="padding: var(--spacing-2xl); text-align: center; color: var(--text-secondary);">
                    No contacts found.
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Plan Interest</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo e($contact['id']); ?></td>
                                <td><?php echo e($contact['name']); ?></td>
                                <td><a href="mailto:<?php echo e($contact['email']); ?>"><?php echo e($contact['email']); ?></a>
                                </td>
                                <td><?php echo e($contact['subject']); ?></td>
                                <td><?php echo e($contact['plan_interest'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge badge--<?php echo e($contact['status']); ?>">
                                        <?php echo e(ucfirst($contact['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo e(timeAgo($contact['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <button onclick="viewContact(<?php echo e($contact['id']); ?>)"
                                            class="btn btn--small btn--outline" title="View details">
                                            👁️
                                        </button>
                                        <button onclick="deleteContact(<?php echo e($contact['id']); ?>)"
                                            class="btn btn--small btn--outline" title="Delete"
                                            style="color: #E53E3E; border-color: #E53E3E;">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&status_filter=<?php echo e($status_filter); ?>&search=<?php echo e($search); ?>"
                                class="pagination__btn">← Previous</a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status_filter=<?php echo e($status_filter); ?>&search=<?php echo e($search); ?>"
                                class="pagination__btn <?php echo $i === $page ? 'pagination__btn--active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&status_filter=<?php echo e($status_filter); ?>&search=<?php echo e($search); ?>"
                                class="pagination__btn">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Contact Modal -->
    <div id="viewModal" class="modal">
        <div class="modal__content">
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../public/assets/js/main.js"></script>
    <script>
        // View contact details
        function viewContact(id) {
            fetch(`ajax_get_contact.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const contact = data.contact;
                        const html = `
                            <h2>Contact Details</h2>
                            <p><strong>Name:</strong> ${escapeHtml(contact.name)}</p>
                            <p><strong>Email:</strong> <a href="mailto:${escapeHtml(contact.email)}">${escapeHtml(contact.email)}</a></p>
                            <p><strong>Phone:</strong> ${contact.phone ? escapeHtml(contact.phone) : 'N/A'}</p>
                            <p><strong>Subject:</strong> ${escapeHtml(contact.subject)}</p>
                            <p><strong>Plan Interest:</strong> ${contact.plan_interest ? escapeHtml(contact.plan_interest) : 'N/A'}</p>
                            <p><strong>Message:</strong><br>${escapeHtml(contact.message).replace(/\n/g, '<br>')}</p>
                            <p><strong>IP Address:</strong> ${escapeHtml(contact.ip_address)}</p>
                            <p><strong>Submitted:</strong> ${escapeHtml(contact.created_at)}</p>
                            
                            <form method="POST" action="?action=update_status" style="margin-top: 1.5rem;">
                                <input type="hidden" name="csrf_token" value="<?php echo e(generateCSRFToken()); ?>">
                                <input type="hidden" name="contact_id" value="${contact.id}">
                                <div class="form__group">
                                    <label class="form__label">Update Status</label>
                                    <select name="status" class="form__select">
                                        <option value="new" ${contact.status === 'new' ? 'selected' : ''}>New</option>
                                        <option value="replied" ${contact.status === 'replied' ? 'selected' : ''}>Replied</option>
                                        <option value="closed" ${contact.status === 'closed' ? 'selected' : ''}>Closed</option>
                                    </select>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="submit" class="btn btn--primary">Update Status</button>
                                    <button type="button" onclick="closeModal()" class="btn btn--outline">Close</button>
                                </div>
                            </form>
                        `;
                        document.getElementById('modalContent').innerHTML = html;
                        document.getElementById('viewModal').classList.add('modal--open');
                    } else {
                        alert('Error loading contact details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading contact details');
                });
        }

        // Delete contact
        function deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '?action=delete';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo e(generateCSRFToken()); ?>">
                    <input type="hidden" name="contact_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('viewModal').classList.remove('modal--open');
        }

        // Close modal on background click
        document.getElementById('viewModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>