<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../core/Database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
$package_type = trim($_POST['package_type'] ?? '');

// Nếu không có POST data (trường hợp App gửi JSON), thử lấy từ php://input
if (empty($package_type) && empty($amount)) {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if ($jsonData) {
        $amount = (int)($jsonData['amount'] ?? 0);
        $package_type = trim($jsonData['package_type'] ?? '');
    }
}

$user_id = $_SESSION['user']['id'] ?? $_SESSION['user']['id_nguoidung'] ?? 0;

if ($user_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$conn = Database::connect();
$stmt = $conn->prepare("SELECT id_goi FROM goi_premium WHERE ten_goi = ? AND gia = ? AND trangthai = 'active' LIMIT 1");
$stmt->bind_param("sd", $package_type, $amount);
$stmt->execute();
$validPackage = $stmt->get_result()->fetch_assoc();

if (!$validPackage) {
    echo json_encode(['success' => false, 'message' => 'Gói premium không hợp lệ hoặc đã ngừng bán']);
    exit;
}

$order_code = 'DH' . time() . rand(100, 999);

$sql = "INSERT INTO payments
        (user_id, order_code, amount, package_type, status, expires_at, created_at)
        VALUES (?, ?, ?, ?, 'pending', DATE_ADD(NOW(), INTERVAL 15 MINUTE), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $user_id, $order_code, $amount, $package_type);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'order_code' => $order_code,
        'amount' => $amount,
        'qr_content' => "CHUYEN TIEN " . $order_code,
        'message' => 'Tạo đơn thành công'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn thanh toán. Vui lòng thử lại sau.']);
}
?>
