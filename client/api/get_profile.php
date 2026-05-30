<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


require_once __DIR__ . "/../core/TokenManager.php";

$user = null;
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (strpos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
    $user = TokenManager::validateToken($token);
} elseif (isset($_SESSION["user"])) {
    $user = $_SESSION["user"];
}

if (!$user) {
    Response::json(["success" => false, "error" => "Unauthorized"], 401);
}

$user_id = $user["id_nguoidung"] ?? $user["id"] ?? 0;
$conn = Database::connect();

// Refresh premium status and get attempts
$stmt = $conn->prepare("SELECT email, ten, ngaytao, avatar, premium_status, premium_expire FROM nguoidung WHERE id_nguoidung = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();

if (!$userData) {
    Response::json(["success" => false, "error" => "Không tìm thấy người dùng"], 404);
}

// Check expiration
if ($userData['premium_status'] == 1 && !empty($userData['premium_expire'])) {
    $expireDate = new DateTime($userData['premium_expire']);
    if ($expireDate < new DateTime()) {
        $userData['premium_status'] = 0;
        $userData['premium_expire'] = null;
        $update = $conn->prepare("UPDATE nguoidung SET premium_status = 0, premium_expire = NULL WHERE id_nguoidung = ?");
        $update->bind_param("i", $user_id);
        $update->execute();
    }
}

// Get daily attempts (Accept custom date from client to fix timezone issues)
$clientDate = $_GET['date'] ?? date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) as attempts_today FROM lanthi WHERE id_nguoidung = ? AND (DATE(thoigianbatdau) = ? OR DATE(thoigiannop) = ?)");
$stmt->bind_param("iss", $user_id, $clientDate, $clientDate);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$userData['attempts_today'] = $row ? (int)$row['attempts_today'] : 0;

Response::json(["success" => true, "data" => $userData]);