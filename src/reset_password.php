<?php
require_once 'config.php';
session_start();
$message = '';
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new = $_POST['new_password'];
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=? AND reset_expires > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $stmt->close();
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
            $stmt->bind_param("si", $new_hash, $user_id);
            $stmt->execute();
            $message = "Password reset! <a href='login.php' class='text-indigo-600 hover:underline'>Login</a>";
        } else {
            $message = "Invalid or expired token.";
        }
        $stmt->close();
    }
} else {
    $message = "No token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - QR Code Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 p-4 md:p-8">
    <div class="container mx-auto max-w-md bg-white dark:bg-slate-800 p-6 md:p-8 rounded-lg shadow-xl mt-16">
        <h2 class="text-2xl font-bold text-slate-700 dark:text-slate-100 mb-6 text-center">Reset Password</h2>
        <?php if ($message) echo "<div class='mb-4 p-3 rounded-md bg-".(strpos($message, 'reset!') !== false ? 'green' : 'red')."-100 dark:bg-".(strpos($message, 'reset!') !== false ? 'green' : 'red')."-900 text-".(strpos($message, 'reset!') !== false ? 'green' : 'red')."-700 dark:text-".(strpos($message, 'reset!') !== false ? 'green' : 'red')."-200 text-center'>$message</div>"; ?>
        <?php if (isset($_GET['token']) && !$message): ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">New Password</label>
                <input type="password" name="new_password" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 dark:text-slate-100">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Reset Password
            </button>
        </form>
        <?php endif; ?>
        <div class="mt-4 text-center text-sm text-slate-600 dark:text-slate-300">
            <a href="login.php" class="text-indigo-600 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>