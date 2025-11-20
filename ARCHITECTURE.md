# Architecture Overview

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT BROWSER                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  HTML5   │  │   CSS3   │  │    JS    │  │LocalStore│   │
│  │ Semantic │  │  Modern  │  │   ES6+   │  │Dark Mode │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ HTTPS
┌─────────────────────────────────────────────────────────────┐
│                      WEB SERVER (Apache)                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   PUBLIC PAGES                        │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐           │  │
│  │  │index.php │  │plans.php │  │contact.php│           │  │
│  │  │ (Home)   │  │ (Plans)  │  │ (Form)   │           │  │
│  │  └──────────┘  └──────────┘  └──────────┘           │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                   ADMIN PANEL                         │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐           │  │
│  │  │login.php │  │dashboard │  │ajax_get  │           │  │
│  │  │  (Auth)  │  │ (Manage) │  │_contact  │           │  │
│  │  └──────────┘  └──────────┘  └──────────┘           │  │
│  └──────────────────────────────────────────────────────┘  │
│                            ↕                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  BACKEND LOGIC (PHP)                  │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐           │  │
│  │  │  db.php  │  │ auth.php │  │functions │           │  │
│  │  │   (PDO)  │  │(Session) │  │ (Helpers)│           │  │
│  │  └──────────┘  └──────────┘  └──────────┘           │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↕ PDO
┌─────────────────────────────────────────────────────────────┐
│                   DATABASE (MySQL/MariaDB)                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Tables:                                              │  │
│  │  • contacts (form submissions)                        │  │
│  │  • admin_users (authentication)                       │  │
│  │  • login_attempts (security)                          │  │
│  │  • rate_limits (anti-spam)                            │  │
│  │                                                        │  │
│  │  Views:                                                │  │
│  │  • view_recent_contacts                               │  │
│  │  • view_contact_stats                                 │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔄 Request Flow

### Public Contact Form Submission

```
User fills form
    ↓
JavaScript validates (client-side)
    ↓
Form submits to contact.php
    ↓
PHP validates (server-side)
    ↓
Check CSRF token
    ↓
Check honeypot field
    ↓
Check rate limit (IP-based)
    ↓
Sanitize input
    ↓
Save to database (PDO prepared statement)
    ↓
Set flash message
    ↓
Redirect to success page
    ↓
Display success message
```

### Admin Login Flow

```
User enters credentials
    ↓
Submit to login.php
    ↓
Check CSRF token
    ↓
Check IP lockout
    ↓
Validate username/password
    ↓
Query admin_users table
    ↓
Verify password hash
    ↓
Check account status
    ↓
Log login attempt
    ↓
Create secure session
    ↓
Regenerate session ID
    ↓
Redirect to dashboard
```

### Admin Dashboard View

```
User accesses dashboard.php
    ↓
Check authentication
    ↓
Get current admin info
    ↓
Get contact statistics
    ↓
Apply filters (status, search)
    ↓
Query contacts table
    ↓
Paginate results
    ↓
Render dashboard HTML
    ↓
User clicks "View" button
    ↓
AJAX request to ajax_get_contact.php
    ↓
Return JSON data
    ↓
Display in modal
```

---

## 🔐 Security Layers

```
┌─────────────────────────────────────────┐
│         Layer 1: Input Validation       │
│  • Client-side JS validation            │
│  • Server-side PHP validation           │
│  • Type checking                        │
│  • Length limits                        │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│         Layer 2: CSRF Protection        │
│  • Token generation                     │
│  • Token validation                     │
│  • Session-based tokens                 │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│      Layer 3: SQL Injection Prevention  │
│  • PDO prepared statements              │
│  • Parameter binding                    │
│  • No string concatenation              │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│         Layer 4: XSS Prevention         │
│  • htmlspecialchars() on output         │
│  • ENT_QUOTES flag                      │
│  • UTF-8 encoding                       │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│       Layer 5: Authentication           │
│  • Password hashing (bcrypt)            │
│  • Secure sessions                      │
│  • Account lockout                      │
│  • Login attempt logging                │
└─────────────────────────────────────────┘
                  ↓
┌─────────────────────────────────────────┐
│        Layer 6: Rate Limiting           │
│  • IP-based throttling                  │
│  • Honeypot field                       │
│  • Time-window enforcement              │
└─────────────────────────────────────────┘
```

---

## 📊 Data Flow Diagram

### Contact Submission

```
┌──────────┐
│  User    │
└────┬─────┘
     │ Fills form
     ↓
┌──────────────┐
│ contact.php  │
└──────┬───────┘
       │ Validates
       ↓
┌──────────────┐
│ functions.php│
└──────┬───────┘
       │ validateContactForm()
       │ checkRateLimit()
       │ saveContactSubmission()
       ↓
┌──────────────┐
│   db.php     │
└──────┬───────┘
       │ PDO execute()
       ↓
┌──────────────┐
│   MySQL      │
│  contacts    │
└──────┬───────┘
       │ Insert success
       ↓
┌──────────────┐
│ Flash message│
└──────┬───────┘
       │ Redirect
       ↓
┌──────────────┐
│Success page  │
└──────────────┘
```

### Admin View Contacts

```
┌──────────┐
│  Admin   │
└────┬─────┘
     │ Accesses dashboard
     ↓
┌──────────────┐
│dashboard.php │
└──────┬───────┘
       │ requireAuth()
       ↓
┌──────────────┐
│  auth.php    │
└──────┬───────┘
       │ isLoggedIn()
       ↓
┌──────────────┐
│functions.php │
└──────┬───────┘
       │ getContacts()
       │ getContactStats()
       ↓
┌──────────────┐
│   db.php     │
└──────┬───────┘
       │ PDO query()
       ↓
┌──────────────┐
│   MySQL      │
│  contacts    │
│  views       │
└──────┬───────┘
       │ Return data
       ↓
┌──────────────┐
│Render HTML   │
└──────────────┘
```

---

## 🗂️ File Dependencies

### Public Pages

```
index.php
├── config.php
├── src/functions.php
│   ├── src/db.php
│   │   └── config.php
│   └── config.php
├── assets/css/style.css
└── assets/js/main.js

plans.php
├── config.php
├── src/functions.php
└── [same assets]

contact.php
├── config.php
├── src/db.php
├── src/functions.php
└── [same assets]
```

### Admin Pages

```
login.php
├── config.php
├── src/db.php
├── src/auth.php
│   ├── src/db.php
│   └── config.php
├── src/functions.php
└── [assets]

dashboard.php
├── config.php
├── src/db.php
├── src/auth.php
├── src/functions.php
└── [assets]

ajax_get_contact.php
├── config.php
├── src/db.php
├── src/auth.php
└── src/functions.php
```

---

## 🎨 Component Hierarchy

### Frontend Components

```
Page Layout
├── Header
│   ├── Logo
│   ├── Navigation
│   │   ├── Nav Links
│   │   └── Mobile Menu Toggle
│   └── Theme Toggle
├── Main Content
│   ├── Hero Section (index only)
│   ├── Features Grid (index only)
│   ├── Plans Grid (index, plans)
│   ├── Contact Form (contact only)
│   └── CTA Section
└── Footer
    ├── Footer Grid
    │   ├── About
    │   ├── Quick Links
    │   ├── Support
    │   └── Contact Info
    └── Copyright
```

### Admin Components

```
Admin Layout
├── Admin Header
│   ├── Title
│   ├── User Info
│   ├── Theme Toggle
│   └── Logout Button
├── Dashboard Content
│   ├── Statistics Grid
│   │   ├── Total Contacts
│   │   ├── New Messages
│   │   ├── Replied
│   │   ├── Today
│   │   └── This Week
│   ├── Filters
│   │   ├── Search Input
│   │   ├── Status Filter
│   │   └── Export Button
│   ├── Contacts Table
│   │   ├── Table Headers
│   │   ├── Table Rows
│   │   │   ├── Contact Data
│   │   │   └── Action Buttons
│   │   └── Pagination
│   └── Modal
│       ├── Contact Details
│       └── Status Update Form
└── [No footer in admin]
```

---

## 🔄 State Management

### Client-Side State (JavaScript)

```
localStorage
├── theme ('light' or 'dark')
└── [future: user preferences]

sessionStorage
└── [not currently used]

DOM State
├── Modal open/closed
├── Mobile menu open/closed
├── Form validation errors
└── Alert messages
```

### Server-Side State (PHP Sessions)

```
$_SESSION
├── csrf_token (CSRF protection)
├── admin_logged_in (boolean)
├── admin_id (user ID)
├── admin_username (username)
├── login_time (timestamp)
├── ip_address (security)
├── created (session age)
└── flash_message (temporary messages)
    ├── type ('success', 'error', etc.)
    └── message (text)
```

---

## 🗄️ Database Relationships

```
admin_users (1) ──< (many) login_attempts
    │
    └── username matches username in login_attempts

contacts (independent table)
    │
    └── No foreign keys (standalone submissions)

rate_limits (independent table)
    │
    └── Keyed by ip_address + action
```

---

## 🚀 Deployment Architecture

### Development Environment

```
Local Machine (XAMPP)
├── Apache (localhost:80)
├── MySQL (localhost:3306)
├── PHP 8.0+
└── Browser (testing)
```

### Production Environment

```
Web Server (VPS/Shared Hosting)
├── Apache/Nginx (port 80/443)
├── MySQL/MariaDB (port 3306)
├── PHP 8.0+ (FPM)
├── SSL Certificate (Let's Encrypt)
├── Firewall (UFW/iptables)
└── Monitoring (optional)
```

---

## 📦 Technology Stack

```
┌─────────────────────────────────────┐
│          Frontend Layer             │
│  • HTML5 (semantic)                 │
│  • CSS3 (variables, grid, flexbox)  │
│  • JavaScript ES6+ (vanilla)        │
│  • Google Fonts (Inter)             │
└─────────────────────────────────────┘
                ↕
┌─────────────────────────────────────┐
│          Backend Layer              │
│  • PHP 8.0+ (OOP, PDO)              │
│  • Session management               │
│  • Password hashing                 │
│  • File system (logs)               │
└─────────────────────────────────────┘
                ↕
┌─────────────────────────────────────┐
│         Database Layer              │
│  • MySQL 5.7+ / MariaDB 10.3+       │
│  • InnoDB engine                    │
│  • utf8mb4 charset                  │
│  • Stored procedures                │
│  • Views, triggers, events          │
└─────────────────────────────────────┘
```

---

## 🔧 Configuration Flow

```
config.sample.php (template)
         ↓ (copy & edit)
config.php (actual config)
         ↓ (required by)
    ┌────┴────┐
    ↓         ↓
src/*.php   public/*.php
    ↓         ↓
Database   Web Pages
```

---

## 📈 Performance Optimization Points

```
Browser
├── CSS minification (production)
├── JS minification (production)
├── Image optimization
├── Lazy loading
└── Browser caching

Server
├── OPcache (PHP)
├── Query optimization
├── Database indexes
├── Gzip compression
└── CDN (optional)

Database
├── Indexed columns
├── Query caching
├── Connection pooling
└── Regular optimization
```

---

This architecture provides:
- ✅ Clear separation of concerns
- ✅ Scalable structure
- ✅ Security at every layer
- ✅ Easy maintenance
- ✅ Performance optimization
- ✅ Professional organization
