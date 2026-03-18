<?php
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
    die("Database connection failed");
}

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid verification link.");
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE verify_token=?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("This verification link is invalid or already used.");
}

/* ---- VERIFY + AUTO APPROVE ---- */
$update = $pdo->prepare("
    UPDATE students 
    SET email_verified=1,
        status='Approved',
        verify_token=NULL
    WHERE id=?
");
$update->execute([$user['id']]);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Verified | STUDENT ADMISSION LONDON</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5 text-center">
    <h2 class="text-success">✅ Email Verified Successfully</h2>
    <p class="mt-3">
        Thank you <strong><?= htmlspecialchars($user['name']) ?></strong>,<br>
        Your account has been <b>approved automatically</b>.
    </p>

    <a href="student-portal.html" class="btn btn-primary mt-4">
        Go to Student Portal
    </a>
</div>

</body>
</html>
