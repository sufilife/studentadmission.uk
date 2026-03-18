<?php
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("
    SELECT r.rating, r.comment, r.created_at, u.name
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.status = 'approved'
    ORDER BY r.created_at DESC
    LIMIT 20
");

echo json_encode([
    'success' => true,
    'reviews' => $stmt->fetchAll()
]);
