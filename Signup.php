<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/Login.css">
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="title">
            <img src="img/icon.png" alt="start icon" class="icon">
            <a href="index.php" style="text-decoration: none;">
                <h1 style="color:#9FB9CC;">Like the Sun that shines but doesn't burn</h1>
            </a>
            <img src="img/icon.png" alt="end icon" class="icon">

        </div>

    </div>

    <div class="login-container">
        <h2 style="color: #9FB9CC;">Sign Up</h2>
      <form id="signupForm">
    <input type="text" name="name" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
</form>

<p id="msg"></p>



        <p style="margin-top: 10px; font-size: 14px;">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
    </div>
</body>

</html>


<script>
document.getElementById("signupForm").addEventListener("submit", function(e){
    e.preventDefault();

    let form = new FormData(this);
    let msg = document.getElementById("msg");
    msg.innerHTML = "Processing, please wait...";

    fetch("auth/signup.php", {
        method: "POST",
        body: form
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === "success"){
            msg.innerHTML = "Check your email to verify your account";
        }else{
            msg.innerHTML = data;
        }
    });
});

</script>
