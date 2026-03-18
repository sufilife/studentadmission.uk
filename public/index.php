<?php
require_once __DIR__ . '/../bootstrap.php';

$token = $_GET['token'] ?? null;

if ($token) {
    echo "Login successful<br>";
    echo "JWT: " . htmlspecialchars($token);
} else {
    echo '<a href="/api/auth/google-login.php">Login with Google</a>';
}
