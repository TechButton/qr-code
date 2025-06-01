<?php
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
            $message = "Password reset! <a href='login.php'>Login</a>";
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
<html>
<head><title>Reset Password</title></head>
<body>
<h2>Reset Password</h2>
<?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
<?php if (isset($_GET['token']) && !$message): ?>
<form method="post">
    <label>New Password: <input type="password" name="new_password" required></label><br>
    <button type="submit">Reset Password</button>
</form>
<?php endif; ?>
</body>
</html>