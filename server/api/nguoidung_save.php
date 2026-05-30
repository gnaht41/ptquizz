<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/admin/nguoidung.model.php";

Api::requireRole(["admin"]);

$data = Api::jsonInput();

$id_nguoidung = (int) ($data["id_nguoidung"] ?? 0);
$ten = trim((string) ($data["ten"] ?? ""));
$vaitro = normalizeManagedRole($data["vaitro"] ?? "");
$trangthai = normalizeUserStatus($data["trangthai"] ?? "active") ?? "active";
$matkhau = (string) ($data["matkhau"] ?? "");
$reset_pwd = (bool) ($data["reset_pwd"] ?? false);

if ($ten === "") {
    Api::json(["error" => "Tên người dùng không được để trống"], 400);
}

if ($vaitro === null) {
    Api::json(["error" => "Vai trò chỉ được là thí sinh hoặc giảng viên"], 400);
}

if ($id_nguoidung > 0) {
    // --- CẬP NHẬT NGƯỜI DÙNG ---
    $existing = getManagedUserById($id_nguoidung);
    if (!$existing) {
        Api::json(["error" => "Không tìm thấy người dùng"], 404);
    }

    // Admin không được sửa email, lấy lại email cũ từ DB
    $email = $existing["email"];

    // Admin chỉ được reset mật khẩu về User@123456 nếu checkbox được tích
    $finalMatKhau = $reset_pwd ? "User@123456" : "";

    $ok = updateManagedUser($id_nguoidung, $email, $ten, $vaitro, $trangthai, $finalMatKhau);
    if (!$ok) {
        Api::json(["error" => "Không thể cập nhật người dùng"], 500);
    }

    Api::json([
        "success" => true,
        "message" => "Cập nhật người dùng thành công",
        "data" => getManagedUserById($id_nguoidung),
    ]);
} else {
    // --- TẠO MỚI NGƯỜI DÙNG ---
    $email = trim((string) ($data["email"] ?? ""));
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Api::json(["error" => "Email không hợp lệ"], 400);
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]).{8,}$/', $matkhau)) {
        Api::json(["error" => "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt"], 400);
    }

    if (isUserEmailExists($email)) {
        Api::json(["error" => "Email này đã tồn tại"], 409);
    }

    $newId = createManagedUser($email, $matkhau, $ten, $vaitro, $trangthai);
    if ($newId <= 0) {
        Api::json(["error" => "Không thể tạo người dùng"], 500);
    }

    Api::json([
        "success" => true,
        "message" => "Thêm người dùng thành công",
        "data" => getManagedUserById($newId),
    ], 201);
}
