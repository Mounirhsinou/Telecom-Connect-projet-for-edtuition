# Testing Checklist

Use this checklist to verify all features are working correctly before deployment.

## ✅ Functional Testing

### Homepage
- [ ] Homepage loads without errors
- [ ] Hero section displays correctly
- [ ] All navigation links work
- [ ] Features section displays all 6 features
- [ ] Plans section shows 3 featured plans
- [ ] Testimonials section displays correctly
- [ ] Footer displays all information
- [ ] All links in footer work
- [ ] Page is responsive on mobile (< 768px)
- [ ] Page is responsive on tablet (768px - 1024px)
- [ ] Page is responsive on desktop (> 1024px)

### Plans Page
- [ ] Plans page loads without errors
- [ ] All home internet plans display correctly
- [ ] All business plans display correctly
- [ ] All mobile plans display correctly
- [ ] "Get Started" buttons link to contact page with plan pre-selected
- [ ] FAQ section displays correctly
- [ ] Page is responsive on all screen sizes

### Contact Page
- [ ] Contact form displays correctly
- [ ] All form fields are present (name, email, phone, subject, plan, message)
- [ ] Plan dropdown shows all available plans
- [ ] Form validation works (client-side)
- [ ] Required fields show error when empty
- [ ] Email validation works
- [ ] Phone validation works (optional field)
- [ ] Form submits successfully with valid data
- [ ] Success message displays after submission
- [ ] Form data is saved to database
- [ ] Contact info section displays correctly
- [ ] Page is responsive on all screen sizes

### Admin Login
- [ ] Login page loads without errors
- [ ] Login form displays correctly
- [ ] CSRF token is generated
- [ ] Login works with correct credentials (admin / Admin@2025!)
- [ ] Login fails with incorrect username
- [ ] Login fails with incorrect password
- [ ] Error messages display correctly
- [ ] Account locks after 5 failed attempts
- [ ] Locked account shows appropriate message
- [ ] Session is created on successful login
- [ ] Redirect to dashboard after login
- [ ] "Back to Website" link works

### Admin Dashboard
- [ ] Dashboard requires authentication
- [ ] Unauthenticated users are redirected to login
- [ ] Dashboard loads without errors
- [ ] Statistics display correctly
- [ ] All stat cards show accurate numbers
- [ ] Contact table displays all contacts
- [ ] Search filter works
- [ ] Status filter works
- [ ] Combined filters work
- [ ] Pagination works correctly
- [ ] "View" button opens modal with contact details
- [ ] Modal displays all contact information
- [ ] Status update works from modal
- [ ] Delete button works
- [ ] Delete confirmation appears
- [ ] Contact is removed from database after deletion
- [ ] Export CSV button works
- [ ] CSV contains correct data
- [ ] Logout button works
- [ ] Session is destroyed on logout

## 🔒 Security Testing

### SQL Injection Prevention
- [ ] Try `' OR '1'='1` in contact form name field → Should be safe
- [ ] Try `'; DROP TABLE contacts; --` in email field → Should be safe
- [ ] Try SQL injection in admin login → Should be safe
- [ ] Try SQL injection in search filter → Should be safe
- [ ] Verify all queries use prepared statements (check code)

### XSS Prevention
- [ ] Try `<script>alert('XSS')</script>` in contact form message → Should be escaped
- [ ] Try `<img src=x onerror=alert('XSS')>` in name field → Should be escaped
- [ ] Try JavaScript in subject field → Should be escaped
- [ ] Verify all output uses htmlspecialchars() (check code)
- [ ] Check that user input is never echoed directly

### CSRF Protection
- [ ] Submit contact form without CSRF token → Should fail
- [ ] Submit contact form with invalid CSRF token → Should fail
- [ ] Submit admin login without CSRF token → Should fail
- [ ] Update contact status without CSRF token → Should fail
- [ ] Delete contact without CSRF token → Should fail
- [ ] Verify CSRF token is validated on all POST requests

### Password Security
- [ ] Check database: passwords should be hashed (not plain text)
- [ ] Verify password_hash() is used for new passwords
- [ ] Verify password_verify() is used for login
- [ ] Ensure passwords are never logged or displayed

### Session Security
- [ ] Session cookie has HttpOnly flag
- [ ] Session cookie has Secure flag (if HTTPS)
- [ ] Session cookie has SameSite=Strict
- [ ] Session ID regenerates after login
- [ ] Session expires after inactivity (2 hours)
- [ ] Logout properly destroys session

### Rate Limiting
- [ ] Submit contact form 3 times rapidly → Should succeed
- [ ] Submit contact form 4th time → Should be blocked
- [ ] Wait 1 hour → Should be able to submit again
- [ ] Verify rate limit is per IP address

### Honeypot Anti-Spam
- [ ] Fill honeypot field (website) in contact form → Should be rejected
- [ ] Leave honeypot field empty → Should succeed
- [ ] Verify honeypot field is hidden from users

## 🎨 UI/UX Testing

### Dark Mode
- [ ] Dark mode toggle button is visible
- [ ] Clicking toggle switches to dark mode
- [ ] Dark mode persists after page reload
- [ ] Dark mode persists across different pages
- [ ] All colors are readable in dark mode
- [ ] All components look good in dark mode
- [ ] Dark mode respects system preference on first visit
- [ ] Transition between modes is smooth

### Responsive Design
- [ ] Test on mobile (320px - 480px)
- [ ] Test on tablet (481px - 768px)
- [ ] Test on laptop (769px - 1024px)
- [ ] Test on desktop (1025px+)
- [ ] Mobile menu works on small screens
- [ ] Mobile menu toggle button appears on mobile
- [ ] Mobile menu closes when clicking outside
- [ ] Mobile menu closes when clicking a link
- [ ] All text is readable on all screen sizes
- [ ] No horizontal scrolling on any screen size
- [ ] Images scale appropriately
- [ ] Buttons are touch-friendly on mobile (min 44px)

### Accessibility
- [ ] All images have alt text
- [ ] Form labels are associated with inputs
- [ ] Required fields are marked with aria-required
- [ ] Error messages have role="alert"
- [ ] Focus indicators are visible
- [ ] Keyboard navigation works (Tab, Enter, Esc)
- [ ] Color contrast meets WCAG AA standards
- [ ] Heading hierarchy is correct (h1 → h2 → h3)
- [ ] Links have descriptive text (no "click here")
- [ ] Test with screen reader (if possible)

### Performance
- [ ] Homepage loads in < 3 seconds (3G network)
- [ ] Images are optimized
- [ ] No console errors in browser
- [ ] No console warnings in browser
- [ ] CSS is minified for production
- [ ] JavaScript is minified for production
- [ ] Lighthouse Performance score ≥ 85
- [ ] Lighthouse Accessibility score ≥ 90
- [ ] Lighthouse Best Practices score ≥ 90
- [ ] Lighthouse SEO score ≥ 90

## 📊 Database Testing

### Schema
- [ ] Database `telecom_db` exists
- [ ] Table `contacts` exists with correct columns
- [ ] Table `admin_users` exists with correct columns
- [ ] Table `login_attempts` exists with correct columns
- [ ] Table `rate_limits` exists with correct columns
- [ ] Default admin user exists
- [ ] Sample contact exists (for testing)
- [ ] Views are created (view_recent_contacts, view_contact_stats)
- [ ] Stored procedures are created
- [ ] Triggers are created
- [ ] Events are created (if event scheduler is enabled)

### Data Integrity
- [ ] Contact submission creates new row in contacts table
- [ ] All contact fields are saved correctly
- [ ] IP address is captured correctly
- [ ] Timestamp is set automatically
- [ ] Status defaults to 'new'
- [ ] Login attempt is logged in login_attempts table
- [ ] Failed login increments failed_login_attempts
- [ ] Successful login resets failed_login_attempts
- [ ] Account locks after max attempts
- [ ] Rate limit is enforced correctly

## 🚀 Deployment Readiness

### Configuration
- [ ] config.php exists (not committed to git)
- [ ] Database credentials are correct
- [ ] SECRET_KEY is changed from default
- [ ] ENVIRONMENT is set to 'production' for live site
- [ ] DISPLAY_ERRORS is false for production
- [ ] LOG_ERRORS is true
- [ ] Error log directory exists and is writable
- [ ] SITE_URL is updated for production domain
- [ ] ADMIN_URL is updated for production domain

### Security Hardening
- [ ] Default admin password is changed
- [ ] File permissions are correct (644 for files, 755 for directories)
- [ ] config.php is not accessible via web browser
- [ ] .git directory is not accessible via web browser
- [ ] Error messages don't reveal sensitive information
- [ ] HTTPS is enabled (Let's Encrypt recommended)
- [ ] Security headers are enabled (uncomment in config.php)
- [ ] Database user has minimal required permissions

### Documentation
- [ ] README.md is complete and accurate
- [ ] Installation instructions are clear
- [ ] Configuration instructions are clear
- [ ] Default credentials are documented
- [ ] Security best practices are documented
- [ ] Troubleshooting section is helpful
- [ ] Code comments are clear and helpful

## 📝 Code Quality

### PHP
- [ ] No PHP errors or warnings
- [ ] All functions have docblocks
- [ ] Code follows PSR standards (where applicable)
- [ ] No hardcoded credentials
- [ ] No debug code (var_dump, print_r) in production
- [ ] All database queries use prepared statements
- [ ] All user input is validated
- [ ] All output is escaped

### JavaScript
- [ ] No JavaScript errors in console
- [ ] Code is well-commented
- [ ] ES6+ features are used appropriately
- [ ] No global variable pollution
- [ ] Event listeners are properly attached
- [ ] No memory leaks

### CSS
- [ ] No CSS errors
- [ ] CSS variables are used consistently
- [ ] No unused CSS rules
- [ ] Responsive design uses mobile-first approach
- [ ] Transitions are smooth
- [ ] No layout shifts (CLS)

## 🧪 Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## 📱 Device Testing

- [ ] iPhone (various models)
- [ ] Android phone (various models)
- [ ] iPad
- [ ] Android tablet
- [ ] Desktop (Windows)
- [ ] Desktop (Mac)
- [ ] Desktop (Linux)

---

## Final Checklist Before Going Live

- [ ] All tests above are passing
- [ ] Backup database and files
- [ ] Change default admin password
- [ ] Update config.php for production
- [ ] Enable HTTPS
- [ ] Test on production server
- [ ] Monitor error logs
- [ ] Set up automated backups
- [ ] Document any custom configurations

---

**Testing Date:** _______________  
**Tested By:** _______________  
**Environment:** Development / Staging / Production  
**Status:** Pass / Fail / Needs Review  

**Notes:**
