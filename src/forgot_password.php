<?php
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
        // In production, send email. For demo, just show the link:
        $reset_link = BASE_URL . "reset_password.php?token=$token";
        $message = "Password reset link (demo): <a href='$reset_link'>$reset_link</a>";
    } else {
        $message = "Email not found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
<h2>Forgot Password</h2>
<?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
<form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Send Reset Link</button>
</form>
</body>
</html>