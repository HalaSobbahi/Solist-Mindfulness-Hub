<?php
require "../config/db.php";  // Database connection
require "../mail/send_verification.php";  // Email verification sender

// Check if form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean form inputs
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Password length validation

    if (strlen($pass) < 8) {
        echo "Password must be at least 8 characters long";
        exit;
    }

    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pass)) {
        echo "Password must include at least one special character";
        exit;
    }
    // Password match check

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

    // Generate email verification token

    $token = bin2hex(random_bytes(32));

    // Send verification email

    if (sendVerification($email, $token)) {

        // Hash password for security

        $hashed = password_hash($pass, PASSWORD_BCRYPT);

        // Insert user into database

        $stmt = $conn->prepare("INSERT INTO users 
(full_name, email, password, role, status, created_at, email_verified, verification_token)
VALUES (?, ?, ?, ?, ?, NOW(), 0, ?)");

        $stmt->bind_param("ssssss", $name, $email, $hashed, $role, $status, $token);


        $role = 'user';
        $status = 'active';


        if ($stmt->execute()) {
            echo "success"; // Registration successful
        } else {
            echo "Database error: " . $stmt->error;
        }
    } else {
        echo "Failed to send verification email. Please try again.";
    }
}
