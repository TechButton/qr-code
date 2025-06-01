<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hash);
    if ($stmt->fetch() && password_verify($current, $hash)) {
        $stmt->close();
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si", $new_hash, $user_id);
        $stmt->execute();
        $message = "Password changed!";
    } else {
        $message = "Current password incorrect.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - QR Code Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8">
    <div class="container mx-auto max-w-md bg-white p-6 md:p-8 rounded-lg shadow-xl mt-16">
        <h2 class="text-2xl font-bold text-slate-700 mb-6 text-center">Change Password</h2>
        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded-md bg-<?php echo strpos($message, 'changed') !== false ? 'green' : 'red'; ?>-100 text-<?php echo strpos($message, 'changed') !== false ? 'green' : 'red'; ?>-700 text-center"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                <input type="password" name="new_password" required class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Change Password
            </button>
        </form>
        <div class="mt-4 text-center text-sm text-slate-600">
            <a href="index.php" class="text-indigo-600 hover:underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>