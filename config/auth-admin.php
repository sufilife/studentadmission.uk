<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo 'Access denied';
    exit;
}
