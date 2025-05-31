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

$conn->close();

echo "<p><a href='index.php'>Go to Home</a></p>";
?>
