<?php

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerification($email, $token) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'solist.hub@gmail.com';
        $mail->Password   = 'lmawhwpeeswadkyr';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('solist.hub@gmail.com', 'Solist Mindfulness Hub');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your email';
        
        $link = "http://localhost/solist/auth/verify.php?token=$token";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background-color: #363535; color: #fff; padding: 30px; text-align: center;'>
            
            <div style='background-color: #9FB9CC; padding: 40px; border-radius: 10px; color: #363535; display: inline-block;'>
                <h2 style='margin-bottom: 20px;'>Email Verification</h2>
                <p style='font-size: 16px; margin-bottom: 30px;'>Click the button below to verify your account:</p>
                <a href='$link' style='display: inline-block; padding: 12px 25px; background-color: #363535; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verify Email</a>
            </div>

            <p style='margin-top: 30px; font-size: 12px; color: #ccc;'>If you did not request this email, please ignore it.</p>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

