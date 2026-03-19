<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/PHPMailer/src/Exception.php';
require __DIR__.'/PHPMailer/src/PHPMailer.php';
require __DIR__.'/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $html)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'send.one.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@studentadmission.uk';
        $mail->Password   = 'N0r3ply@ studentadmission_arpor ki?';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('noreply@studentadmission.uk','Student Admission London');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;

        return true;
    } catch (Exception $e) {
        error_log($mail->ErrorInfo);
        return false;
    }
}
