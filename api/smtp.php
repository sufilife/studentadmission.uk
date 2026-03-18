<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../lib/PHPMailer/src/Exception.php';
require __DIR__.'/../lib/PHPMailer/src/PHPMailer.php';
require __DIR__.'/../lib/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $bodyHtml) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'send.one.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@studentadmission.uk'; // 🔴 must exist
        $mail->Password   = '*shN$QxP-2d8)?r';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('noreply@studentadmission.uk', 'Student Admission London');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
