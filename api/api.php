<?php
header("Content-Type: application/json");
require "db.php";
require "mailer.php";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input['action'] ?? null;

if (!$action) {
    http_response_code(400);
    exit(json_encode(["error" => "Invalid request"]));
}

/* =========================
   REGISTER
========================= */
if ($action === "register") {

    $email = strtolower(trim($input['email']));
    $password = password_hash($input['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("SELECT id FROM students WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        exit(json_encode(["error" => "Email already exists"]));
    }

    $token = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare("
        INSERT INTO students (email, password, email_verify_token)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$email, $password, $token]);

    sendVerificationMail($email, $token);

    exit(json_encode([
        "success" => true,
        "message" => "Registration successful. Check email to verify."
    ]));
}

/* =========================
   GOOGLE LOGIN
========================= */
if ($action === "google_login") {

    $email = strtolower($input['email']);

    $stmt = $pdo->prepare("SELECT id, email_verified FROM students WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $stmt = $pdo->prepare("
            INSERT INTO students (email, email_verified)
            VALUES (?, 1)
        ");
        $stmt->execute([$email]);
    }

    exit(json_encode([
        "success" => true,
        "message" => "Google login success"
    ]));
}

http_response_code(400);
echo json_encode(["error" => "Unknown action"]);
