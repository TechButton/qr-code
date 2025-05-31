<?php
require_once 'config.php'; // DB connection and constants

if (isset($_GET['code'])) {
    $short_code = trim($_GET['code']);

    // Fetch the original URL from the database
    $stmt_fetch = $conn->prepare("SELECT id, data_url FROM qr_codes WHERE short_code = ?");
    if (!$stmt_fetch) {
        die("Prepare failed (fetch): " . $conn->error);
    }
    $stmt_fetch->bind_param("s", $short_code);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result && $result->num_rows > 0) {
        $qr_code_data = $result->fetch_assoc();
        $original_url = $qr_code_data['data_url'];
        $qr_code_id = $qr_code_data['id'];
        $stmt_fetch->close();

        // Log the scan
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

        $stmt_log = $conn->prepare("INSERT INTO scan_logs (qr_code_id, ip_address, user_agent) VALUES (?, ?, ?)");
        if (!$stmt_log) {
            // Log error but still attempt redirect
            error_log("Prepare failed (log scan): " . $conn->error);
        } else {
            $stmt_log->bind_param("iss", $qr_code_id, $ip_address, $user_agent);
            if (!$stmt_log->execute()) {
                error_log("Execute failed (log scan): " . $stmt_log->error);
            }
            $stmt_log->close();
        }
        
        $conn->close();

        // Redirect to the original URL
        header("Location: " . $original_url, true, 301); // 301 for permanent redirect
        exit;

    } else {
        $stmt_fetch->close();
        $conn->close();
        http_response_code(404);
        die("QR Code not found or invalid.");
    }
} else {
    $conn->close();
    http_response_code(400);
    die("No QR code specified.");
}
?>