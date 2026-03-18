<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/jwt_helper.php';

$headers = getallheaders();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$token = str_replace('Bearer ', '', $headers['Authorization']);

try {
    $user = verify_jwt($token);
} catch (Exception $e) {
    http_response_code(401);
    exit(json_encode(['error' => 'Invalid token']));
}
