<?php
// Database configuration
define('DB_HOST', 'db');
define('DB_USER', getenv('MYSQL_USER') ?: 'qruser');
define('DB_PASSWORD', getenv('MYSQL_PASSWORD') ?: 'qrpassword');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'qr_tracker_db');

// Use Apache server name from environment, fallback to localhost
$serverName = getenv('APACHE_SERVER_NAME') ?: 'localhost';
define('BASE_URL', 'http://' . $serverName . '/');

// Directory to store QR code images (relative to this src/ directory)
define('QR_CODE_DIR', 'qrcodes/');

// Create a database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>