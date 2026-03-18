<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['rating']) || empty($data['comment'])) {
    http_response_code(422);
    exit(json_encode(['error' => 'Rating & comment required']));
}

$stmt = $pdo->prepare("
    INSERT INTO reviews (user_id, rating, comment)
    VALUES (?, ?, ?)
");

$stmt->execute([
    $user->sub,
    (int)$data['rating'],
    trim($data['comment'])
]);

echo json_encode([
    'success' => true,
    'message' => 'Review submitted (pending approval)'
]);
