<?php
session_start();
require __DIR__ . '/config/db.php'; // ✅ Fixed path

// Redirect if already logged in
if(isset($_SESSION['user_id'])){
    header("Location: user.php");
    exit;
}

$error = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email_or_username = trim($_POST['email_or_username']);
    $password = trim($_POST['password']);

    if($email_or_username && $password){
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email=? OR full_name=? LIMIT 1");
        $stmt->bind_param("ss", $email_or_username, $email_or_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                header("Location: user.php");
                exit;
            } else {
                $error = "Invalid email/username or password.";
            }
        } else {
            $error = "Invalid email/username or password.";
        }
        $stmt->close();
    } else {
        $error = "Please enter both email/username and password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/Login.css">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <a href="index.html">
            <div class="title">
                <img src="img/icon.png" alt="start icon" class="icon">
             <a href="index.php" style="text-decoration: none;">
                <h1 style="color:#9FB9CC;">Like the Sun that shines but doesn't burn</h1>
            </a>
                <img src="img/icon.png" alt="end icon" class="icon">
            </div>
        </a>
    </div>

    <div class="login-container">
        <h2 style="color: #9FB9CC;">Login</h2>

        <!-- Login Form -->
        <form method="POST" action="">
            <input type="text" name="email_or_username" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

        <?php if($error): ?>
            <p style="color:#eee; margin-top: 10px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <p style="margin-top: 10px; font-size: 14px;">
            <a href="forgot-password.html" class="forgot-password">Forgot Password?</a>
        </p>

        <p style="margin-top: 5px; font-size: 14px;">
            Don't have an account? <a href="Signup.php">Sign Up</a>
        </p>
    </div>

</body>
</html>
