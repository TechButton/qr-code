#!/bin/bash
set -e

# Wait for the database to be ready
echo "Waiting for database to be ready..."
until php -r "mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); \$c = new mysqli(getenv('DB_HOST') ?: 'db', getenv('MYSQL_USER') ?: 'qruser', getenv('MYSQL_PASSWORD') ?: 'qrpassword', getenv('MYSQL_DATABASE') ?: 'qr_tracker_db');" 2>/dev/null; do
  sleep 2
done

# Run init_db.php only if tables do not exist
php /var/www/html/init_db.php || true

# Start Apache in the foreground
exec apache2-foreground