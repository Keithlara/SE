# Travelers Place - Hotel Reservation & Billing System

## Overview
A PHP + MySQL web application for hotel room reservations, user management, and admin operations. The system is called "Travelers Place" and supports room browsing, booking, payment, and an admin dashboard.

## Architecture

- **Backend**: PHP 8.2 (procedural + some OOP)
- **Database**: MySQL 8.0 (travelers_DB)
- **Web Server**: Nginx 1.28 (port 5000) + PHP-FPM
- **Frontend**: Bootstrap 5, Swiper.js, SweetAlert2, Flatpickr, vanilla JS

## Project Layout

```
/
├── admin/                 # Admin dashboard
│   ├── ajax/              # Admin AJAX handlers
│   │   └── confirm_booking.php  # Booking confirmation + billing notification
│   ├── inc/               # db_config.php, essentials.php (core config)
│   │   ├── admin_users_table.php  # Creates/upgrades admin_users table (email, reset_token)
│   │   └── email_config.php       # SMTP credentials from env vars
│   ├── forgot_password.php        # Admin forgot-password form
│   ├── reset_admin_password.php   # Admin reset-password landing page
│   ├── create_user.php            # Create admin/staff accounts (includes email field)
│   └── index.php                  # Admin login (username OR email)
├── ajax/
│   ├── confirm_booking.php        # Availability check (returns room_total, days, price_night)
│   └── login_register.php         # User registration, login, forgot-password
├── inc/
│   ├── booking_notifications.php  # Email/SMS notification functions (billing breakdown)
│   ├── smtp_mailer.php            # PHPMailer + raw SMTP fallback
│   └── notifications_functions.php
├── backups/               # MySQL SQL dumps (import on first run)
├── confirm_booking.php    # Room booking page (live billing breakdown UI)
├── pay_now.php            # Booking submission + downpayment calc + guest email
├── bookings.php           # User booking history (shows billing summary per booking)
├── start.sh               # Startup script (MySQL + PHP-FPM + Nginx)
└── .config/               # Nginx and PHP-FPM config files
```

## Configuration Files

### Database Config: `admin/inc/db_config.php`
- Reads env vars: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_SOCK`
- Defaults: host=localhost, port=3306, name=travelers_DB, user=root, sock=/tmp/mysql.sock

### Site Config: `admin/inc/essentials.php`
- `SITE_URL`: dynamically built from request host + forwarded headers
- `UPLOAD_IMAGE_PATH`, `UPLOADS_PATH`, `ROOMS_IMG_PATH`, `SITE_NAME`, etc.

## Services

The `start.sh` script launches:
1. **MySQL 8.0** — socket at `/tmp/mysql.sock`, port 3306
2. **PHP-FPM** — listening on `127.0.0.1:9000`
3. **Nginx** — listening on `0.0.0.0:5000`

On first start, the latest SQL backup from `backups/` is automatically imported into `travelers_DB`.

## Database Schema (key tables)

- `user_cred` — guests: id, name, email, password, is_verified, token, t_expire, profile, ...
- `admin_users` — admin/staff: id, username, password, role, **email**, **reset_token**, **reset_expires**, created_at
- `admin_cred` — legacy admin table (plaintext passwords, fallback only)
- `booking_order` — id, user_id, room_id, check_in, check_out, booking_status, payment_status, payment_proof, **total_amt**, **downpayment**, **balance_due**, trans_amt, ...
- `booking_details` — booking_id, room_name, price, total_pay, user_name, phonenum, address, room_no, booking_note, staff_note
- `booking_extras` — add-ons per booking

## Email Configuration (Gmail SMTP)

Set these secrets in Replit:
- `SMTP_USER` — your full Gmail address (e.g. you@gmail.com)
- `SMTP_PASS` — a Gmail App Password (Google Account → Security → 2-Step Verification → App passwords)

Optional:
- `SMTP_FROM` — sender address (defaults to SMTP_USER)
- `SMTP_FROM_NAME` — sender display name
- `SITE_NAME` — hotel name in emails

## Features

### Guest-facing
- Room browsing and availability checking
- User registration with email verification (Gmail SMTP)
- Login with verified-account enforcement
- Forgot password with reset email flow
- Booking with date selection and add-on extras
- **Live billing breakdown** on booking page: Room charge, Extras, Total, Downpayment (50%), Balance at check-in
- **Booking received email** sent immediately when guest submits booking (includes full billing breakdown)
- Payment via GCash/Maya (manual proof upload)
- Booking confirmation email with billing details when admin confirms

### Admin-facing
- Login by **username OR email**
- **Forgot password** flow (email reset link, 1-hour expiry)
- Create admin/staff users with optional email field
- Room management, booking management, reports
- Confirm/cancel bookings, assign room numbers
- Staff notes on bookings

## Billing/Payment Model

1. Guest selects dates + add-ons → sees live breakdown: Total, Downpayment (50%), Balance at check-in
2. Guest uploads GCash/Maya payment proof for the downpayment (50%)
3. `booking_order` stores: `total_amt` (full cost), `downpayment` (50%), `balance_due` (remaining), `trans_amt` (= downpayment)
4. Admin confirms → booking status → `booked`; confirmation email includes billing summary
5. Guest pays remaining balance at check-in

## Auth Sources
- Admin login tries `admin_users` first (hashed passwords), falls back to legacy `admin_cred` (plaintext)
- Guests log in via `user_cred` (hashed passwords, must be verified)
