<?php
<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if first user (admin)
    $is_admin = 0;
    $result = $conn->query("SELECT COUNT(*) as cnt FROM users");
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['cnt'] == 0) $is_admin = 1;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash, is_admin) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password_hash, $is_admin);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful. Please log in.";
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['message'] = "Registration failed: " . $conn->error;
    }
}
?>
<!-- Simple registration form -->
<form method="post">
    <input name="username" required placeholder="Username"><br>
    <input name="password" type="password" required placeholder="Password"><br>
    <button type="submit">Register</button>
</form>
<?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>