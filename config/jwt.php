<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($payload) {
    $secret = $_ENV['JWT_SECRET'];
    return JWT::encode($payload, $secret, 'HS256');
}

function verifyJWT($token) {
    $secret = $_ENV['JWT_SECRET'];
    return JWT::decode($token, new Key($secret, 'HS256'));
}
