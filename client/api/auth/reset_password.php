<?php

require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../core/Response.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data["email"] ?? "";
$otp = $data["otp"] ?? "";
$newPassword = $data["password"] ?? "";

if (empty($email) || empty($otp) || empty($newPassword)) {
    Response::json(["error" => "Thiếu thông tin đặt lại mật khẩu"], 400);
}

$conn = Database::connect();

// 1. Xác thực lại mã OTP một lần nữa cho chắc chắn
$stmt = $conn->prepare("SELECT id FROM password_resets WHERE email = ? AND otp = ? AND expires_at > NOW()");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    Response::json(["error" => "Yêu cầu không hợp lệ hoặc đã hết hạn"], 401);
}

// 2. Cập nhật mật khẩu mới (Dùng password_hash đồng bộ với hệ thống)
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE nguoidung SET matkhau = ? WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    // 3. Xóa mã OTP sau khi dùng xong
    $conn->query("DELETE FROM password_resets WHERE email = '$email'");
    Response::json(["success" => true, "message" => "Đổi mật khẩu thành công. Vui lòng đăng nhập lại."]);
} else {
    Response::json(["error" => "Lỗi cập nhật mật khẩu"], 500);
}
