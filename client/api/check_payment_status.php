<?php
// server/api/check_payment_status.php
header('Content-Type: application/json');
require_once __DIR__ . "/../core/Database.php";

$order_code = $_GET['order_code'] ?? '';

if (empty($order_code)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing order code']);
    exit;
}

$conn = Database::connect();

// 1. Kiểm tra trạng thái đơn hàng cụ thể và thời gian hết hạn
$stmt = $conn->prepare("SELECT status, user_id, 
    CASE WHEN expires_at < NOW() AND status = 'pending' THEN 1 ELSE 0 END as is_expired 
    FROM payments WHERE order_code = ?");
$stmt->bind_param("s", $order_code);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

if ($payment && $payment['is_expired'] == 1) {
    $payment['status'] = 'expired';
}

// 2. Kiểm tra trạng thái Premium tổng thể của user (đề phòng user đã thanh toán mã khác)
$is_premium = false;
$user_id = 0;

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (strpos($authHeader, 'Bearer ') === 0) {
    require_once __DIR__ . "/../core/TokenManager.php";
    $token = substr($authHeader, 7);
    $user = TokenManager::validateToken($token);
    if ($user) {
        $user_id = $user["id"] ?? $user["id_nguoidung"] ?? 0;
    }
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $user_id = $_SESSION['user']['id'] ?? $_SESSION['user']['id_nguoidung'] ?? 0;
}

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT premium_status FROM nguoidung WHERE id_nguoidung = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && $user['premium_status'] == 1) {
        $is_premium = true;
    }
}

echo json_encode([
    'status' => $payment['status'] ?? 'not_found',
    'is_premium' => $is_premium
]);
?>