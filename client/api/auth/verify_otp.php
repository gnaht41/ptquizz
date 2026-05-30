<?php

require_once __DIR__ . "/../../core/Api.php";
require_once __DIR__ . "/../../core/Database.php";
require_once __DIR__ . "/../../core/Response.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data["email"] ?? "";
$otp = $data["otp"] ?? "";

if (empty($email) || empty($otp)) {
    Response::json(["error" => "Thiếu thông tin xác thực"], 400);
}

$conn = Database::connect();

// Kiểm tra mã OTP
$stmt = $conn->prepare("SELECT id FROM password_resets WHERE email = ? AND otp = ? AND expires_at > NOW()");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    Response::json(["success" => true, "message" => "Xác thực mã OTP thành công"]);
} else {
    Response::json(["error" => "Mã OTP không chính xác hoặc đã hết hạn"], 401);
}
