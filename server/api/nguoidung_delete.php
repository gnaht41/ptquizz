<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/admin/nguoidung.model.php";

Api::requireRole(["admin"]);

$data = Api::jsonInput();
$id_nguoidung = (int) ($data["id_nguoidung"] ?? 0);

if ($id_nguoidung <= 0) {
    Api::json(["error" => "Thiếu ID người dùng"], 400);
}

$existing = getManagedUserById($id_nguoidung);
if (!$existing) {
    Api::json(["error" => "Không tìm thấy người dùng"], 404);
}

if (($existing["trangthai"] ?? "") === "inactive") {
    Api::json([
        "success" => true,
        "message" => "Tài khoản này đã bị khóa trước đó",
    ]);
}

$ok = softDeleteManagedUser($id_nguoidung);
if (!$ok) {
    Api::json(["error" => "Không thể khóa tài khoản"], 500);
}

Api::json([
    "success" => true,
    "message" => "Đã khóa mềm tài khoản thành công",
]);
