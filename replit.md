# Travelers Place - Hotel Reservation & Billing System

## Overview
A PHP + MySQL web application for hotel room reservations, user management, and admin operations. The system is called "Travelers Place" and supports room browsing, booking, payment, and an admin dashboard.

## Architecture

- **Backend**: PHP 8.2 (procedural + some OOP)
- **Database**: MySQL 8.0 (travelers_DB)
- **Web Server**: Nginx 1.28 (port 5000) + PHP-FPM
- **Frontend**: Bootstrap 5, Swiper.js, SweetAlert2, vanilla JS

## Project Layout

```
/
├── admin/          # Admin dashboard
│   ├── ajax/       # Admin AJAX handlers
│   ├── inc/        # db_config.php, essentials.php (core config)
│   ├── sql/        # Migration scripts
│   └── css/        # Admin styles
├── ajax/           # Client-side AJAX handlers
├── backups/        # MySQL SQL dumps (import on first run)
├── css/            # Client CSS
├── images/         # Static images (rooms, carousel, facilities)
├── inc/            # Shared includes (links.php, footer.php)
├── js/             # Client JavaScript
├── uploads/        # User-uploaded files
├── index.php       # Homepage
├── rooms.php       # Room listing
├── bookings.php    # User booking history
├── start.sh        # Startup script (MySQL + PHP-FPM + Nginx)
└── .config/        # Nginx and PHP-FPM config files
```

## Configuration Files

### Database Config: `admin/inc/db_config.php`
- Reads env vars: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_SOCK`
- Defaults: host=localhost, port=3306, name=travelers_DB, user=root, sock=/tmp/mysql.sock
- Provides both mysqli and PDO connections

### Site Config: `admin/inc/essentials.php`
- `SITE_URL`: dynamically built from request host
- `UPLOAD_IMAGE_PATH`: `$_SERVER['DOCUMENT_ROOT']/images/`
- Payments disabled by default (`PAYMENTS_ENABLED=0`)

## Services

The `start.sh` script launches:
1. **MySQL 8.0** - socket at `/tmp/mysql.sock`, port 3306
2. **PHP-FPM** - listening on `127.0.0.1:9000`
3. **Nginx** - listening on `0.0.0.0:5000`

On first start, the latest SQL backup from `backups/` is automatically imported into `travelers_DB`.

## Data

MySQL data stored in `.mysql/data/`. Logs in `.mysql/log/`.

Config files in `.config/nginx/` and `.config/php-fpm/`.

## User Accounts (from backup)
- Admin panel at `/admin/`
- Check `admin_cred` table for admin credentials

## Email Configuration (Gmail SMTP — free)

Emails are sent via Gmail SMTP using `inc/smtp_mailer.php` and configured in `admin/inc/email_config.php`.

Set these secrets in Replit:
- `SMTP_USER` — your full Gmail address (e.g. you@gmail.com)
- `SMTP_PASS` — a Gmail App Password (Google Account → Security → 2-Step Verification → App passwords)

Optional env vars:
- `SMTP_FROM` — sender address (defaults to SMTP_USER)
- `SMTP_FROM_NAME` — sender display name (defaults to SITE_NAME)
- `SITE_NAME` — hotel name shown in emails (default: Travelers Place)

## Features
- Room browsing and availability checking
- User registration with email verification (Gmail SMTP)
- Login with verified-account enforcement
- Booking with date selection
- Booking confirmation email sent to guests automatically when admin confirms
- Payment integration (Paytm - test mode by default)
- Admin: room management, booking management, reports, user management
