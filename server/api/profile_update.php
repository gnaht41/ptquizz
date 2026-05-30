<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";

$user = Api::requireRole(["admin", "giangvien"]);

$isMultipart = strpos($_SERVER["CONTENT_TYPE"] ?? "", "multipart/form-data") !== false;
$data = $isMultipart ? $_POST : Api::jsonInput();

$id_nguoidung = (int) ($user["id_nguoidung"] ?? 0);
$ten = trim((string) ($data["ten"] ?? ""));
$email = trim((string) ($data["email"] ?? ""));
$matkhau = (string) ($data["matkhau"] ?? "");

if ($ten === "") {
    Api::json(["error" => "Họ tên không được để trống"], 400);
}

if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Api::json(["error" => "Email không hợp lệ"], 400);
}

if ($matkhau !== "" && strlen($matkhau) < 6) {
    Api::json(["error" => "Mật khẩu mới phải có ít nhất 6 ký tự"], 400);
}

// Xử lý upload avatar
$avatarPath = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
    $fileSize = $_FILES['avatar']['size'];
    
    if (!in_array($fileType, $allowedTypes)) {
        Api::json(["error" => "Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF"], 400);
    }
    
    if ($fileSize > 2 * 1024 * 1024) { // 2MB limit
        Api::json(["error" => "Dung lượng ảnh tối đa là 2MB"], 400);
    }
    
    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $newFileName = 'avatar_' . $id_nguoidung . '_' . time() . '.' . $extension;
    $uploadDir = __DIR__ . '/../public/imgs/avatars/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFileName)) {
        $avatarPath = $newFileName;
    } else {
        Api::json(["error" => "Không thể tải lên ảnh đại diện"], 500);
    }
}

$conn = Database::connect();

$stmtCheck = $conn->prepare("SELECT id_nguoidung FROM nguoidung WHERE email = ? AND id_nguoidung != ? LIMIT 1");
$stmtCheck->bind_param("si", $email, $id_nguoidung);
$stmtCheck->execute();
$exists = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if ($exists) {
    if ($avatarPath && file_exists(__DIR__ . '/../public/imgs/avatars/' . $avatarPath)) {
        unlink(__DIR__ . '/../public/imgs/avatars/' . $avatarPath);
    }
    // $conn->close();
    Api::json(["error" => "Email này đã được sử dụng bởi tài khoản khác"], 409);
}

// Lấy avatar cũ để xóa sau khi update thành công
$stmtOld = $conn->prepare("SELECT avatar FROM nguoidung WHERE id_nguoidung = ?");
$stmtOld->bind_param("i", $id_nguoidung);
$stmtOld->execute();
$oldAvatar = $stmtOld->get_result()->fetch_assoc()['avatar'] ?? 'default.jpg';
$stmtOld->close();

$updateFields = ["ten = ?", "email = ?"];
$types = "ss";
$params = [$ten, $email];

if ($matkhau !== "") {
    $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
    $updateFields[] = "matkhau = ?";
    $types .= "s";
    $params[] = $hashedPassword;
}

if ($avatarPath !== null) {
    $updateFields[] = "avatar = ?";
    $types .= "s";
    $params[] = $avatarPath;
}

$types .= "i";
$params[] = $id_nguoidung;

$sql = "UPDATE nguoidung SET " . implode(", ", $updateFields) . " WHERE id_nguoidung = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

$ok = $stmt->execute();

// Nếu update thành công và có avatar mới, xóa avatar cũ (nếu không phải mặc định)
if ($ok && $avatarPath !== null && $oldAvatar !== 'default.jpg' && $oldAvatar !== $avatarPath) {
    $oldFilePath = __DIR__ . '/../public/imgs/avatars/' . $oldAvatar;
    if (file_exists($oldFilePath)) {
        unlink($oldFilePath);
    }
}

$stmt->close();
// $conn->close();

if (!$ok) {
    if ($avatarPath && file_exists(__DIR__ . '/../public/imgs/avatars/' . $avatarPath)) {
        unlink(__DIR__ . '/../public/imgs/avatars/' . $avatarPath);
    }
    Api::json(["error" => "Không thể cập nhật thông tin cá nhân"], 500);
}

$_SESSION["user"]["ten"] = $ten;
if ($avatarPath !== null) {
    $_SESSION["user"]["avatar"] = $avatarPath;
}

Api::json([
    "success" => true,
    "message" => "Cập nhật thông tin thành công",
    "avatar" => $avatarPath
]);
