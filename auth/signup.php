<?php
require "../config/db.php";
require "../mail/send_verification.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Basic validation
    if ($pass !== $confirm) {
        echo "Passwords do not match";
        exit;
    }

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email already exists";
        exit;
    }

    // Generate verification token
    $token = bin2hex(random_bytes(32));

    // Try sending the email first
    if (sendVerification($email, $token)) {

        // Encrypt password
        $hashed = password_hash($pass, PASSWORD_BCRYPT);

        // Insert user into DB
        $stmt = $conn->prepare("INSERT INTO users 
            (full_name,email,password,role,status,created_at,email_verified,verification_token)
            VALUES (?,?,?,?,NOW(),0,?)");

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
