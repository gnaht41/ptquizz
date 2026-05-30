<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/monhoc.model.php";

// Kiểm tra quyền đăng nhập
$user = Api::requireLogin();
$data = Api::jsonInput();

$id_monhoc = (int) ($data["id_monhoc"] ?? 0);

if ($id_monhoc <= 0) {
    Api::json(["error" => "ID môn học không hợp lệ"], 400);
}

$existing = getOne_monhoc($id_monhoc);
if (!$existing) {
    Api::json(["error" => "Không tìm thấy môn học"], 404);
}

// Kiểm tra quyền sở hữu (trừ admin)
if (($user["vaitro"] ?? "") !== "admin" && (int) $existing["id_nguoidung"] !== (int) ($user["id_nguoidung"] ?? 0)) {
    Api::json(["error" => "Bạn không có quyền xóa môn học này"], 403);
}

// Kiểm tra xem môn học có bài thi nào không
$examCount = count_baithi_by_monhoc($id_monhoc);
if ($examCount > 0) {
    Api::json(["error" => "Môn học đang có bài thi liên quan, vui lòng xóa bài thi trước"], 409);
}

$ok = delete_monhoc($id_monhoc, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");

if (!$ok) {
    Api::json(["error" => "Không thể xóa môn học"], 500);
}

Api::json([
    "success" => true,
    "message" => "Xóa môn học thành công",
]);
