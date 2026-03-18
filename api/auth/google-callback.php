<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../helpers/jwt_helper.php';
require_once __DIR__ . '/../../config/db.php';

use Google\Client;

$google = require __DIR__ . '/../../config/google.php';

if (!isset($_GET['code'])) {
    exit('Invalid Google response');
}

$client = new Client();
$client->setClientId($google['client_id']);
$client->setClientSecret($google['client_secret']);
$client->setRedirectUri($google['redirect_uri']);

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    exit('Google auth failed');
}

$client->setAccessToken($token['access_token']);
$googleUser = $client->verifyIdToken();

$email = $googleUser['email'];
$name  = $googleUser['name'];

// Auto-approved user
$stmt = $pdo->prepare("
    INSERT INTO users (name, email, role)
    VALUES (?, ?, 'student')
    ON DUPLICATE KEY UPDATE name = VALUES(name)
");
$stmt->execute([$name, $email]);

$userId = $pdo->lastInsertId() ?: $pdo->query(
    "SELECT id FROM users WHERE email='{$email}'"
)->fetchColumn();

$jwt = generate_jwt([
    'id' => $userId,
    'email' => $email,
    'role' => 'student'
]);

header("Location: /public/index.php?token={$jwt}");
exit;
