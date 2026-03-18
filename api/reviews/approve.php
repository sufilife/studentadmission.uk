<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/db.php';

if ($user->role !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error' => 'Admin only']));
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id']) || empty($data['status'])) {
    http_response_code(422);
    exit(json_encode(['error' => 'Invalid request']));
}

if (!in_array($data['status'], ['approved','pending'])) {
    exit(json_encode(['error' => 'Invalid status']));
}

$stmt = $pdo->prepare(
    "UPDATE reviews SET status=? WHERE id=?"
);
$stmt->execute([$data['status'], (int)$data['id']]);

echo json_encode(['success'=>true]);
