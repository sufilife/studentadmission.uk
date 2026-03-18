<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/PHPMailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

$host = 'studentadmission.uk.mysql';
$dbname = 'studentadmission_ukstudentportaldb';
$username = 'studentadmission_ukstudentportaldb';
$password = 'STUDENT ADMISSION_@123';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    echo json_encode(['status'=>'db_error']);
    exit;
}

function sendVerificationMail($email, $name, $token){
    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host       = 'send.one.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@studentadmission.uk';
        $mail->Password   = '*shN$QxP-2d8)?r)';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('noreply@studentadmission.uk','Student Admission London');
        $mail->addAddress($email,$name);

        $verifyLink = "https://studentadmission.uk/verify-email.php?token=".$token;

        $mail->isHTML(true);
        $mail->Subject = 'Verify your email | Student Admission London';
        $mail->Body = "
            <p>Hi <b>$name</b>,</p>
            <p>Please verify your email to activate your student portal.</p>
            <p><a href='$verifyLink'>Verify Email</a></p>
            <p>If you did not register, ignore this email.</p>
        ";

        $mail->send();
        return true;
    }catch(Exception $e){
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

/* ---------- GOOGLE LOGIN ---------- */
if ($method === 'POST' && isset($_GET['google'])) {

    $data = json_decode(file_get_contents("php://input"), true);
    $email = strtolower(trim($data['email'] ?? ''));
    $name  = trim($data['name'] ?? '');
    $gid   = trim($data['google_id'] ?? '');

    if(!$email || !$gid){
        echo json_encode(['status'=>'error']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        if(!$user['google_id']){
            $pdo->prepare("UPDATE students SET google_id=?, email_verified=1, status='Approved' WHERE email=?")
                ->execute([$gid,$email]);
        }
        echo json_encode(['status'=>'login','email'=>$email]);
        exit;
    }

    $pdo->prepare("
        INSERT INTO students (name,email,google_id,email_verified,status)
        VALUES (?,?,?,?, 'Approved')
    ")->execute([$name,$email,$gid,1]);

    echo json_encode(['status'=>'registered','email'=>$email]);
    exit;
}

/* ---------- NORMAL REGISTRATION ---------- */
if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);
    $name = trim($data['name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $whatsapp = trim($data['whatsapp'] ?? '');

    if(!$name || !$email || !$whatsapp){
        echo json_encode(['status'=>'error']);
        exit;
    }

    $check = $pdo->prepare("SELECT id FROM students WHERE email=?");
    $check->execute([$email]);
    if($check->fetch()){
        echo json_encode(['status'=>'exists']);
        exit;
    }

    $token = bin2hex(random_bytes(32));

    $pdo->prepare("
        INSERT INTO students (name,email,whatsapp,verify_token)
        VALUES (?,?,?,?)
    ")->execute([$name,$email,$whatsapp,$token]);

    sendVerificationMail($email,$name,$token);

    echo json_encode(['status'=>'verify_sent']);
    exit;
}

/* ---------- EMAIL VERIFY ---------- */
if ($method === 'GET' && isset($_GET['verify'])) {

    $token = $_GET['verify'];

    $stmt = $pdo->prepare("
        UPDATE students 
        SET email_verified=1, status='Approved', verify_token=NULL 
        WHERE verify_token=?
    ");
    $stmt->execute([$token]);

    header("Location: /student-portal.html?verified=1");
    exit;
}

/* ---------- DASHBOARD ---------- */
if ($method === 'GET' && isset($_GET['email'])) {

    $stmt = $pdo->prepare("
        SELECT name,email,whatsapp,status,registered
        FROM students WHERE email=?
    ");
    $stmt->execute([strtolower($_GET['email'])]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo $row ? json_encode($row) : json_encode(['status'=>'not_found']);
    exit;
}

echo json_encode(['status'=>'invalid']);
