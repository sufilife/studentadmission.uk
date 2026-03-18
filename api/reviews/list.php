<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/db.php';

if ($user->role !== 'admin') {
    http_response_code(403);
    exit(json_encode(['error' => 'Admin only']));
}

$reviews = $pdo->query("
    SELECT r.id, r.rating, r.comment, r.status, r.created_at,
           u.name, u.email
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reviews);
