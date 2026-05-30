<?php
// server/api/sepay_webhook.php
header('Content-Type: application/json');
require_once __DIR__ . "/../core/Database.php";

// SePay Secret Key (Verify request)
$API_KEY = "whsec_djK1mRikLkUHci9as3xgsbqd34ETRlkU";

// Lấy dữ liệu từ SePay gửi sang
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data']);
    exit;
}

// Kiểm tra bảo mật HMAC-SHA256 (Khuyên dùng)
$headers = getallheaders();
$received_signature = $headers['X-Sepay-Signature'] ?? $headers['x-sepay-signature'] ?? '';

if (!empty($received_signature)) {
    $computed_signature = hash_hmac('sha256', $payload, $API_KEY);
    if (!hash_equals($computed_signature, $received_signature)) {
        echo json_encode(['success' => false, 'message' => 'Invalid signature']);
        exit;
    }
} else {
    // Nếu SePay cấu hình HMAC mà không gửi header thì cũng từ chối (trừ khi đang test)
    // echo json_encode(['success' => false, 'message' => 'Missing signature']);
    // exit;
}

$content = $data['content'] ?? ''; // Nội dung chuyển khoản
$amount = $data['amount'] ?? 0;
$transaction_id = $data['reference_number'] ?? $data['id'] ?? '';

// Tìm mã đơn từ nội dung chuyển khoản (Ví dụ: CHUYEN TIEN DH123456789)
preg_match('/DH\d+/', $content, $matches);
if (empty($matches)) {
    echo json_encode(['success' => false, 'message' => 'Invalid content']);
    exit;
}

$order_code = $matches[0];

$conn = Database::connect();

// Kiểm tra đơn hàng trong DB
$stmt = $conn->prepare("SELECT * FROM payments WHERE order_code = ? AND status = 'pending'");
$stmt->bind_param("s", $order_code);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found or already processed']);
    exit;
}

// Kiểm tra số tiền (Phải khớp)
if ((float)$amount < (float)$order['amount']) {
    echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
    exit;
}

// Bắt đầu cập nhật
$conn->begin_transaction();

try {
    // 1. Cập nhật trạng thái đơn hàng
    $stmt = $conn->prepare("UPDATE payments SET status = 'completed', transaction_id = ? WHERE id_thanhtoan = ?");
    $stmt->bind_param("si", $transaction_id, $order['id_thanhtoan']);
    $stmt->execute();

    // 2. Cập nhật premium cho user
    $user_id = $order['user_id'];
    $package_type = $order['package_type']; // đang lưu tên gói

    // Truy xuất số ngày thực tế của gói từ bảng goi_premium
    $stmt = $conn->prepare("SELECT thoihan_ngay FROM goi_premium WHERE ten_goi = ? AND trangthai = 'active'");
    $stmt->bind_param("s", $package_type);
    $stmt->execute();
    $goi_info = $stmt->get_result()->fetch_assoc();
    if (!$goi_info) {
        throw new Exception('Gói premium không còn hoạt động');
    }
    $days = $goi_info ? (int)$goi_info['thoihan_ngay'] : 30; // Mặc định 30 nếu lỗi

    // Tính ngày hết hạn: Nếu đang là premium thì cộng dồn, nếu không thì tính từ NOW
    $stmt = $conn->prepare("SELECT premium_expire, premium_status FROM nguoidung WHERE id_nguoidung = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    // Tính ngày hết hạn: 
    // - Nếu ĐANG LÀ PREMIUM (chưa hết hạn): Lấy ngày hết hạn cũ + số ngày của gói mới (Cộng dồn)
    // - Nếu ĐÃ HẾT HẠN hoặc CHƯA TỪNG MUA: Lấy ngày hiện tại + số ngày của gói mới
    $now = new DateTime();
    $expireDate = new DateTime();
    
    if ($user['premium_status'] == 1 && !empty($user['premium_expire'])) {
        $currentExpire = new DateTime($user['premium_expire']);
        if ($currentExpire > $now) {
            $expireDate = clone $currentExpire; // Giữ lại những ngày còn dư
        }
    }
    
    $expireDate->modify("+$days days");
    $expireStr = $expireDate->format('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE nguoidung SET premium_status = 1, premium_expire = ? WHERE id_nguoidung = ?");
    $stmt->bind_param("si", $expireStr, $user_id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Payment processed successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
