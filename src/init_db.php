<?php
require_once 'config.php'; // Includes $conn

echo "<h1>Database Initialization</h1>";

// SQL to create qr_codes table
$sql_qr_codes = "CREATE TABLE IF NOT EXISTS qr_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_url TEXT NOT NULL,
    short_code VARCHAR(10) UNIQUE NOT NULL,
    image_filename VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";

// SQL to create scan_logs table
$sql_scan_logs = "CREATE TABLE IF NOT EXISTS scan_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    qr_code_id INT NOT NULL,
    scan_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (qr_code_id) REFERENCES qr_codes(id) ON DELETE CASCADE
);";

// SQL to create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";

// Execute create table for qr_codes
if ($conn->query($sql_qr_codes) === TRUE) {
    echo "<p>Table 'qr_codes' checked/created successfully.</p>";
} else {
    echo "<p>Error creating table 'qr_codes': " . $conn->error . "</p>";
}

// Execute create table for scan_logs
if ($conn->query($sql_scan_logs) === TRUE) {
    echo "<p>Table 'scan_logs' checked/created successfully.</p>";
} else {
    echo "<p>Error creating table 'scan_logs': " . $conn->error . "</p>";
}

// Execute create table for users
if ($conn->query($sql_users) === TRUE) {
    echo "<p>Table 'users' checked/created successfully.</p>";
} else {
    echo "<p>Error creating table 'users': " . $conn->error . "</p>";
}

// Add user_id to qr_codes
$conn->query("ALTER TABLE qr_codes ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL");

// Create default admin user if not exists
$admin_username = 'admin';
$admin_password = password_hash('changeme', PASSWORD_DEFAULT);
$check_admin = $conn->prepare("SELECT id FROM users WHERE username=?");
$check_admin->bind_param("s", $admin_username);
$check_admin->execute();
$check_admin->store_result();
if ($check_admin->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, is_admin) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $admin_username, $admin_password);
    $stmt->execute();
    $stmt->close();
}
$check_admin->close();
$conn->close();

echo "<p><a href='index.php'>Go to Home</a></p>";
?>
