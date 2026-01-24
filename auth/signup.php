<?php
require "../config/db.php";
require "../mail/send_verification.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm'];


$pass  = $_POST['password'];
$confirm = $_POST['confirm'];

if (strlen($pass) < 8) {
    echo "Password must be at least 8 characters long";
    exit;
}

if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pass)) {
    echo "Password must include at least one special character";
    exit;
}

if ($pass !== $confirm) {
    echo "Passwords do not match";
    exit;
}



    if ($pass !== $confirm) {
        echo "Passwords do not match";
        exit;
    }

   
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email already exists";
        exit;
    }

   
    $token = bin2hex(random_bytes(32));

  
    if (sendVerification($email, $token)) {

       
        $hashed = password_hash($pass, PASSWORD_BCRYPT);

      
 $stmt = $conn->prepare("INSERT INTO users 
(full_name, email, password, role, status, created_at, email_verified, verification_token)
VALUES (?, ?, ?, ?, ?, NOW(), 0, ?)");

$stmt->bind_param("ssssss", $name, $email, $hashed, $role, $status, $token);


        $role = 'user';
        $status = 'active';

        $stmt->bind_param("ssssss", $name, $email, $hashed, $role, $status, $token);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Database error: ".$stmt->error;
        }

    } else {
        echo "Failed to send verification email. Please try again.";
    }
}
?>
