<?php
/**
 * Homepage
 * 
 * Main landing page for the telecom website
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
$page_title = 'Home - ' . SITE_NAME;
$page_description = 'Leading telecommunications provider offering high-speed internet, mobile plans, and business solutions.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($page_description); ?>">
    <meta name="keywords" content="telecom, internet, mobile, fiber, business solutions">
    <meta name="author" content="<?php echo e(SITE_NAME); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(SITE_URL); ?>">
    <meta property="og:title" content="<?php echo e($page_title); ?>">
    <meta property="og:description" content="<?php echo e($page_description); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo e(SITE_URL); ?>">
    <meta property="twitter:title" content="<?php echo e($page_title); ?>">
    <meta property="twitter:description" content="<?php echo e($page_description); ?>">

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
                    <li><a href="index.php" class="nav__link nav__link--active">Home</a></li>
                    <li><a href="plans.php" class="nav__link">Plans</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero__container">
            <div class="hero__content">
                <h1 class="hero__title">
                    Connect to the Future with High-Speed Internet
                </h1>
                <p class="hero__subtitle">
                    Experience blazing-fast fiber internet, reliable mobile networks, and innovative business solutions
                    tailored to your needs.
                </p>
                <div class="hero__cta">
                    <a href="plans.php" class="btn btn--primary btn--large">
                        View Plans
                    </a>
                    <a href="contact.php" class="btn btn--secondary btn--large">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">Why Choose Us?</h2>
                <p class="section__subtitle">
                    We provide cutting-edge telecommunications services with unmatched reliability and customer support.
                </p>
            </div>

            <div class="grid grid--3">
                <div class="feature fade-in-up">
                    <div class="feature__icon">⚡</div>
                    <h3 class="feature__title">Lightning Fast</h3>
                    <p class="feature__description">
                        Experience speeds up to 1 Gbps with our fiber-optic network. Stream, game, and work without
                        interruption.
                    </p>
                </div>

                <div class="feature fade-in-up" style="animation-delay: 0.1s;">
                    <div class="feature__icon">🛡️</div>
                    <h3 class="feature__title">Secure & Reliable</h3>
                    <p class="feature__description">
                        99.9% uptime guarantee with enterprise-grade security. Your data and connection are always
                        protected.
                    </p>
                </div>

                <div class="feature fade-in-up" style="animation-delay: 0.2s;">
                    <div class="feature__icon">💬</div>
                    <h3 class="feature__title">24/7 Support</h3>
                    <p class="feature__description">
                        Our expert support team is available around the clock to help you with any questions or issues.
                    </p>
                </div>

                <div class="feature fade-in-up" style="animation-delay: 0.3s;">
                    <div class="feature__icon">💰</div>
                    <h3 class="feature__title">Best Value</h3>
                    <p class="feature__description">
                        Competitive pricing with no hidden fees. Get the most bandwidth for your budget.
                    </p>
                </div>

                <div class="feature fade-in-up" style="animation-delay: 0.4s;">
                    <div class="feature__icon">📱</div>
                    <h3 class="feature__title">Mobile Integration</h3>
                    <p class="feature__description">
                        Seamlessly connect your mobile devices with our integrated mobile and internet plans.
                    </p>
                </div>

                <div class="feature fade-in-up" style="animation-delay: 0.5s;">
                    <div class="feature__icon">🏢</div>
                    <h3 class="feature__title">Business Solutions</h3>
                    <p class="feature__description">
                        Customized enterprise solutions with dedicated support and scalable infrastructure.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Plans Section -->
    <section class="section section--alt">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">Popular Plans</h2>
                <p class="section__subtitle">
                    Choose the perfect plan for your home or business
                </p>
            </div>

            <div class="grid grid--3">
                <!-- Basic Plan -->
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
                    </ul>
                    <a href="plans.php" class="btn btn--outline" style="width: 100%;">Choose Plan</a>
                </div>

                <!-- Pro Plan (Featured) -->
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
                        <li class="plan-card__feature">Free Router + WiFi 6</li>
                        <li class="plan-card__feature">Priority Support</li>
                        <li class="plan-card__feature">Free Installation</li>
                    </ul>
                    <a href="plans.php" class="btn btn--primary" style="width: 100%;">Choose Plan</a>
                </div>

                <!-- Business Plan -->
                <div class="plan-card">
                    <div class="plan-card__header">
                        <h3 class="plan-card__name">Business</h3>
                        <div class="plan-card__price">
                            <span class="plan-card__price-currency">$</span>99<span
                                class="plan-card__price-period">/mo</span>
                        </div>
                    </div>
                    <ul class="plan-card__features">
                        <li class="plan-card__feature">1 Gbps Download</li>
                        <li class="plan-card__feature">500 Mbps Upload</li>
                        <li class="plan-card__feature">Unlimited Data</li>
                        <li class="plan-card__feature">Enterprise Router</li>
                        <li class="plan-card__feature">Dedicated Support</li>
                        <li class="plan-card__feature">Static IP Address</li>
                        <li class="plan-card__feature">SLA Guarantee</li>
                    </ul>
                    <a href="plans.php" class="btn btn--outline" style="width: 100%;">Choose Plan</a>
                </div>
            </div>

            <div class="text-center" style="margin-top: 2rem;">
                <a href="plans.php" class="btn btn--primary">View All Plans</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">What Our Customers Say</h2>
                <p class="section__subtitle">
                    Join thousands of satisfied customers
                </p>
            </div>

            <div class="grid grid--3">
                <div class="card">
                    <div class="card__body">
                        <p>"Switched to <?php echo e(SITE_NAME); ?> six months ago and couldn't be happier. The speed is
                            incredible and customer service is top-notch!"</p>
                    </div>
                    <div class="card__footer">
                        <strong>Sarah Johnson</strong><br>
                        <small style="color: var(--text-muted);">Home Pro Customer</small>
                    </div>
                </div>

                <div class="card">
                    <div class="card__body">
                        <p>"As a business owner, reliable internet is crucial. <?php echo e(SITE_NAME); ?> has never let
                            us down. Highly recommended!"</p>
                    </div>
                    <div class="card__footer">
                        <strong>Michael Chen</strong><br>
                        <small style="color: var(--text-muted);">Business Customer</small>
                    </div>
                </div>

                <div class="card">
                    <div class="card__body">
                        <p>"Installation was quick and easy. The technician was professional and explained everything
                            clearly. Great experience!"</p>
                    </div>
                    <div class="card__footer">
                        <strong>Emily Rodriguez</strong><br>
                        <small style="color: var(--text-muted);">Home Basic Customer</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section section--alt">
        <div class="container container--narrow text-center">
            <h2 class="section__title">Ready to Get Started?</h2>
            <p class="section__subtitle mb-xl">
                Join thousands of satisfied customers and experience the difference today.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="plans.php" class="btn btn--primary btn--large">View Plans</a>
                <a href="contact.php" class="btn btn--outline btn--large">Contact Sales</a>
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