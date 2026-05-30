<?php
require_once __DIR__ . "/../../core/Database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_token = $_POST['credential'] ?? '';

if (empty($id_token)) {
    die("Token không hợp lệ");
}

// Xác thực token qua Google API
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
$response = file_get_contents($url);
$payload = json_decode($response, true);

if (!$payload || isset($payload['error'])) {
    die("Xác thực Google thất bại: " . ($payload['error_description'] ?? 'Lỗi không xác định'));
}

// Kiểm tra Client ID để đảm bảo an toàn
$google_client_id = "406738188655-6tbqad65pusvs16vf2ep4gli1jae2agt.apps.googleusercontent.com";
if ($payload['aud'] !== $google_client_id) {
    die("Client ID không khớp");
}

$email = $payload['email'];
$name = $payload['name'];
$google_id = $payload['sub'];

$conn = Database::connect();

// Kiểm tra người dùng đã tồn tại chưa
$stmt = $conn->prepare("SELECT id_nguoidung, ten, vaitro, trangthai, avatar FROM nguoidung WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $existingName, $role, $status, $avatar);

if ($stmt->fetch()) {
    // Người dùng đã tồn tại
    if ($status !== 'active') {
        die("Tài khoản của bạn đã bị khóa");
    }
    
    $_SESSION["user"] = [
        "id"     => $id,
        "name"   => $existingName,
        "role"   => $role,
        "avatar" => $avatar ?? 'default.jpg',
    ];
} else {
    // Tạo người dùng mới (vai trò thí sinh)
    $stmt->close();
    
    $vaitro    = 'thisinh';
    $trangthai = 'active';
    $matkhau   = password_hash("User@123456", PASSWORD_DEFAULT); // Mật khẩu mặc định an toàn cho Google login
    
    $stmt = $conn->prepare("INSERT INTO nguoidung (email, ten, matkhau, vaitro, trangthai, avatar, ngaytao) VALUES (?, ?, ?, ?, ?, 'default.jpg', NOW())");
    $stmt->bind_param("sssss", $email, $name, $matkhau, $vaitro, $trangthai);
    
    if ($stmt->execute()) {
        $_SESSION["user"] = [
            "id"     => $conn->insert_id,
            "name"   => $name,
            "role"   => $vaitro,
            "avatar" => 'default.jpg',
        ];
    } else {
        die("Không thể tạo tài khoản mới");
    }
}

$stmt->close();
// $conn->close();

// Chuyển hướng về trang chủ
header("Location: ../../index.php");
exit;
