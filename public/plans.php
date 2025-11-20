<?php
/**
 * Plans & Pricing Page
 * 
 * Displays all available telecom plans and offers
 * 
 * @package TelecomWebsite
 * @version 1.0.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/functions.php';

// Start session for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get flash message if any
$flash = getFlashMessage();

// Page metadata
$page_title = 'Plans & Pricing - ' . SITE_NAME;
$page_description = 'Explore our flexible internet and mobile plans. Find the perfect package for your home or business.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($page_description); ?>">
    <meta name="keywords" content="internet plans, mobile plans, fiber internet, business internet, pricing">
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
                    <li><a href="plans.php" class="nav__link nav__link--active">Plans</a></li>
                    <li><a href="contact.php" class="nav__link">Contact</a></li>
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

    <!-- Flash Message -->
    <?php if ($flash): ?>
        <div class="container" style="margin-top: 1rem;">
            <div class="alert alert--<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <section class="section">
        <div class="container">
            <div class="section__header">
                <h1 class="section__title">Plans & Pricing</h1>
                <p class="section__subtitle">
                    Choose the perfect plan for your needs. No contracts, no hidden fees.
                </p>
            </div>
        </div>
    </section>

    <!-- Home Internet Plans -->
    <section class="section section--alt">
        <div class="container">
            <h2 class="section__title text-center mb-xl">Home Internet Plans</h2>

            <div class="grid grid--3">
                <!-- Home Basic -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Home Basic</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>29<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">100 Mbps Download</li>
                        <li class="plan-card__feature">20 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Free Router</li>
                        <li class="plan-card__feature">24/7 Support</li>
                        <li class="plan-card__feature">No Setup Fee</li>
                    </ul>
                    <a href="contact.php?plan=Home+Basic" class="btn btn--outline" style="width: 100%;">Get Started</a>
                </div>

                <!-- Home Pro (Featured) -->
                <div class="plan-card plan-card--featured">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Home Pro</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>49<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">500 Mbps Download</li>
                        <li class="plan-card__feature">100 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Free WiFi 6 Router</li>
                        <li class="plan-card__feature">Priority Support</li>
                        <li class="plan-card__feature">Free Installation</li>
                        <li class="plan-card__feature">1 Month Free Trial</li>
                    </ul>
                    <a href="contact.php?plan=Home+Pro" class="btn btn--primary" style="width: 100%;">Get Started</a>
                </div>

                <!-- Home Ultra -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Home Ultra</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>69<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">1 Gbps Download</li>
                        <li class="plan-card__feature">500 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Premium WiFi 6E Router</li>
                        <li class="plan-card__feature">VIP Support</li>
                        <li class="plan-card__feature">Free Installation</li>
                        <li class="plan-card__feature">Advanced Security Suite</li>
                    </ul>
                    <a href="contact.php?plan=Home+Ultra" class="btn btn--outline" style="width: 100%;">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Plans -->
    <section class="section">
        <div class="container">
            <h2 class="section__title text-center mb-xl">Business Solutions</h2>

            <div class="grid grid--3">
                <!-- Business Starter -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Business Starter</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>79<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">500 Mbps Download</li>
                        <li class="plan-card__feature">250 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Business Router</li>
                        <li class="plan-card__feature">Dedicated Support</li>
                        <li class="plan-card__feature">Static IP Address</li>
                        <li class="plan-card__feature">99.5% Uptime SLA</li>
                    </ul>
                    <a href="contact.php?plan=Business+Starter" class="btn btn--outline" style="width: 100%;">Contact
                        Sales</a>
                </div>

                <!-- Business Pro (Featured) -->
                <div class="plan-card plan-card--featured">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Business Pro</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>149<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">1 Gbps Download</li>
                        <li class="plan-card__feature">500 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Enterprise Router</li>
                        <li class="plan-card__feature">24/7 Priority Support</li>
                        <li class="plan-card__feature">5 Static IP Addresses</li>
                        <li class="plan-card__feature">99.9% Uptime SLA</li>
                        <li class="plan-card__feature">Managed Security</li>
                    </ul>
                    <a href="contact.php?plan=Business+Pro" class="btn btn--primary" style="width: 100%;">Contact
                        Sales</a>
                </div>

                <!-- Enterprise -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Enterprise</h3>
                        <div class="plan-card__price">
                            <span style="font-size: var(--font-size-xl);">Custom</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">Up to 10 Gbps</li>
                        <li class="plan-card__feature">Symmetric Upload/Download</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Custom Infrastructure</li>
                        <li class="plan-card__feature">Dedicated Account Manager</li>
                        <li class="plan-card__feature">Custom IP Allocation</li>
                        <li class="plan-card__feature">99.99% Uptime SLA</li>
                        <li class="plan-card__feature">Full Managed Services</li>
                    </ul>
                    <a href="contact.php?plan=Enterprise" class="btn btn--outline" style="width: 100%;">Contact
                        Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Plans -->
    <section class="section section--alt">
        <div class="container">
            <h2 class="section__title text-center mb-xl">Mobile Plans</h2>

            <div class="grid grid--3">
                <!-- Mobile Basic -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Mobile Basic</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>19<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">10 GB Data</li>
                        <li class="plan-card__feature">Unlimited Calls</li>
                        <li class="plan-card__feature">Unlimited SMS</li>
                        <li class="plan-card__feature">4G LTE Network</li>
                        <li class="plan-card__feature">Hotspot Enabled</li>
                    </ul>
                    <a href="contact.php?plan=Mobile+Basic" class="btn btn--outline" style="width: 100%;">Get
                        Started</a>
                </div>

                <!-- Mobile Plus -->
                <div class="plan-card plan-card--featured">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Mobile Plus</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>39<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">50 GB Data</li>
                        <li class="plan-card__feature">Unlimited Calls</li>
                        <li class="plan-card__feature">Unlimited SMS</li>
                        <li class="plan-card__feature">5G Network</li>
                        <li class="plan-card__feature">Hotspot Enabled</li>
                        <li class="plan-card__feature">International Roaming</li>
                    </ul>
                    <a href="contact.php?plan=Mobile+Plus" class="btn btn--primary" style="width: 100%;">Get Started</a>
                </div>

                <!-- Mobile Unlimited -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Mobile Unlimited</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>59<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Unlimited Calls</li>
                        <li class="plan-card__feature">Unlimited SMS</li>
                        <li class="plan-card__feature">5G+ Network</li>
                        <li class="plan-card__feature">Premium Hotspot</li>
                        <li class="plan-card__feature">Global Roaming</li>
                        <li class="plan-card__feature">Priority Network Access</li>
                    </ul>
                    <a href="contact.php?plan=Mobile+Unlimited" class="btn btn--outline" style="width: 100%;">Get
                        Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Bundle Offers -->
    <section class="section">
        <div class="container container--narrow text-center">
            <h2 class="section__title">Bundle & Save</h2>
            <p class="section__subtitle mb-xl">
                Combine internet and mobile plans to save up to 20% on your monthly bill.
            </p>
            <a href="contact.php?plan=Bundle" class="btn btn--primary btn--large">Explore Bundles</a>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section section--alt">
        <div class="container container--narrow">
            <h2 class="section__title text-center mb-xl">Frequently Asked Questions</h2>

            <div class="card mb-lg">
                <h4>Is there a contract?</h4>
                <p class="mb-0">No, all our plans are month-to-month with no long-term contracts. Cancel anytime without
                    penalties.</p>
            </div>

            <div class="card mb-lg">
                <h4>What's included in installation?</h4>
                <p class="mb-0">Professional installation includes router setup, network configuration, and testing.
                    Most installations are completed within 2-3 hours.</p>
            </div>

            <div class="card mb-lg">
                <h4>Can I upgrade or downgrade my plan?</h4>
                <p class="mb-0">Yes! You can change your plan anytime. Upgrades are instant, and downgrades take effect
                    at the start of your next billing cycle.</p>
            </div>

            <div class="card">
                <h4>Do you offer business support?</h4>
                <p class="mb-0">Absolutely. Business plans include dedicated support teams, SLA guarantees, and optional
                    managed services.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container container--narrow text-center">
            <h2 class="section__title">Ready to Get Connected?</h2>
            <p class="section__subtitle mb-xl">
                Have questions? Our sales team is here to help you find the perfect plan.
            </p>
            <a href="contact.php" class="btn btn--primary btn--large">Contact Us</a>
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