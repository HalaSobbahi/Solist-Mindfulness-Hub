<?php
session_start();
require __DIR__ . '/config/db.php';

$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists
    $stmt = $conn->prepare("SELECT id, token_expiry FROM users WHERE reset_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if token is expired
        if (strtotime($user['token_expiry']) < time()) {
            $message = "Token has expired. Please request a new reset link.";
        } else {
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = trim($_POST['password']);
                $confirm_password = trim($_POST['confirm_password']);

                // Password strength check
                if (strlen($password) < 8) {
                    $message = "Password must be at least 8 characters long.";
                } elseif (!preg_match('/[!@#$%^&*(),.?\":{}|<>]/', $password)) {
                    $message = "Password must include at least one special character (!@#$%^&* etc.).";
                } elseif ($password !== $confirm_password) {
                    $message = "Passwords do not match.";
                } else {
                    // Hash password and save
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
                    $stmt->bind_param("si", $hashed, $user['id']);
                    $stmt->execute();
                    $message = "Password successfully updated. You can now <a href='Login.php'>login</a>.";
                }
            }
        }
    } else {
        $message = "Invalid token.";
    }
} else {
    $message = "No token provided.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/Login.css">
</head>

<body>
    <!-- Reset Password Form -->
    <div class="login-container">
        <h2 style="color: #9FB9CC;">Reset Password</h2>

        <?php if (strpos($message, "successfully") === false): ?>
            <form method="POST" action="">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <p style="color:#eee; margin-top:10px;"><?php echo $message; ?></p>
    </div>
</body>

</html>