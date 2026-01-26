<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Your session checking code below



// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Sat, 26 Jan 1990 00:00:00 GMT");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Destroy session completely in case it's partially set
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    header("Location: login.php");
    exit;
}

// Store user ID safely
$user_id = (int) $_SESSION['user_id'];
?>
