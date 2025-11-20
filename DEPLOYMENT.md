# Deployment Guide

Complete guide to deploying your telecom website to a production server.

## 📋 Pre-Deployment Checklist

Before deploying, ensure you've completed these steps:

- [ ] All features tested locally (see TESTING_CHECKLIST.md)
- [ ] Default admin password changed
- [ ] Database backup created
- [ ] Production server meets requirements
- [ ] Domain name configured
- [ ] SSL certificate ready (Let's Encrypt recommended)

---

## 🖥️ Server Requirements

### Minimum Requirements

- **PHP:** 8.0 or higher
- **MySQL/MariaDB:** 5.7+ / 10.3+
- **Apache/Nginx:** Latest stable version
- **Disk Space:** 100 MB minimum
- **RAM:** 512 MB minimum (1 GB recommended)
- **SSL Certificate:** Required for production

### Required PHP Extensions

- PDO
- PDO_MySQL
- mbstring
- openssl
- session
- json

### Recommended PHP Settings

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 60
memory_limit = 256M
display_errors = Off
log_errors = On
```

---

## 🚀 Deployment Steps

### Step 1: Prepare Files

1. **Create a clean copy of your project**
   ```bash
   # Remove development files
   rm -rf .git
   rm -rf node_modules
   rm -rf vendor
   ```

2. **Verify .gitignore is working**
   - Ensure `config.php` is NOT included
   - Ensure logs/ is NOT included
   - Ensure uploads/ is NOT included

3. **Create a zip archive**
   ```bash
   zip -r telecom-website.zip . -x "*.git*" "node_modules/*" "vendor/*"
   ```

### Step 2: Upload to Server

#### Option A: FTP/SFTP (Most common)

1. **Connect to your server**
   - Use FileZilla, Cyberduck, or WinSCP
   - Host: your-domain.com
   - Protocol: SFTP (port 22) or FTP (port 21)
   - Username: your-username
   - Password: your-password

2. **Upload files**
   - Upload to: `/public_html/` or `/var/www/html/`
   - Ensure all files are uploaded
   - Verify folder structure is intact

#### Option B: SSH (Recommended for advanced users)

```bash
# Connect to server
ssh username@your-domain.com

# Navigate to web root
cd /var/www/html

# Upload using SCP (from local machine)
scp -r /path/to/project username@your-domain.com:/var/www/html/

# Or clone from repository
git clone https://github.com/yourusername/telecom-website.git
```

### Step 3: Configure Database

#### Create Production Database

```sql
-- Connect to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE telecom_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user (recommended)
CREATE USER 'telecom_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

-- Grant permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON telecom_db.* TO 'telecom_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

#### Import Schema

```bash
# Import schema
mysql -u telecom_user -p telecom_db < sql/schema.sql

# Verify import
mysql -u telecom_user -p telecom_db -e "SHOW TABLES;"
```

### Step 4: Configure Application

1. **Create config.php from sample**
   ```bash
   cp config.sample.php config.php
   ```

2. **Edit config.php**
   ```php
   <?php
   // Database
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'telecom_db');
   define('DB_USER', 'telecom_user');
   define('DB_PASS', 'YOUR_STRONG_PASSWORD');
   
   // Site URLs (update with your domain)
   define('SITE_URL', 'https://yourdomain.com/public');
   define('ADMIN_URL', 'https://yourdomain.com/admin');
   
   // Environment
   define('ENVIRONMENT', 'production');
   define('DISPLAY_ERRORS', false);
   define('LOG_ERRORS', true);
   
   // Security (generate new secret key)
   define('SECRET_KEY', 'GENERATE_NEW_RANDOM_STRING_HERE');
   ```

3. **Generate new SECRET_KEY**
   ```php
   php -r "echo bin2hex(random_bytes(32));"
   ```

### Step 5: Set File Permissions

```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make logs directory writable
chmod 755 logs/
chmod 644 logs/*.log

# Protect config.php
chmod 600 config.php

# Set ownership (replace 'www-data' with your web server user)
chown -R www-data:www-data /var/www/html/
```

### Step 6: Configure Web Server

#### Apache (.htaccess)

Create `.htaccess` in project root:

```apache
# Prevent directory listing
Options -Indexes

# Protect config.php
<Files "config.php">
    Require all denied
</Files>

# Protect .git directory
<DirectoryMatch "\.git">
    Require all denied
</DirectoryMatch>

# Enable HTTPS redirect
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
<IfModule mod_headers.c>
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

#### Nginx (nginx.conf)

Add to your server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/html/public;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Deny access to config.php
    location ~ /config\.php$ {
        deny all;
    }
    
    # Deny access to .git
    location ~ /\.git {
        deny all;
    }
    
    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### Step 7: Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

# For Apache
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# For Nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### Step 8: Change Admin Password

1. **Login to admin panel**
   - Go to: `https://yourdomain.com/admin/login.php`
   - Login with default credentials

2. **Generate new password hash**
   ```php
   php -r "echo password_hash('YourNewStrongPassword', PASSWORD_BCRYPT);"
   ```

3. **Update in database**
   ```sql
   UPDATE admin_users 
   SET password_hash = '$2y$10$YOUR_NEW_HASH_HERE' 
   WHERE username = 'admin';
   ```

### Step 9: Test Everything

1. **Test public pages**
   - Homepage: `https://yourdomain.com/public/`
   - Plans: `https://yourdomain.com/public/plans.php`
   - Contact: `https://yourdomain.com/public/contact.php`

2. **Test contact form**
   - Submit a test message
   - Verify it appears in admin dashboard

3. **Test admin panel**
   - Login: `https://yourdomain.com/admin/login.php`
   - View contacts
   - Update status
   - Export CSV

4. **Test security**
   - Verify HTTPS is working
   - Check SSL certificate is valid
   - Try accessing config.php (should be denied)
   - Test CSRF protection
   - Test rate limiting

### Step 10: Monitor and Maintain

1. **Set up monitoring**
   - Use UptimeRobot or Pingdom for uptime monitoring
   - Monitor error logs regularly
   - Set up Google Analytics (optional)

2. **Create backup schedule**
   ```bash
   # Database backup script
   #!/bin/bash
   DATE=$(date +%Y%m%d_%H%M%S)
   mysqldump -u telecom_user -p telecom_db > backup_$DATE.sql
   gzip backup_$DATE.sql
   ```

3. **Set up cron jobs**
   ```bash
   # Edit crontab
   crontab -e
   
   # Add daily backup at 2 AM
   0 2 * * * /path/to/backup-script.sh
   
   # Clean old logs weekly
   0 0 * * 0 find /var/www/html/logs -name "*.log" -mtime +30 -delete
   ```

---

## 🔒 Security Hardening

### Additional Security Measures

1. **Disable PHP functions**
   ```ini
   disable_functions = exec,passthru,shell_exec,system,proc_open,popen
   ```

2. **Hide PHP version**
   ```ini
   expose_php = Off
   ```

3. **Enable fail2ban**
   ```bash
   sudo apt-get install fail2ban
   sudo systemctl enable fail2ban
   sudo systemctl start fail2ban
   ```

4. **Set up firewall**
   ```bash
   sudo ufw allow 22/tcp
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

5. **Regular updates**
   ```bash
   sudo apt-get update
   sudo apt-get upgrade
   ```

---

## 🐛 Troubleshooting Production Issues

### "500 Internal Server Error"

1. Check PHP error logs: `/var/log/apache2/error.log`
2. Check application logs: `logs/error.log`
3. Verify file permissions
4. Check PHP version compatibility

### "Database connection error"

1. Verify database credentials in config.php
2. Check MySQL is running: `sudo systemctl status mysql`
3. Test database connection: `mysql -u telecom_user -p`
4. Check firewall rules

### "Permission denied"

1. Check file ownership: `ls -la`
2. Fix permissions: `chmod 755` for directories, `chmod 644` for files
3. Check SELinux: `sudo setenforce 0` (temporary)

### SSL certificate issues

1. Verify certificate: `sudo certbot certificates`
2. Renew certificate: `sudo certbot renew`
3. Check certificate expiry: `openssl x509 -in cert.pem -noout -dates`

---

## 📊 Performance Optimization

### Enable Caching

```apache
# Apache .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Enable Compression

```apache
# Apache .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### Optimize Database

```sql
-- Analyze tables
ANALYZE TABLE contacts, admin_users, login_attempts;

-- Optimize tables
OPTIMIZE TABLE contacts, admin_users, login_attempts;
```

---

## ✅ Post-Deployment Checklist

- [ ] All pages load correctly
- [ ] HTTPS is working
- [ ] SSL certificate is valid
- [ ] Contact form works
- [ ] Admin login works
- [ ] Admin password changed
- [ ] Database backups configured
- [ ] Error logging enabled
- [ ] Monitoring set up
- [ ] Security headers enabled
- [ ] File permissions correct
- [ ] config.php protected
- [ ] Performance optimized
- [ ] Documentation updated

---

## 📞 Support

If you encounter issues during deployment:

1. Check error logs first
2. Review this guide carefully
3. Consult your hosting provider's documentation
4. Check server requirements are met

---

**Deployment Date:** _______________  
**Deployed By:** _______________  
**Server:** _______________  
**Domain:** _______________  

**Notes:**
