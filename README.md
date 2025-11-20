# Telecom Website - Production-Ready Full-Stack Application

A modern, secure, and responsive telecom company website built with HTML, CSS, JavaScript, PHP, and MySQL. Features include a contact management system, admin dashboard, dark mode, and comprehensive security measures.

## 🎯 Features

- **Modern UI/UX**: Clean, minimal design with light/dark mode toggle
- **Responsive Design**: Mobile-first approach, works on all devices
- **Contact System**: Form submissions stored in MySQL database
- **Admin Dashboard**: Manage contact inquiries, mark as replied, delete entries
- **Security First**: CSRF protection, prepared statements, XSS prevention, password hashing
- **Accessibility**: Semantic HTML, ARIA attributes, keyboard navigation
- **Performance**: Lazy-loaded images, optimized assets

## 📋 Prerequisites

- **Web Server**: Apache 2.4+ (XAMPP, WAMP, LAMP, or MAMP)
- **PHP**: Version 8.0 or higher
- **MySQL/MariaDB**: Version 5.7+ / 10.3+
- **Browser**: Modern browser with JavaScript enabled

## 🚀 Installation & Setup

### Step 1: Clone or Download

Place the project folder in your web server's document root:
- XAMPP: `C:\xampp\htdocs\telecom`
- LAMP: `/var/www/html/telecom`
- MAMP: `/Applications/MAMP/htdocs/telecom`

### Step 2: Database Setup

1. Start your MySQL server (via XAMPP/LAMP control panel)
2. Access phpMyAdmin (usually `http://localhost/phpmyadmin`)
3. Create a new database named `telecom_db`
4. Import the schema:
   - Click on `telecom_db`
   - Go to "Import" tab
   - Choose file: `sql/schema.sql`
   - Click "Go"

### Step 3: Configuration

1. Copy the sample configuration file:
   ```bash
   cp config.sample.php config.php
   ```

2. Edit `config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'telecom_db');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   ```

### Step 4: Create Admin User

The schema includes a default admin account:
- **Username**: `admin`
- **Password**: `Admin@2025!`

**⚠️ IMPORTANT**: Change this password immediately after f irst login!

To create additional admin users, use the provided SQL or add via phpMyAdmin:
```sql
INSERT INTO admin_users (username, password_hash) 
VALUES ('yourusername', '$2y$10$...');
```

Generate password hash in PHP:
```php
echo password_hash('YourPassword', PASSWORD_BCRYPT);
```

### Step 5: Access the Website

1. Start Apache and MySQL
2. Navigate to:
   - **Homepage**: `http://localhost/telecom/public/`
   - **Admin Panel**: `http://localhost/telecom/admin/login.php`

## 📁 Project Structure

```
telecom/
├── public/                 # Public-facing pages
│   ├── index.php          # Homepage
│   ├── plans.php          # Plans & Offers
│   ├── contact.php        # Contact form
│   └── assets/
│       ├── css/
│       │   └── style.css  # Main stylesheet
│       ├── js/
│       │   └── main.js    # Main JavaScript
│       └── images/
│           └── free/
│               └── credits.txt
├── src/                   # Backend logic
│   ├── db.php            # Database connection
│   ├── auth.php          # Authentication functions
│   └── functions.php     # Helper functions
├── admin/                 # Admin panel
│   ├── login.php         # Admin login
│   └── dashboard.php     # Contact management
├── sql/
│   └── schema.sql        # Database schema
├── config.sample.php     # Sample configuration
├── config.php            # Your configuration (create from sample)
└── README.md             # This file
```

## 🔒 Security Features

### Implemented Protections

1. **SQL Injection Prevention**: All queries use PDO prepared statements
2. **XSS Prevention**: All output escaped with `htmlspecialchars()`
3. **CSRF Protection**: Token-based form validation
4. **Password Security**: Bcrypt hashing with `password_hash()`
5. **Input Validation**: Both client-side and server-side
6. **Honeypot Anti-Spam**: Hidden field to catch bots
7. **Rate Limiting**: IP-based submission throttling (recommended)

### Security Best Practices

- Always use HTTPS in production (Let's Encrypt is free)
- Change default admin credentials immediately
- Keep PHP and MySQL updated
- Set proper file permissions (644 for files, 755 for directories)
- Never commit `config.php` to version control

## 🧪 Testing Checklist

### Functional Testing

- [ ] Homepage loads correctly on desktop and mobile
- [ ] Plans page displays all offers properly
- [ ] Contact form validates input (client-side)
- [ ] Contact form submits successfully
- [ ] Form data appears in database
- [ ] Admin login works with correct credentials
- [ ] Admin login rejects incorrect credentials
- [ ] Admin dashboard displays contact messages
- [ ] Admin can mark messages as replied/closed
- [ ] Admin can delete messages
- [ ] Dark mode toggle works and persists
- [ ] All images load correctly

### Security Testing

- [ ] **CSRF Protection**: Try submitting form without token (should fail)
- [ ] **SQL Injection**: Try `' OR '1'='1` in form fields (should be safe)
- [ ] **XSS Prevention**: Try `<script>alert('XSS')</script>` in message (should be escaped)
- [ ] **Password Security**: Verify passwords are hashed in database
- [ ] **Session Security**: Logout works and invalidates session

### Performance Testing

- [ ] Lighthouse score: Performance ≥ 85
- [ ] Lighthouse score: Accessibility ≥ 90
- [ ] Images lazy-load on scroll
- [ ] Page loads in < 3 seconds on 3G

## 🎨 Customization

### Colors

Edit CSS variables in `public/assets/css/style.css`:

```css
:root {
  --primary: #2C7A7B;
  --accent: #F6AD55;
  --bg-light: #F7FAFC;
  /* ... */
}
```

### Content

- **Homepage**: Edit `public/index.php`
- **Plans**: Edit `public/plans.php`
- **Contact**: Edit `public/contact.php`

### Database

- Add fields: Modify `sql/schema.sql` and run migration
- Change table names: Update in `src/db.php` and relevant files

## 📧 Email Notifications (Optional)

To enable email notifications when contact form is submitted:

1. Install PHPMailer: `composer require phpmailer/phpmailer`
2. Configure SMTP in `config.php`
3. Uncomment email code in `public/contact.php`

Example SMTP configuration:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

## 🌐 Deployment to Production

### Pre-Deployment Checklist

1. Change all default passwords
2. Update `config.php` with production database credentials
3. Set `error_reporting(0)` in production
4. Enable HTTPS (Let's Encrypt)
5. Set proper file permissions
6. Remove `config.sample.php` from public access
7. Test all functionality on staging environment

### Recommended Hosting

- **Shared Hosting**: Bluehost, SiteGround, HostGator
- **VPS**: DigitalOcean, Linode, Vultr
- **Managed**: Cloudways, Kinsta (WordPress alternative)

### SSL Certificate (Free)

```bash
# Using Certbot (Let's Encrypt)
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

## 🐛 Troubleshooting

### Database Connection Error

- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database `telecom_db` exists

### 404 Errors

- Check Apache `mod_rewrite` is enabled
- Verify file paths are correct
- Check `.htaccess` if using URL rewriting

### Dark Mode Not Persisting

- Check browser localStorage is enabled
- Clear browser cache and cookies
- Verify JavaScript is enabled

### Contact Form Not Submitting

- Check browser console for JavaScript errors
- Verify PHP error logs: `tail -f /var/log/apache2/error.log`
- Ensure database connection is working

## 📚 Learning Resources

This project demonstrates:

- **PHP PDO**: Secure database interactions
- **Session Management**: User authentication
- **CSRF Protection**: Form security
- **Responsive Design**: Mobile-first CSS
- **JavaScript ES6+**: Modern syntax and features
- **SQL**: Database design and queries

### Recommended Reading

- [PHP The Right Way](https://phptherightway.com/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [MDN Web Docs](https://developer.mozilla.org/)

## 📄 License

This project is provided as-is for educational and commercial use.

## 🤝 Contributing

This is a learning project. Feel free to:
- Report bugs
- Suggest improvements
- Add features
- Improve documentation

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review PHP error logs
3. Check browser console for JavaScript errors

## ✅ Version History

- **v1.0.0** (2025-01-20): Initial release
  - Contact form with database storage
  - Admin dashboard
  - Dark mode toggle
  - Security features (CSRF, XSS, SQL injection protection)

---

**Built with ❤️ for learners and professionals**

*Remember: Security is not a feature, it's a requirement!*
