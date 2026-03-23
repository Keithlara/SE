#!/bin/bash
MYSQL_DATA="/home/runner/workspace/.mysql/data"
MYSQL_SOCK="/tmp/mysql.sock"
MYSQL_PID="/tmp/mysql.pid"
MYSQL_LOG="/home/runner/workspace/.mysql/log/error.log"

# Clean up old socket
rm -f "$MYSQL_SOCK" "$MYSQL_SOCK.lock" "$MYSQL_PID"

exec mysqld \
  --datadir="$MYSQL_DATA" \
  --socket="$MYSQL_SOCK" \
  --pid-file="$MYSQL_PID" \
  --log-error="$MYSQL_LOG" \
  --port=3306 \
  --user=runner \
  --mysqlx=0
