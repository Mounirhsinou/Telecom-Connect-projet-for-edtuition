# Quick Start Guide

Get your telecom website up and running in 5 minutes!

## Prerequisites

✅ **XAMPP** (or LAMP/WAMP/MAMP) installed  
✅ **PHP 8.0+** installed  
✅ **MySQL/MariaDB** running  
✅ **Web browser** (Chrome, Firefox, Safari, or Edge)

---

## Step 1: Setup Database (2 minutes)

### Option A: Using phpMyAdmin (Recommended for beginners)

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click **Start** for Apache
   - Click **Start** for MySQL

2. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`

3. **Create Database**
   - Click **New** in the left sidebar
   - Database name: `telecom_db`
   - Collation: `utf8mb4_unicode_ci`
   - Click **Create**

4. **Import Schema**
   - Click on `telecom_db` in the left sidebar
   - Click **Import** tab
   - Click **Choose File**
   - Select: `sql/schema.sql`
   - Click **Go** at the bottom
   - Wait for success message ✅

### Option B: Using Command Line (For advanced users)

```bash
# Navigate to project directory
cd "C:\Users\mhdev\OneDrive\Desktop\TELECOME TEST"

# Import schema
mysql -u root -p telecom_db < sql/schema.sql
```

---

## Step 2: Configure Application (1 minute)

The `config.php` file is already created with default settings. If you need to change database credentials:

1. Open `config.php`
2. Update these lines if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'telecom_db');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   ```
3. Save the file

---

## Step 3: Access the Website (1 minute)

### Public Website

Open your browser and go to:
```
http://localhost/TELECOME%20TEST/public/
```

Or if you moved the project to htdocs root:
```
http://localhost/telecom/public/
```

You should see the homepage! 🎉

### Admin Panel

Go to:
```
http://localhost/TELECOME%20TEST/admin/login.php
```

**Default Login Credentials:**
- **Username:** `admin`
- **Password:** `Admin@2025!`

⚠️ **IMPORTANT:** Change this password immediately after first login!

---

## Step 4: Test the Contact Form (1 minute)

1. Go to the **Contact** page
2. Fill out the form:
   - Name: Your Name
   - Email: your@email.com
   - Subject: Test Message
   - Message: This is a test message
3. Click **Send Message**
4. You should see a success message ✅

---

## Step 5: View in Admin Dashboard (1 minute)

1. Go to admin login: `http://localhost/TELECOME%20TEST/admin/login.php`
2. Login with default credentials
3. You should see:
   - Statistics dashboard
   - Your test message in the contacts table
   - Ability to view, update status, and delete contacts

---

## 🎉 Congratulations!

Your telecom website is now running! Here's what you can do next:

### Explore Features

- ✅ **Homepage** - Hero section, features, plans, testimonials
- ✅ **Plans Page** - All internet and mobile plans
- ✅ **Contact Page** - Secure form with validation
- ✅ **Admin Dashboard** - Manage contact submissions
- ✅ **Dark Mode** - Click the moon/sun icon in the header
- ✅ **Responsive Design** - Resize your browser to see mobile view

### Customize Your Site

1. **Change Site Name**
   - Edit `config.php`
   - Update `SITE_NAME` constant

2. **Update Colors**
   - Edit `public/assets/css/style.css`
   - Modify CSS variables in `:root` section

3. **Add Your Content**
   - Edit `public/index.php` for homepage
   - Edit `public/plans.php` for plans
   - Edit `public/contact.php` for contact page

4. **Change Admin Password**
   - Login to admin panel
   - Use this SQL in phpMyAdmin:
   ```sql
   UPDATE admin_users 
   SET password_hash = '$2y$10$YOUR_NEW_HASH' 
   WHERE username = 'admin';
   ```
   - Generate hash in PHP:
   ```php
   echo password_hash('YourNewPassword', PASSWORD_BCRYPT);
   ```

---

## 🐛 Troubleshooting

### "Database connection error"

**Problem:** Can't connect to database

**Solutions:**
1. Check MySQL is running in XAMPP
2. Verify database name is `telecom_db`
3. Check username/password in `config.php`
4. Ensure database was imported correctly

### "404 Not Found"

**Problem:** Page not found

**Solutions:**
1. Check the URL path matches your folder structure
2. Ensure Apache is running in XAMPP
3. Verify files are in the correct location
4. Check file permissions (should be readable)

### "CSRF token error"

**Problem:** Form submission fails with CSRF error

**Solutions:**
1. Clear browser cookies
2. Refresh the page
3. Check session is working (PHP session extension enabled)

### Dark mode not working

**Problem:** Theme toggle doesn't work

**Solutions:**
1. Check browser console for JavaScript errors
2. Ensure `main.js` is loading correctly
3. Check localStorage is enabled in browser
4. Clear browser cache

### Admin login fails

**Problem:** Can't login with default credentials

**Solutions:**
1. Verify database was imported correctly
2. Check `admin_users` table exists
3. Ensure default admin user was created
4. Try resetting password using SQL

---

## 📚 Next Steps

1. **Read the full README.md** for detailed documentation
2. **Review TESTING_CHECKLIST.md** to test all features
3. **Customize the design** to match your brand
4. **Add your own content** and images
5. **Test security features** before going live
6. **Deploy to production** when ready

---

## 🆘 Need Help?

1. **Check README.md** - Comprehensive documentation
2. **Review TESTING_CHECKLIST.md** - Common issues and solutions
3. **Check browser console** - Look for JavaScript errors
4. **Check PHP error logs** - Look in XAMPP logs folder
5. **Verify database** - Use phpMyAdmin to check data

---

## 🔐 Security Reminders

Before deploying to production:

- [ ] Change default admin password
- [ ] Update `SECRET_KEY` in config.php
- [ ] Set `ENVIRONMENT` to 'production'
- [ ] Enable HTTPS (Let's Encrypt)
- [ ] Set proper file permissions
- [ ] Remove or protect config.sample.php
- [ ] Test all security features

---

**Happy coding! 🚀**

Built with ❤️ for learners and professionals.
