<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$data = Api::jsonInput();

if (empty($data["fullname"]) || empty($data["email"]) || empty($data["password"])) {
    Response::json(["error" => "Thiếu thông tin đăng ký"], 400);
}

if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
    Response::json(["error" => "Email không hợp lệ"], 400);
}

// Password complexity validation (8+ chars, upper, lower, digit, special)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]).{8,}$/', $data["password"])) {
    Response::json(["error" => "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt"], 400);
}

$conn = Database::connect();

$stmt = $conn->prepare("SELECT id_nguoidung FROM nguoidung WHERE email = ?");
$stmt->bind_param("s", $data["email"]);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    Response::json(["error" => "Email đã tồn tại"], 400);
}

$passwordHash = password_hash($data["password"], PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO nguoidung(email, matkhau, ten, vaitro, trangthai, ngaytao, avatar)
    VALUES (?, ?, ?, 'thisinh', 'active', NOW(), 'default.jpg')
");

$stmt->bind_param("sss", $data["email"], $passwordHash, $data["fullname"]);

if (!$stmt->execute()) {
    Response::json(["error" => "Lỗi hệ thống"], 500);
}

Response::json([
    "success" => true,
    "message" => "Đăng ký thành công",
]);

