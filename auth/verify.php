<?php
require "../config/db.php";

if (!isset($_GET['token'])) {
    header("Location: ../login.php?error=invalid");
    exit;
}

$token = urldecode(trim($_GET['token']));

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

  
    header("Location: verified.html");
    exit;

} else {
    
    header("Location: verified.html?status=invalid");
    exit;
}
?>
