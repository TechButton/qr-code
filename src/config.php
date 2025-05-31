<?php
// Database configuration
define('DB_HOST', 'db'); // This is the service name from docker-compose.yml
define('DB_USER', 'qruser');
define('DB_PASSWORD', 'qrpassword');
define('DB_NAME', 'qr_tracker_db');

// Base URL of your application (important for generating redirect links)
// Adjust if your domain/port is different or if running in a subdirectory
define('BASE_URL', 'http://localhost:8000/');

// Directory to store QR code images (relative to this src/ directory)
define('QR_CODE_DIR', 'qrcodes/');

// Create a database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>