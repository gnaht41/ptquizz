<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";
require_once __DIR__ . "/../model/giangvien/baithi.model.php";

$user = Api::requireLogin();
$data = Api::jsonInput();

$id_baithi = (int) ($data["id_baithi"] ?? 0);
$id_nguoidung = (int) ($user["id_nguoidung"] ?? 0);
$role = $user["vaitro"] ?? "";

if ($id_baithi <= 0) {
    Api::json(["error" => "Thiếu ID bài thi"], 400);
}

$conn = Database::connect();
// Kiểm tra quyền: Chỉ chủ sở hữu môn học của bài thi này hoặc admin mới được xóa
$sql = "SELECT bt.id_baithi
    FROM baithi bt
    JOIN monhoc mh ON bt.id_monhoc = mh.id_monhoc
    WHERE bt.id_baithi = ? AND (mh.id_nguoidung = ? OR ? = 'admin')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $id_baithi, $id_nguoidung, $role);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    Api::json(["error" => "Bạn không có quyền xóa bài thi này"], 403);
}

$ok = delete_baithi($id_baithi);

if (!$ok) {
    Api::json(["error" => "Không thể xóa bài thi"], 500);
}

Api::json([
    "success" => true,
    "message" => "Xóa bài thi thành công",
]);
