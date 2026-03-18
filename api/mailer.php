<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/PHPMailer/src/Exception.php";
require __DIR__ . "/PHPMailer/src/PHPMailer.php";
require __DIR__ . "/PHPMailer/src/SMTP.php";

function sendVerificationMail($to, $token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = "send.one.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "noreply@studentadmission.uk";
        $mail->Password   = "EMAIL_PASSWORD";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom("noreply@studentadmission.uk", "Student Admission London");
        $mail->addAddress($to);

        $verifyLink = "https://studentadmission.uk/api/verify-email.php?token=" . $token;

        $mail->isHTML(true);
        $mail->Subject = "Verify your email";
        $mail->Body = "
            <h3>Email Verification</h3>
            <p>Please verify your email by clicking below:</p>
            <a href='$verifyLink'>$verifyLink</a>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
