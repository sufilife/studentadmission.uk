<?php
require "db.php";

$token = $_GET['token'] ?? null;
if (!$token) exit("Invalid token");

$stmt = $pdo->prepare("
    UPDATE students
    SET email_verified=1, email_verified_at=NOW(), email_verify_token=NULL
    WHERE email_verify_token=?
");
$stmt->execute([$token]);

if ($stmt->rowCount()) {
    echo "✅ Email verified successfully";
} else {
    echo "❌ Invalid or expired token";
}
