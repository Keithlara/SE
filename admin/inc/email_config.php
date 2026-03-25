<?php
// SMTP Configuration
// Set these via environment variables (recommended) or update the defaults below.
// For Gmail: use your full Gmail address as SMTP_USER and a 16-character App Password as SMTP_PASS
// (Google Account → Security → 2-Step Verification → App passwords)

if (!defined('SMTP_HOST'))       define('SMTP_HOST',       getenv('SMTP_HOST')      ?: 'smtp.gmail.com');
if (!defined('SMTP_PORT'))       define('SMTP_PORT',       (int)(getenv('SMTP_PORT') ?: 587));
if (!defined('SMTP_USERNAME'))   define('SMTP_USERNAME',   getenv('SMTP_USER')      ?: '');
if (!defined('SMTP_PASSWORD'))   define('SMTP_PASSWORD',   getenv('SMTP_PASS')      ?: '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', getenv('SMTP_FROM')      ?: getenv('SMTP_USER') ?: '');
if (!defined('SMTP_FROM_NAME'))  define('SMTP_FROM_NAME',  getenv('SMTP_FROM_NAME') ?: (defined('SITE_NAME') ? SITE_NAME : 'Travelers Place'));
