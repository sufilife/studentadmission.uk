<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/lib/PHPMailer/src/Exception.php';
require_once __DIR__.'/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/lib/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'send.one.com';
    $mail->SMTPAuth = true;

    // 🔑 EXACT one.com login email
    $mail->Username = 'noreply@studentadmission.uk';
    $mail->Password = 'N0r3ply@ studentadmission_arpor ki?';

    // 🔁 CHANGE HERE (IMPORTANT)
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->setFrom('noreply@studentadmission.uk', 'Student Admission London');
    $mail->addAddress('admin@studentadmission.uk');

    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test one.com';
    $mail->Body = '<b>SMTP authenticated successfully</b>';

    $mail->send();
    echo "✅ SMTP AUTH OK";
} catch (Exception $e) {
    echo "<pre>❌ ERROR:\n".$mail->ErrorInfo."</pre>";
}
