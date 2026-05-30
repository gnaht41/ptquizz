<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";

$user = Api::requireRole(["admin", "giangvien", "thisinh"]);
$id_nguoidung = (int) ($user["id_nguoidung"] ?? 0);

$conn = Database::connect();
$stmt = $conn->prepare("SELECT id_nguoidung, email, ten, vaitro, trangthai, ngaytao, avatar FROM nguoidung WHERE id_nguoidung = ? LIMIT 1");
$stmt->bind_param("i", $id_nguoidung);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();
// $conn->close();

if (!$profile) {
    Api::json(["error" => "Không tìm thấy thông tin người dùng"], 404);
}

Api::json([
    "success" => true,
    "data" => $profile,
]);
