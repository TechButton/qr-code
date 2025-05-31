<?php
require_once 'vendor/autoload.php'; // Composer autoloader
require_once 'config.php';         // DB connection and constants
session_start();                   // For feedback messages

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_url'])) {
    $data_url = trim($_POST['data_url']);

    if (empty($data_url)) {
        $_SESSION['message'] = "Error: URL or text cannot be empty.";
        $_SESSION['message_type'] = "error";
        header('Location: index.php');
        exit;
    }

    // Generate a short unique code
    $short_code = substr(md5(uniqid(rand(), true)), 0, 8); // Simple short code

    // Define image filename and path
    $image_filename = 'qr_' . $short_code . '.png';
    $qr_code_storage_path = QR_CODE_DIR;

    // Create the qrcodes directory if it doesn't exist
    if (!is_dir($qr_code_storage_path)) {
        if (!mkdir($qr_code_storage_path, 0755, true)) {
            $_SESSION['message'] = "Error: Failed to create QR code storage directory.";
            $_SESSION['message_type'] = "error";
            header('Location: index.php');
            exit;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($qr_code_storage_path)) {
        $_SESSION['message'] = "Error: QR code storage directory is not writable. Please check permissions for '" . realpath($qr_code_storage_path) . "'.";
        $_SESSION['message_type'] = "error";
        header('Location: index.php');
        exit;
    }

    $full_image_path = $qr_code_storage_path . $image_filename;
    $redirect_url = BASE_URL . 'redirect.php?code=' . $short_code;

    try {
        // Generate QR Code using endroid/qr-code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($redirect_url) // Encode the redirect URL
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            // ->logoPath(__DIR__.'/assets/symfony.png') // Optional: Add a logo
            // ->logoResizeToWidth(50)
            // ->logoPunchoutBackground(true)
            // ->validateResult(false) // Set to true to validate the result
            ->build();

        // Save the QR code image
        $result->saveToFile($full_image_path);

        // Save QR code details to the database
        $stmt = $conn->prepare("INSERT INTO qr_codes (data_url, short_code, image_filename) VALUES (?, ?, ?)");
        if ($stmt === false) {
             throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("sss", $data_url, $short_code, $image_filename);

        if ($stmt->execute()) {
            $_SESSION['message'] = "QR Code generated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        $_SESSION['message'] = "Error generating QR Code: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        // Clean up image if created before DB error
        if (file_exists($full_image_path)) {
            unlink($full_image_path);
        }
    }

    $conn->close();
    header('Location: index.php');
    exit;

} else {
    // Not a POST request or data_url not set
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "error";
    header('Location: index.php');
    exit;
}
?>