#!/bin/bash
set -e

WORKSPACE="/home/runner/workspace"
MYSQL_DATA="$WORKSPACE/.mysql/data"
MYSQL_LOG="$WORKSPACE/.mysql/log"
MYSQL_SOCK="/tmp/mysql.sock"
MYSQL_PID="/tmp/mysql.pid"

mkdir -p "$MYSQL_LOG" /tmp/nginx_client_temp /tmp/nginx_proxy_temp /tmp/nginx_fastcgi_temp /tmp/nginx_uwsgi_temp /tmp/nginx_scgi_temp

echo "==> Starting MySQL..."
rm -f "$MYSQL_SOCK" "$MYSQL_SOCK.lock" "$MYSQL_PID"

mysqld \
  --datadir="$MYSQL_DATA" \
  --socket="$MYSQL_SOCK" \
  --pid-file="$MYSQL_PID" \
  --log-error="$MYSQL_LOG/mysql_error.log" \
  --port=3306 \
  --user=runner \
  --mysqlx=0 \
  --sql_mode="NO_ENGINE_SUBSTITUTION" &

MYSQL_BG_PID=$!

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
MIGRATIONS

echo "==> Schema migrations applied."

echo "==> Starting PHP-FPM..."
php-fpm --fpm-config "$WORKSPACE/.config/php-fpm/php-fpm.conf" &

sleep 1

echo "==> Starting Nginx on port 5000..."
exec nginx -c "$WORKSPACE/.config/nginx/nginx.conf" -e "$MYSQL_LOG/nginx_error.log" -g "daemon off;"
