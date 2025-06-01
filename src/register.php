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

    // Prevent duplicate usernames or emails
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
            $message = "Registration successful. Please <a href='login.php'>login</a>.";
        } else {
            $message = "Registration failed.";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - QR Code Tracker</title>
</head>
<body>
    <h2>Register</h2>
    <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
    <form method="post">
        <label>Username: <input name="username" required></label><br>
        <label>Email: <input name="email" type="email" required></label><br>
        <label>Password: <input name="password" type="password" required></label><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>