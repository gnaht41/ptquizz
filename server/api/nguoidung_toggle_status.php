<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";

// This file is now handled by the router (server/api/index.php)
// Router handles: Api::boot(), Api::requireLogin(), Api::requireRole()

$data = Api::jsonInput();
$id = (int)($data['id_nguoidung'] ?? 0);

if (!$id) {
    Api::json(["success" => false, "message" => "ID người dùng không hợp lệ"], 400);
}

$conn = Database::connect();

// Get current status
$stmt = $conn->prepare("SELECT trangthai FROM nguoidung WHERE id_nguoidung = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    Api::json(["success" => false, "message" => "Không tìm thấy người dùng"], 404);
}

$newStatus = ($user['trangthai'] === 'active') ? 'inactive' : 'active';

// Update status
$updateStmt = $conn->prepare("UPDATE nguoidung SET trangthai = ? WHERE id_nguoidung = ?");
$updateStmt->bind_param("si", $newStatus, $id);

if ($updateStmt->execute()) {
    Api::json([
        "success" => true, 
        "message" => "Đã " . ($newStatus === 'active' ? 'mở khóa' : 'khóa') . " tài khoản thành công",
        "new_status" => $newStatus
    ]);
} else {
    Api::json(["success" => false, "message" => "Lỗi cập nhật database"], 500);
}

// $conn->close();
?>
