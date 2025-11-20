/**
 * Telecom Website - Main JavaScript
 * Version: 1.0.0
 * Description: Dark mode toggle, form validation, and interactive features
 */

(function() {
  'use strict';

  // ============================================
  // DARK MODE TOGGLE
  // ============================================

  class ThemeManager {
    constructor() {
      this.theme = localStorage.getItem('theme') || this.getSystemPreference();
      this.init();
    }

    getSystemPreference() {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    init() {
      // Apply saved theme
      this.applyTheme(this.theme);

      // Listen for system theme changes
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
          this.applyTheme(e.matches ? 'dark' : 'light');
        }
      });

      // Setup toggle buttons
      this.setupToggleButtons();
    }

    applyTheme(theme) {
      this.theme = theme;
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      this.updateToggleButtons();
    }

    toggleTheme() {
      const newTheme = this.theme === 'light' ? 'dark' : 'light';
      this.applyTheme(newTheme);
    }

    setupToggleButtons() {
      const toggleButtons = document.querySelectorAll('.theme-toggle');
      toggleButtons.forEach(button => {
        button.addEventListener('click', () => this.toggleTheme());
      });
    }

    updateToggleButtons() {
      const toggleButtons = document.querySelectorAll('.theme-toggle');
      toggleButtons.forEach(button => {
        const icon = button.querySelector('.theme-toggle__icon');
        if (icon) {
          icon.textContent = this.theme === 'light' ? '🌙' : '☀️';
        }
      });
    }
  }

  // ============================================
  // MOBILE MENU
  // ============================================

  class MobileMenu {
    constructor() {
      this.toggle = document.querySelector('.mobile-menu-toggle');
      this.menu = document.querySelector('.nav__list');
      this.init();
    }

    init() {
      if (!this.toggle || !this.menu) return;

      this.toggle.addEventListener('click', () => this.toggleMenu());

      // Close menu when clicking outside
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav') && !e.target.closest('.mobile-menu-toggle')) {
          this.closeMenu();
        }
      });

      // Close menu when clicking a link
      this.menu.querySelectorAll('.nav__link').forEach(link => {
        link.addEventListener('click', () => this.closeMenu());
      });
    }

    toggleMenu() {
      this.menu.classList.toggle('nav__list--open');
      this.toggle.setAttribute('aria-expanded', 
        this.menu.classList.contains('nav__list--open'));
    }

    closeMenu() {
      this.menu.classList.remove('nav__list--open');
      this.toggle.setAttribute('aria-expanded', 'false');
    }
  }

  // ============================================
  // FORM VALIDATION
  // ============================================

  class FormValidator {
    constructor(formSelector) {
      this.form = document.querySelector(formSelector);
      if (this.form) {
        this.init();
      }
    }

    init() {
      this.form.addEventListener('submit', (e) => this.handleSubmit(e));

      // Real-time validation
      const inputs = this.form.querySelectorAll('input, textarea, select');
      inputs.forEach(input => {
        input.addEventListener('blur', () => this.validateField(input));
        input.addEventListener('input', () => this.clearError(input));
      });
    }

    handleSubmit(e) {
      e.preventDefault();

      // Clear previous errors
      this.clearAllErrors();

      // Validate all fields
      const isValid = this.validateForm();

      if (isValid) {
        // Show loading state
        const submitButton = this.form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';

        // Submit form
        this.form.submit();
      } else {
        // Focus first error
        const firstError = this.form.querySelector('.form__error');
        if (firstError) {
          const field = firstError.previousElementSibling;
          if (field) field.focus();
        }
      }
    }

    validateForm() {
      const inputs = this.form.querySelectorAll('input:not([type="hidden"]), textarea, select');
      let isValid = true;

      inputs.forEach(input => {
        if (!this.validateField(input)) {
          isValid = false;
        }
      });

      return isValid;
    }

    validateField(field) {
      const value = field.value.trim();
      const name = field.name;
      const type = field.type;
      let error = '';

      // Required field check
      if (field.hasAttribute('required') && !value) {
        error = `${this.getFieldLabel(field)} is required.`;
      }
      // Email validation
      else if (type === 'email' && value && !this.isValidEmail(value)) {
        error = 'Please enter a valid email address.';
      }
      // Phone validation
      else if (name === 'phone' && value && !this.isValidPhone(value)) {
        error = 'Please enter a valid phone number.';
      }
      // Min length
      else if (field.hasAttribute('minlength')) {
        const minLength = parseInt(field.getAttribute('minlength'));
        if (value.length < minLength) {
          error = `${this.getFieldLabel(field)} must be at least ${minLength} characters.`;
        }
      }
      // Max length
      else if (field.hasAttribute('maxlength')) {
        const maxLength = parseInt(field.getAttribute('maxlength'));
        if (value.length > maxLength) {
          error = `${this.getFieldLabel(field)} must not exceed ${maxLength} characters.`;
        }
      }

      if (error) {
        this.showError(field, error);
        return false;
      }

      return true;
    }

    isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    }

    isValidPhone(phone) {
      // Remove spaces, dashes, parentheses
      const cleaned = phone.replace(/[\s\-\(\)]/g, '');
      // Check if it contains only digits and optional + at start
      const re = /^\+?[0-9]{8,15}$/;
      return re.test(cleaned);
    }

    getFieldLabel(field) {
      const label = this.form.querySelector(`label[for="${field.id}"]`);
      return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    showError(field, message) {
      this.clearError(field);

      field.classList.add('form__input--error');
      field.setAttribute('aria-invalid', 'true');

      const errorElement = document.createElement('span');
      errorElement.className = 'form__error';
      errorElement.textContent = message;
      errorElement.setAttribute('role', 'alert');

      field.parentNode.appendChild(errorElement);
    }

    clearError(field) {
      field.classList.remove('form__input--error');
      field.removeAttribute('aria-invalid');

      const error = field.parentNode.querySelector('.form__error');
      if (error) {
        error.remove();
      }
    }

    clearAllErrors() {
      const errors = this.form.querySelectorAll('.form__error');
      errors.forEach(error => error.remove());

      const errorFields = this.form.querySelectorAll('.form__input--error');
      errorFields.forEach(field => {
        field.classList.remove('form__input--error');
        field.removeAttribute('aria-invalid');
      });
    }
  }

  // ============================================
  // LAZY LOADING IMAGES
  // ============================================

  class LazyLoader {
    constructor() {
      this.images = document.querySelectorAll('img[data-src]');
      this.init();
    }

    init() {
      if ('IntersectionObserver' in window) {
        this.observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              this.loadImage(entry.target);
            }
          });
        }, {
          rootMargin: '50px'
        });

        this.images.forEach(img => this.observer.observe(img));
      } else {
        // Fallback for browsers without IntersectionObserver
        this.images.forEach(img => this.loadImage(img));
      }
    }

    loadImage(img) {
      const src = img.getAttribute('data-src');
      if (!src) return;

      img.src = src;
      img.removeAttribute('data-src');
      img.classList.add('loaded');

      if (this.observer) {
        this.observer.unobserve(img);
      }
    }
  }

  // ============================================
  // SMOOTH SCROLL
  // ============================================

  class SmoothScroll {
    constructor() {
      this.init();
    }

    init() {
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
          const href = anchor.getAttribute('href');
          if (href === '#') return;

          const target = document.querySelector(href);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });
    }
  }

  // ============================================
  // ALERT AUTO-DISMISS
  // ============================================

  class AlertManager {
    constructor() {
      this.alerts = document.querySelectorAll('.alert');
      this.init();
    }

    init() {
      this.alerts.forEach(alert => {
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
          this.dismissAlert(alert);
        }, 5000);

        // Add close button if not exists
        if (!alert.querySelector('.alert__close')) {
          const closeBtn = document.createElement('button');
          closeBtn.className = 'alert__close';
          closeBtn.innerHTML = '×';
          closeBtn.setAttribute('aria-label', 'Close alert');
          closeBtn.addEventListener('click', () => this.dismissAlert(alert));
          alert.appendChild(closeBtn);
        }
      });
    }

    dismissAlert(alert) {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-20px)';
      setTimeout(() => {
        alert.remove();
      }, 300);
    }
  }

  // ============================================
  // ACTIVE NAV LINK
  // ============================================

  class NavigationHighlighter {
    constructor() {
      this.currentPath = window.location.pathname;
      this.init();
    }

    init() {
      const navLinks = document.querySelectorAll('.nav__link');
      navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (linkPath === this.currentPath) {
          link.classList.add('nav__link--active');
        }
      });
    }
  }

  // ============================================
  // SCROLL TO TOP BUTTON
  // ============================================

  class ScrollToTop {
    constructor() {
      this.button = this.createButton();
      this.init();
    }

    createButton() {
      const button = document.createElement('button');
      button.className = 'scroll-to-top';
      button.innerHTML = '↑';
      button.setAttribute('aria-label', 'Scroll to top');
      button.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        font-size: 24px;
        box-shadow: var(--shadow-lg);
      `;
      document.body.appendChild(button);
      return button;
    }

    init() {
      // Show/hide button on scroll
      window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
          this.button.style.opacity = '1';
          this.button.style.visibility = 'visible';
        } else {
          this.button.style.opacity = '0';
          this.button.style.visibility = 'hidden';
        }
      });

      // Scroll to top on click
      this.button.addEventListener('click', () => {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    }
  }

  // ============================================
  // INITIALIZATION
  // ============================================

  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    // Initialize all components
    new ThemeManager();
    new MobileMenu();
    new LazyLoader();
    new SmoothScroll();
    new AlertManager();
    new NavigationHighlighter();
    new ScrollToTop();

    // Initialize form validation for contact form
    new FormValidator('#contact-form');

    // Log initialization in debug mode
    if (window.location.hostname === 'localhost') {
      console.log('✅ Telecom Website initialized successfully');
    }
  }

  // ============================================
  // UTILITY FUNCTIONS
  // ============================================

  // Debounce function for performance
  window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };

  // Throttle function for performance
  window.throttle = function(func, limit) {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  };

})();
