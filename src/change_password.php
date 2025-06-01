<?php
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
<html>
<head><title>Change Password</title></head>
<body>
<h2>Change Password</h2>
<?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
<form method="post">
    <label>Current Password: <input type="password" name="current_password" required></label><br>
    <label>New Password: <input type="password" name="new_password" required></label><br>
    <button type="submit">Change Password</button>
</form>
</body>
</html>