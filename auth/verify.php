<?php
require "../config/db.php";

// Check if verification token exists in URL
if (!isset($_GET['token'])) {
    // Redirect to login if token is missing

    header("Location: ../login.php?error=invalid");
    exit;
}

$token = urldecode(trim($_GET['token']));
// Check if token exists in database

$stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

// If valid token found

if ($res->num_rows === 1) {
    // Update user as verified and remove token

    $update = $conn->prepare("UPDATE users 
        SET email_verified = 1, verification_token = NULL 
        WHERE verification_token = ?");
    $update->bind_param("s", $token);
    $update->execute();

    // Redirect to success page

    header("Location: verified.html");
    exit;
} else {

    header("Location: verified.html?status=invalid");
    exit;
}
