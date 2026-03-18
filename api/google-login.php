<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Google\Client;

$data = json_decode(file_get_contents('php://input'), true);
$credential = $data['credential'] ?? null;

if (!$credential) {
    echo json_encode(['success' => false]);
    exit;
}

$client = new Client([
    'client_id' => 'YOUR_GOOGLE_CLIENT_ID'
]);

$payload = $client->verifyIdToken($credential);

if (!$payload) {
    echo json_encode(['success' => false]);
    exit;
}

$email = $payload['email'];
$name  = $payload['name'] ?? 'User';

/* Check user */
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, role, created_at)
        VALUES (?, ?, 'user', NOW())
    ");
    $stmt->execute([$name, $email]);
    $user_id = $pdo->lastInsertId();
} else {
    $user_id = $user['id'];
}

session_start();
$_SESSION['user_id'] = $user_id;

echo json_encode([
    'success' => true,
    'user_id' => $user_id
]);
