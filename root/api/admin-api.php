<?php
require_once "../../config/db.php";
require_once "../../config/jwt.php";
require_once "../../config/mailer.php";
require_once "../../templates/email-approved.php";

header("Content-Type: application/json");

if (!isset($_COOKIE['admin_token'])) {
    echo json_encode(['status'=>'unauthorized']);
    exit;
}

try {
    $decoded = verifyJWT($_COOKIE['admin_token']);
    if ($decoded->role !== 'admin') throw new Exception();
} catch(Exception $e){
    echo json_encode(['status'=>'unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);
$status = $data['status'] ?? '';

if (!$id || !in_array($status,['Approved','Rejected'])) {
    echo json_encode(['status'=>'error']);
    exit;
}

$stmt = $pdo->prepare("
  SELECT name,email,status FROM students WHERE id=?
");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode(['status'=>'not_found']);
    exit;
}

$pdo->prepare("UPDATE students SET status=? WHERE id=?")
    ->execute([$status,$id]);

/* ---------- AUTO EMAIL ---------- */
if ($status === 'Approved' && $student['status'] !== 'Approved') {
    sendMail(
        $student['email'],
        "Your Registration is Approved 🎉",
        approvedEmailTemplate($student['name'])
    );
}

echo json_encode(['status'=>'ok']);
