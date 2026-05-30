<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";

$data = Api::jsonInput();

if (empty($data["email"]) || empty($data["password"])) {
    Response::json(["error" => "Thiếu thông tin đăng nhập"], 400);
}

$conn = Database::connect();

$stmt = $conn->prepare("
    SELECT id_nguoidung, ten, matkhau, ngaytao, vaitro, trangthai, avatar, premium_status, premium_expire
    FROM nguoidung
    WHERE email = ?
");

$stmt->bind_param("s", $data["email"]);
$stmt->execute();
$stmt->bind_result($id, $ten, $hashedPassword, $ngaytao, $vaitro, $trangthai, $avatar, $premium_status, $premium_expire);

if (!$stmt->fetch()) {
    Response::json(["error" => "Sai tài khoản hoặc mật khẩu"], 401);
}
$stmt->close();

if (!password_verify($data["password"], $hashedPassword)) {
    Response::json(["error" => "Sai tài khoản hoặc mật khẩu"], 401);
}

if ($trangthai !== "active") {
    Response::json(["error" => "Tài khoản đã bị khóa"], 403);
}

// Check expiration
if ($premium_status == 1 && !empty($premium_expire)) {
    $expireDate = new DateTime($premium_expire);
    if ($expireDate < new DateTime()) {
        $premium_status = 0;
        $premium_expire = null;
        $update = $conn->prepare("UPDATE nguoidung SET premium_status = 0, premium_expire = NULL WHERE id_nguoidung = ?");
        $update->bind_param("i", $id);
        $update->execute();
    }
}

// Get daily attempts
$stmt = $conn->prepare("SELECT COUNT(*) FROM lanthi WHERE id_nguoidung = ? AND DATE(thoigianbatdau) = CURDATE()");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($attemptsToday);
$stmt->fetch();
$stmt->close();

require_once __DIR__ . "/../core/TokenManager.php";

$_SESSION["user"] = [
    "id" => $id,
    "name" => $ten,
    "role" => $vaitro,
    "avatar" => $avatar ?? 'default.jpg',
    "premium_status" => $premium_status,
    "premium_expire" => $premium_expire,
    "attempts_today" => $attemptsToday
];

// Generate API Token for Desktop App
$token = TokenManager::generateToken([
    "id" => $id,
    "email" => $data["email"],
    "role" => $vaitro
]);

Response::json([
    "success" => true,
    "message" => "Đăng nhập thành công",
    "token" => $token,
    "id" => $id,
    "id_nguoidung" => $id,
    "ten" => $ten,
    "email" => $data["email"],
    "ngaytao" => $ngaytao,
    "role" => $vaitro,
    "avatar" => $avatar ?? 'default.jpg',
    "premium_status" => $premium_status,
    "premium_expire" => $premium_expire,
    "attempts_today" => $attemptsToday
]);
