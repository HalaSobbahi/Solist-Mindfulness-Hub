<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendVerification($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'solist.hub@gmail.com';
        $mail->Password   = 'YOUR_APP_PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('solist.hub@gmail.com', 'Solist Mindfulness Hub');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your email';
        
        $link = "http://localhost/solist/auth/verify.php?token=$token";

        $mail->Body = "
        <h2>Email Verification</h2>
        <p>Click the link below to verify your account:</p>
        <a href='$link'>Verify Email</a>
        ";

        $mail->send();
        return true; // ✅ success
    } catch (Exception $e) {
        return false; // ❌ failed
    }
}

?>
