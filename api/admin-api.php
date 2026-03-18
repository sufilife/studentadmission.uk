<?php
session_start();
require_once "../config/db.php";
if(!isset($_SESSION['admin'])) die("Unauthorized");

$data = json_decode(file_get_contents("php://input"),true);
$stmt = $pdo->prepare("UPDATE students SET status=? WHERE id=?");
$stmt->execute([$data['status'],$data['id']]);
echo json_encode(["success"=>true]);
