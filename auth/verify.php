<?php
require "../config/db.php";
require "../mail/send_verification.php";

// ----------------------
// 1️⃣ SIGNUP FORM HANDLER
// ----------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $pass    = $_POST['password'];
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

    // Try sending the verification email
    if (sendVerification($email, $token)) {

        // Encrypt password
        $hashed = password_hash($pass, PASSWORD_BCRYPT);

        // Insert user into DB
        $stmt = $conn->prepare("INSERT INTO users 
            (full_name,email,password,role,status,created_at,email_verified,verification_token)
            VALUES (?,?,?,?,?,NOW(),?,?)");

        $role = 'user';
        $status = 'active';
        $email_verified = 0;

        $stmt->bind_param("sssssss", $name, $email, $hashed, $role, $status, $email_verified, $token);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Database error: " . $stmt->error;
        }

    } else {
        echo "Failed to send verification email. Please try again.";
    }

    exit;
}

// ----------------------
// 2️⃣ EMAIL VERIFICATION
// ----------------------
if (isset($_GET['token'])) {

    $token = $_GET['token'];

    // Check if token exists in DB
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        // Update user: mark email verified
        $update = $conn->prepare("UPDATE users SET email_verified=1, verification_token=NULL WHERE verification_token=?");
        $update->bind_param("s", $token);
        $update->execute();

        echo "Email verified successfully. You can login now.";
    } else {
        echo "Invalid or expired token.";
    }

    exit;
}

// Optional: if page accessed directly without POST or token
echo "Invalid request.";
?>
