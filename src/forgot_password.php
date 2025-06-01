<?php
require_once 'config.php';
session_start();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    if ($stmt->fetch()) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        $stmt->close();
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
        $stmt->bind_param("ssi", $token, $expires, $user_id);
        $stmt->execute();
        $reset_link = BASE_URL . "reset_password.php?token=$token";
        $message = "Password reset link (demo): <a href='$reset_link' class='text-indigo-600 hover:underline'>$reset_link</a>";
    } else {
        $message = "Email not found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - QR Code Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8">
    <div class="container mx-auto max-w-md bg-white p-6 md:p-8 rounded-lg shadow-xl mt-16">
        <h2 class="text-2xl font-bold text-slate-700 mb-6 text-center">Forgot Password</h2>
        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded-md bg-<?php echo strpos($message, 'link') !== false ? 'green' : 'red'; ?>-100 text-<?php echo strpos($message, 'link') !== false ? 'green' : 'red'; ?>-700 text-center"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Send Reset Link
            </button>
        </form>
        <div class="mt-4 text-center text-sm text-slate-600">
            <a href="login.php" class="text-indigo-600 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>