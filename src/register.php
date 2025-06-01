<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $message = "Username or email already exists.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, is_admin) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $username, $password_hash, $email);
        if ($stmt->execute()) {
            $message = "Registration successful. Please <a href='login.php' class='text-indigo-600 hover:underline'>login</a>.";
        } else {
            $message = "Registration failed.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - QR Code Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 p-4 md:p-8">
    <div class="container mx-auto max-w-md bg-white dark:bg-slate-800 p-6 md:p-8 rounded-lg shadow-xl mt-16">
        <h2 class="text-2xl font-bold text-slate-700 dark:text-slate-100 mb-6 text-center">Register</h2>
        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded-md bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 text-center"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Username</label>
                <input name="username" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Email</label>
                <input name="email" type="email" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Password</label>
                <input name="password" type="password" required class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-slate-800 dark:text-slate-100">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                Register
            </button>
        </form>
        <div class="mt-4 text-center text-sm text-slate-600 dark:text-slate-300">
            <a href="login.php" class="text-indigo-600 hover:underline">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>