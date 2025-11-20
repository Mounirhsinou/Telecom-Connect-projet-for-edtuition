<?php
/**
 * Contact Page
 * 
 * Contact form with database storage and validation
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$errors = [];
$form_data = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors['csrf'] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        // Get form data
        $form_data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'subject' => $_POST['subject'] ?? '',
            'message' => $_POST['message'] ?? '',
            'plan_interest' => $_POST['plan_interest'] ?? '',
            'website' => $_POST['website'] ?? '' // Honeypot
        ];

        // Validate form
        $validation = validateContactForm($form_data);

        if (!$validation['valid']) {
            $errors = $validation['errors'];
        } else {
            // Check rate limit
            $ip_address = getClientIP();

            if (!checkRateLimit($ip_address)) {
                $errors['rate_limit'] = 'Too many submissions. Please try again later.';
            } else {
                // Save to database
                $contact_id = saveContactSubmission($form_data);

                if ($contact_id) {
                    $success = true;

                    // Set flash message
                    setFlashMessage('success', 'Thank you for contacting us! We will get back to you soon.');

                    // Redirect to prevent form resubmission
                    header('Location: contact.php?success=1');
                    exit;
                } else {
                    $errors['database'] = 'An error occurred while saving your message. Please try again.';
                }
            }
        }
    }
}

// Get flash message
$flash = getFlashMessage();

// Get plan from URL if present
$plan_interest = $_GET['plan'] ?? '';

// Page metadata
$page_title = 'Contact Us - ' . SITE_NAME;
$page_description = 'Get in touch with our team. We\'re here to help with any questions about our services.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($page_description); ?>">
    <meta name="keywords" content="contact, support, customer service, telecom support">
    <title><?php echo e($page_title); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📡</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .form__input--error,
        .form__textarea--error,
        .form__select--error {
            border-color: #E53E3E;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header__container">
            <a href="index.php" class="header__logo">
                <span class="header__logo-icon">📡</span>
                <span><?php echo e(SITE_NAME); ?></span>
            </a>

            <nav class="nav">
                <ul class="nav__list">
                    <li><a href="index.php" class="nav__link">Home</a></li>
                    <li><a href="plans.php" class="nav__link">Plans</a></li>
                    <li><a href="contact.php" class="nav__link nav__link--active">Contact</a></li>
                </ul>

                <button class="theme-toggle" aria-label="Toggle dark mode">
                    <span class="theme-toggle__icon">🌙</span>
                </button>

                <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                    ☰
                </button>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <section class="section">
        <div class="container container--narrow">
            <div class="section__header">
                <h1 class="section__title">Contact Us</h1>
                <p class="section__subtitle">
                    Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as
                    possible.
                </p>
            </div>

            <!-- Flash Message -->
            <?php if ($flash): ?>
                <div class="alert alert--<?php echo e($flash['type']); ?>">
                    <?php echo e($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- General Errors -->
            <?php if (!empty($errors['csrf']) || !empty($errors['rate_limit']) || !empty($errors['database'])): ?>
                <div class="alert alert--error">
                    <?php
                    if (!empty($errors['csrf']))
                        echo e($errors['csrf']);
                    elseif (!empty($errors['rate_limit']))
                        echo e($errors['rate_limit']);
                    elseif (!empty($errors['database']))
                        echo e($errors['database']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Contact Form -->
            <div class="card">
                <form id="contact-form" method="POST" action="contact.php" class="form" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo e(generateCSRFToken()); ?>">

                    <!-- Honeypot (anti-spam) -->
                    <div class="form__honeypot">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- Name -->
                    <div class="form__group">
                        <label for="name" class="form__label form__label--required">Full Name</label>
                        <input type="text" id="name" name="name"
                            class="form__input <?php echo !empty($errors['name']) ? 'form__input--error' : ''; ?>"
                            value="<?php echo e($form_data['name'] ?? ''); ?>" required minlength="2" maxlength="100"
                            aria-required="true"
                            aria-invalid="<?php echo !empty($errors['name']) ? 'true' : 'false'; ?>">
                        <?php if (!empty($errors['name'])): ?>
                            <span class="form__error" role="alert"><?php echo e($errors['name']); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form__group">
                        <label for="email" class="form__label form__label--required">Email Address</label>
                        <input type="email" id="email" name="email"
                            class="form__input <?php echo !empty($errors['email']) ? 'form__input--error' : ''; ?>"
                            value="<?php echo e($form_data['email'] ?? ''); ?>" required aria-required="true"
                            aria-invalid="<?php echo !empty($errors['email']) ? 'true' : 'false'; ?>">
                        <?php if (!empty($errors['email'])): ?>
                            <span class="form__error" role="alert"><?php echo e($errors['email']); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Phone -->
                    <div class="form__group">
                        <label for="phone" class="form__label">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                            class="form__input <?php echo !empty($errors['phone']) ? 'form__input--error' : ''; ?>"
                            value="<?php echo e($form_data['phone'] ?? ''); ?>" placeholder="+212 6XX-XXXXXX"
                            aria-invalid="<?php echo !empty($errors['phone']) ? 'true' : 'false'; ?>">
                        <?php if (!empty($errors['phone'])): ?>
                            <span class="form__error" role="alert"><?php echo e($errors['phone']); ?></span>
                        <?php else: ?>
                            <span class="form__help">Optional - Include country code for international numbers</span>
                        <?php endif; ?>
                    </div>

                    <!-- Subject -->
                    <div class="form__group">
                        <label for="subject" class="form__label form__label--required">Subject</label>
                        <input type="text" id="subject" name="subject"
                            class="form__input <?php echo !empty($errors['subject']) ? 'form__input--error' : ''; ?>"
                            value="<?php echo e($form_data['subject'] ?? ''); ?>" required minlength="3" maxlength="200"
                            aria-required="true"
                            aria-invalid="<?php echo !empty($errors['subject']) ? 'true' : 'false'; ?>">
                        <?php if (!empty($errors['subject'])): ?>
                            <span class="form__error" role="alert"><?php echo e($errors['subject']); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Plan Interest -->
                    <div class="form__group">
                        <label for="plan_interest" class="form__label">Interested In</label>
                        <select id="plan_interest" name="plan_interest" class="form__select">
                            <option value="">Select a plan (optional)</option>
                            <optgroup label="Home Internet">
                                <option value="Home Basic" <?php echo ($plan_interest === 'Home Basic') ? 'selected' : ''; ?>>Home Basic - $29/mo</option>
                                <option value="Home Pro" <?php echo ($plan_interest === 'Home Pro') ? 'selected' : ''; ?>>
                                    Home Pro - $49/mo</option>
                                <option value="Home Ultra" <?php echo ($plan_interest === 'Home Ultra') ? 'selected' : ''; ?>>Home Ultra - $69/mo</option>
                            </optgroup>
                            <optgroup label="Business">
                                <option value="Business Starter" <?php echo ($plan_interest === 'Business Starter') ? 'selected' : ''; ?>>Business Starter - $79/mo</option>
                                <option value="Business Pro" <?php echo ($plan_interest === 'Business Pro') ? 'selected' : ''; ?>>Business Pro - $149/mo</option>
                                <option value="Enterprise" <?php echo ($plan_interest === 'Enterprise') ? 'selected' : ''; ?>>Enterprise - Custom</option>
                            </optgroup>
                            <optgroup label="Mobile">
                                <option value="Mobile Basic" <?php echo ($plan_interest === 'Mobile Basic') ? 'selected' : ''; ?>>Mobile Basic - $19/mo</option>
                                <option value="Mobile Plus" <?php echo ($plan_interest === 'Mobile Plus') ? 'selected' : ''; ?>>Mobile Plus - $39/mo</option>
                                <option value="Mobile Unlimited" <?php echo ($plan_interest === 'Mobile Unlimited') ? 'selected' : ''; ?>>Mobile Unlimited - $59/mo</option>
                            </optgroup>
                            <option value="Bundle" <?php echo ($plan_interest === 'Bundle') ? 'selected' : ''; ?>>Bundle
                                Package</option>
                            <option value="Other">Other / General Inquiry</option>
                        </select>
                    </div>

                    <!-- Message -->
                    <div class="form__group">
                        <label for="message" class="form__label form__label--required">Message</label>
                        <textarea id="message" name="message"
                            class="form__textarea <?php echo !empty($errors['message']) ? 'form__textarea--error' : ''; ?>"
                            required minlength="10" maxlength="5000" aria-required="true"
                            aria-invalid="<?php echo !empty($errors['message']) ? 'true' : 'false'; ?>"><?php echo e($form_data['message'] ?? ''); ?></textarea>
                        <?php if (!empty($errors['message'])): ?>
                            <span class="form__error" role="alert"><?php echo e($errors['message']); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn--primary form__submit">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Contact Info -->
    <section class="section section--alt">
        <div class="container">
            <h2 class="section__title text-center mb-xl">Other Ways to Reach Us</h2>

            <div class="grid grid--3">
                <div class="card text-center">
                    <div style="font-size: 48px; margin-bottom: 1rem;">📞</div>
                    <h3 class="card__title">Phone</h3>
                    <p>Sales: +212 5XX-XXXXXX</p>
                    <p>Support: +212 5XX-XXXXXX</p>
                    <p class="mb-0"><small style="color: var(--text-muted);">Mon-Fri: 8AM-8PM<br>Sat-Sun:
                            9AM-6PM</small></p>
                </div>

                <div class="card text-center">
                    <div style="font-size: 48px; margin-bottom: 1rem;">✉️</div>
                    <h3 class="card__title">Email</h3>
                    <p>Sales: sales@telecomconnect.com</p>
                    <p>Support: support@telecomconnect.com</p>
                    <p class="mb-0"><small style="color: var(--text-muted);">Response within 24 hours</small></p>
                </div>

                <div class="card text-center">
                    <div style="font-size: 48px; margin-bottom: 1rem;">📍</div>
                    <h3 class="card__title">Visit Us</h3>
                    <p>123 Boulevard Mohammed V</p>
                    <p>Casablanca 20000</p>
                    <p class="mb-0"><small style="color: var(--text-muted);">Mon-Fri: 9AM-6PM</small></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div>
                    <h4 class="footer__section-title"><?php echo e(SITE_NAME); ?></h4>
                    <p style="color: var(--text-secondary);">
                        Leading telecommunications provider offering high-speed internet, mobile plans, and business
                        solutions.
                    </p>
                </div>

                <div>
                    <h4 class="footer__section-title">Quick Links</h4>
                    <ul class="footer__list">
                        <li class="footer__list-item"><a href="index.php" class="footer__link">Home</a></li>
                        <li class="footer__list-item"><a href="plans.php" class="footer__link">Plans & Pricing</a></li>
                        <li class="footer__list-item"><a href="contact.php" class="footer__link">Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer__section-title">Support</h4>
                    <ul class="footer__list">
                        <li class="footer__list-item"><a href="#" class="footer__link">Help Center</a></li>
                        <li class="footer__list-item"><a href="#" class="footer__link">Network Status</a></li>
                        <li class="footer__list-item"><a href="#" class="footer__link">FAQs</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer__section-title">Contact</h4>
                    <ul class="footer__list">
                        <li class="footer__list-item" style="color: var(--text-secondary);">📞 +212 5XX-XXXXXX</li>
                        <li class="footer__list-item" style="color: var(--text-secondary);">✉️
                            <?php echo e(SITE_EMAIL); ?></li>
                        <li class="footer__list-item" style="color: var(--text-secondary);">📍 Casablanca, Morocco</li>
                    </ul>
                </div>
            </div>

            <div class="footer__bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo e(SITE_NAME); ?>. All rights reserved. | Built with ❤️ for
                    learning</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>

</html>