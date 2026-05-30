<?php

require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../core/Response.php";
require_once __DIR__ . "/../../core/MailHelper.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data["email"] ?? "";

if (empty($email)) {
    Response::json(["error" => "Vui lòng nhập địa chỉ email"], 400);
}

$conn = Database::connect();

// 1. Kiểm tra email có tồn tại không
$stmt = $conn->prepare("SELECT id_nguoidung FROM nguoidung WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    Response::json(["error" => "Email này không tồn tại trong hệ thống"], 404);
}

// 2. Tạo mã OTP (6 số ngẫu nhiên)
$otp = str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);

// 3. Lưu OTP vào bảng password_resets (Xóa các OTP cũ của email này trước)
$conn->query("DELETE FROM password_resets WHERE email = '$email'");
$stmt = $conn->prepare("INSERT INTO password_resets (email, otp, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();

// 4. Gửi Email
$subject = "Mã xác thực khôi phục mật khẩu - PT QUIZ";
$body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e0e0e0; padding: 20px; border-radius: 10px;'>
        <h2 style='color: #0d6efd; text-align: center;'>PT QUIZ - Khôi phục mật khẩu</h2>
        <p>Chào bạn,</p>
        <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản liên kết với email này. Vui lòng sử dụng mã xác thực dưới đây:</p>
        <div style='background: #f8f9fa; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #333; border-radius: 5px; border: 1px dashed #0d6efd;'>
            $otp
        </div>
        <p style='color: #666; font-size: 14px; margin-top: 20px;'>Mã này có hiệu lực trong vòng <b>10 phút</b>. Nếu bạn không yêu cầu thay đổi này, vui lòng bỏ qua email này.</p>
        <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
        <p style='text-align: center; color: #999; font-size: 12px;'>© 2026 PT QUIZ System. All rights reserved.</p>
    </div>
";

$sendResult = MailHelper::send($email, $subject, $body);

if ($sendResult === true) {
    Response::json(["success" => true, "message" => "Mã OTP đã được gửi về email của bạn"]);
} else {
    // Nếu gửi lỗi, trả về thông báo lỗi chi tiết (hoặc ẩn đi nếu muốn bảo mật)
    Response::json(["error" => "Không thể gửi email. Lỗi: " . $sendResult], 500);
}
