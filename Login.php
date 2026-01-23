<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Solist Mindfulness Hub</title>
<link rel="stylesheet" href="auth.css">
<link rel="icon" href="../img/logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="auth-body">

<div class="auth-container">
    <img src="../img/logo.png" class="auth-logo">

    <h2>Welcome Back, Solist 🌞</h2>
    <p>Login to continue your journey</p>

    <form class="auth-form">
        <div class="input-box">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" placeholder="Email" required>
        </div>

        <div class="input-box">
            <i class="fa-solid fa-lock"></i>
            <input type="password" placeholder="Password" required>
        </div>

        <button type="submit" class="auth-btn">Login</button>

        <p class="switch-text">
            Don’t have an account?
            <a href="signup.html">Create one</a>
        </p>
    </form>
</div>

<script src="auth.js"></script>
</body>
</html>
