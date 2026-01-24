<?php
require "../config/db.php";

if (!isset($_GET['token'])) {
    die("No token provided.");
}

$token = trim($_GET['token']);
$token = urldecode($token);   // ✅ FIX URL ENCODING

// Debug (TEMP):
// echo "TOKEN FROM URL: [$token]";

$stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {

    $update = $conn->prepare("UPDATE users 
        SET email_verified = 1, verification_token = NULL 
        WHERE verification_token = ?");
    $update->bind_param("s", $token);
    $update->execute();

    echo "✅ Email verified successfully. You can login now.";
} else {
    echo "❌ Invalid or expired token.";
}
?>
