<?php
session_start();
require __DIR__ . '/config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if ($email) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token valid for 1 hour
            
            // Store token in database
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, $expiry, $user['id']);
            $stmt->execute();

            // Generate reset link (for testing on localhost)
            $reset_link = "http://localhost/solist/reset-password.php?token=" . $token;
            
            // Instead of mail(), just show the link for testing
            $message = "Password reset link (for testing purposes): <a href='$reset_link'>$reset_link</a>";

        } else {
            $message = "No account found with that email.";
        }
    } else {
        $message = "Please enter your email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link rel="stylesheet" href="css/Login.css">
</head>
<body>
    <!-- Forgot Password Form -->
<div class="login-container">
    <h2 style="color: #9FB9CC;">Forgot Password</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <?php if($message): ?>
        <p style="color:#eee; margin-top:10px;"><?php echo $message; ?></p>
    <?php endif; ?>
    <p><a href="Login.php">Back to Login</a></p>
</div>
</body>
</html>
