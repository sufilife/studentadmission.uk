<?php
require_once __DIR__ . '/../config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$rating  = (int)($data['rating'] ?? 0);
$comment = trim($data['comment'] ?? '');

if ($rating < 1 || $rating > 5 || $comment === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

/* Prevent duplicate review */
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'You already submitted a review'
    ]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO reviews (user_id, rating, comment, status, created_at)
    VALUES (?, ?, ?, 'approved', NOW())
");

$stmt->execute([
    $_SESSION['user_id'],
    $rating,
    $comment
]);

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your review is live.'
]);
