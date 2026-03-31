#!/bin/bash
set -e

# Linux/Replit startup helper.
# On Windows/XAMPP, use Apache and MySQL from XAMPP instead of this script.

WORKSPACE="/home/runner/workspace"
MYSQL_DATA="$WORKSPACE/.mysql/data"
MYSQL_LOG="$WORKSPACE/.mysql/log"
MYSQL_SOCK="/tmp/mysql.sock"
MYSQL_PID="/tmp/mysql.pid"

mkdir -p "$MYSQL_LOG" /tmp/nginx_client_body /tmp/nginx_proxy /tmp/nginx_fastcgi /tmp/nginx_uwsgi /tmp/nginx_scgi /tmp/sessions

echo "==> Starting MySQL..."
pkill -9 mysqld 2>/dev/null || true
sleep 1
rm -f "$MYSQL_SOCK" "$MYSQL_SOCK.lock" "$MYSQL_PID"

mysqld \
  --datadir="$MYSQL_DATA" \
  --socket="$MYSQL_SOCK" \
  --pid-file="$MYSQL_PID" \
  --log-error="$MYSQL_LOG/mysql_error.log" \
  --port=3306 \
  --user=runner \
  --mysqlx=0 \
  --sql_mode="NO_ENGINE_SUBSTITUTION" \
  --daemonize=ON

echo "==> Waiting for MySQL to be ready..."
for i in $(seq 1 60); do
  if mysql -S "$MYSQL_SOCK" -u root --connect-timeout=2 -e "SELECT 1;" 2>/dev/null; then
    echo "==> MySQL ready after ${i} attempts!"
    break
  fi
  sleep 0.5
done

echo "==> Setting up database..."
mysql -S "$MYSQL_SOCK" -u root 2>/dev/null <<'ENDSQL' || true
CREATE DATABASE IF NOT EXISTS travelers_DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET GLOBAL sql_mode='NO_ENGINE_SUBSTITUTION';
ENDSQL

DB_TABLES=$(mysql -S "$MYSQL_SOCK" -u root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='travelers_DB';" 2>/dev/null || echo "0")

if [ "$DB_TABLES" -lt "10" ]; then
  echo "==> Importing database schema (found $DB_TABLES tables, need at least 10)..."
  LATEST_BACKUP=$(ls -t "$WORKSPACE/backups/"*.sql 2>/dev/null | head -1)
  if [ -n "$LATEST_BACKUP" ]; then
    mysql -S "$MYSQL_SOCK" -u root --init-command="SET sql_mode='NO_ENGINE_SUBSTITUTION';" travelers_DB < "$LATEST_BACKUP" 2>/dev/null && echo "==> Database imported from $LATEST_BACKUP" || echo "==> Import completed (may have had minor errors)"
  fi
else
  echo "==> Database already has $DB_TABLES tables, skipping import."
fi

echo "==> Applying schema migrations..."
mysql -S "$MYSQL_SOCK" -u root travelers_DB 2>/dev/null <<'MIGRATIONS' || true
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS refund_amount DECIMAL(10,2) DEFAULT 0.00 AFTER refund;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS type VARCHAR(50) DEFAULT 'system' AFTER message;
ALTER TABLE user_cred ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE user_cred ADD COLUMN IF NOT EXISTS token VARCHAR(255) DEFAULT NULL;
ALTER TABLE user_cred ADD COLUMN IF NOT EXISTS t_expire DATE DEFAULT NULL;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS payment_status ENUM('pending','partial','paid') DEFAULT 'pending';
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(255) DEFAULT NULL;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS amount_paid DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS total_amt DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS downpayment DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS balance_due DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_order ADD COLUMN IF NOT EXISTS confirmed_at DATETIME DEFAULT NULL;
ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS booking_note TEXT NULL;
ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS staff_note TEXT NULL;
ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS extras_total DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS downpayment DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE booking_details ADD COLUMN IF NOT EXISTS remaining_balance DECIMAL(10,2) DEFAULT 0.00;
UPDATE user_cred SET is_verified=1, token=NULL WHERE is_verified=0 AND status=1;
CREATE TABLE IF NOT EXISTS admin_users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  email VARCHAR(255) DEFAULT NULL,
  reset_token VARCHAR(64) DEFAULT NULL,
  reset_expires DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS reset_expires DATETIME DEFAULT NULL;
MIGRATIONS

echo "==> Schema migrations applied."

echo "==> Starting PHP-FPM..."
pkill -9 php-fpm 2>/dev/null || true
rm -f /tmp/php-fpm.sock /tmp/php-fpm.pid
php-fpm --fpm-config "$WORKSPACE/.config/php-fpm/php-fpm.conf" &

sleep 2

echo "==> Starting Nginx on port 5000..."
exec nginx -c "$WORKSPACE/.config/nginx/nginx.conf" -e /tmp/nginx_error.log -g "daemon off;"
