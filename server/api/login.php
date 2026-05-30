<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Jwt.php";
require_once __DIR__ . "/../core/SecurityLogger.php";
require_once __DIR__ . "/../model/Database.php";

Api::boot();
Api::requireMethod("POST");

$data = Api::jsonInput();
$email = trim((string)($data["email"] ?? ""));
$password = (string)($data["password"] ?? "");

$ip = SecurityLogger::getIpAddress();

// 1. Kiểm tra Brute Force
if (SecurityLogger::checkBruteForce($ip)) {
    SecurityLogger::logRequest(null, 429); // 429 Too Many Requests
    Api::json(["error" => "Bạn đã thử đăng nhập sai quá nhiều lần. Vui lòng quay lại sau 15 phút."], 429);
}

if ($email === "" || $password === "") {
    Api::json(["error" => "Vui lòng nhập đầy đủ email và mật khẩu"], 400);
}

$conn = Database::connect();
$stmt = $conn->prepare("SELECT id_nguoidung, ten, vaitro, matkhau, trangthai, avatar FROM nguoidung WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
// $conn->close();

if ($user && password_verify($password, $user['matkhau'])) {
    if ($user['trangthai'] !== 'active') {
        SecurityLogger::logRequest($user['id_nguoidung'], 403);
        Api::json(["error" => "Tài khoản của bạn đã bị khóa"], 403);
    }

    // Login thành công -> Tạo JWT
    $payload = [
        "id_nguoidung" => $user['id_nguoidung'],
        "ten" => $user['ten'],
        "vaitro" => $user['vaitro'],
        "iat" => time(),
        "exp" => time() + (3600 * 24) // Hết hạn sau 24h
    ];

    $token = Jwt::encode($payload);

    SecurityLogger::logRequest($user['id_nguoidung'], 200);

    $_SESSION["user"] = [
        "id_nguoidung" => $user['id_nguoidung'],
        "ten" => $user['ten'],
        "vaitro" => $user['vaitro'],
        "avatar" => !empty($user['avatar']) ? $user['avatar'] : 'default.jpg'
    ];

    Api::json([
        "success" => true,
        "message" => "Đăng nhập thành công",
        "token" => $token,
        "user" => [
            "id" => $user['id_nguoidung'],
            "ten" => $user['ten'],
            "vaitro" => $user['vaitro'],
            "avatar" => !empty($user['avatar']) ? $user['avatar'] : 'default.jpg'
        ]
    ]);
} else {
    // Login thất bại -> Log lại để tính Brute Force
    SecurityLogger::logRequest(null, 401);
    Api::json(["error" => "Email hoặc mật khẩu không chính xác"], 401);
}
