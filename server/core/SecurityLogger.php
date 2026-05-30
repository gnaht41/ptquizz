<?php
require_once __DIR__ . '/../model/Database.php';

class SecurityLogger
{
    /**
     * Log an API request to the database
     */
    public static function logRequest($id_nguoidung = null, $response_code = 200)
    {
        $conn = Database::connect();
        
        $full_uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $ip_address = self::getIpAddress();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Rút gọn endpoint để dễ nhìn
        $endpoint = parse_url($full_uri, PHP_URL_PATH);
        $query = parse_url($full_uri, PHP_URL_QUERY);
        if ($query) {
            $endpoint .= '?' . $query;
        }

        // Tự động xác định tên hành động
        $action_name = self::mapActionName($full_uri, $method);
        
        // Thu thập tham số
        $params = array_merge($_GET, $_POST);
        if (isset($params['matkhau'])) $params['matkhau'] = '********';
        if (isset($params['password'])) $params['password'] = '********';
        $request_params = json_encode($params, JSON_UNESCAPED_UNICODE);
        
        $stmt = $conn->prepare("INSERT INTO api_logs (id_nguoidung, endpoint, method, action_name, ip_address, request_params, response_code, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssis", $id_nguoidung, $endpoint, $method, $action_name, $ip_address, $request_params, $response_code, $user_agent);
        $stmt->execute();
    }

    /**
     * Dịch endpoint sang tên hành động tiếng Việt và chi tiết hóa
     */
    private static function mapActionName($uri, $method)
    {
        $params = array_merge($_GET, $_POST);
        $id_str = "";
        
        // Cố gắng lấy ID nếu có trong request
        $id = $params['id'] ?? $params['id_monhoc'] ?? $params['id_baithi'] ?? $params['id_nguoidung'] ?? $params['id_cauhoi'] ?? $params['id_nganhang'] ?? null;
        if ($id) $id_str = " [ID: $id]";

        // 1. Phân tích tham số act (cho Web Portal)
        if (strpos($uri, 'act=') !== false) {
            if (strpos($uri, 'quanly-nguoidung') !== false) return "Quản lý người dùng";
            if (strpos($uri, 'quanly-monhoc') !== false) return "Quản lý môn học";
            if (strpos($uri, 'quanly-baithi') !== false) return "Quản lý bài thi";
            if (strpos($uri, 'quanly-nganhang-cauhoi') !== false) return "Quản lý Ngân hàng câu hỏi";
            if (strpos($uri, 'quanly-logs') !== false) return "Giám sát hệ thống";
            if (strpos($uri, 'dashboard') !== false) return "Xem Bảng điều khiển";
        }

        // 2. Phân tích API Route (cho Desktop/Mobile/AJAX)
        if (strpos($uri, 'api/') !== false) {
            $action = 'Truy cập';
            if ($method === 'POST') $action = ($id ? 'Cập nhật' : 'Thêm mới');
            if ($method === 'PATCH' || $method === 'PUT') $action = 'Cập nhật';
            if ($method === 'DELETE') $action = 'Xóa';
            if ($method === 'GET') $action = 'Xem';
            
            if (strpos($uri, 'login') !== false) return "Đăng nhập API";
            if (strpos($uri, 'nguoidung') !== false) return "$action người dùng$id_str";
            if (strpos($uri, 'monhoc') !== false) return "$action môn học$id_str";
            if (strpos($uri, 'baithi') !== false) return "$action bài thi$id_str";
            if (strpos($uri, 'nganhang') !== false) return "$action ngân hàng câu hỏi$id_str";
            if (strpos($uri, 'cauhoi') !== false) return "$action câu hỏi$id_str";
            if (strpos($uri, 'profile') !== false) return "Cập nhật hồ sơ cá nhân";
        }

        return "Truy cập hệ thống";
    }

    /**
     * Kiểm tra xem IP có đang bị khóa do thử sai quá nhiều không
     */
    public static function checkBruteForce($ip)
    {
        $conn = Database::connect();
        // Đếm số lần login sai (401) trong 15 phút qua từ IP này
        $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM api_logs 
                                WHERE ip_address = ? 
                                AND response_code = 401 
                                AND endpoint LIKE '%login.php%'
                                AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        // $conn->close();

        return ($result['attempts'] ?? 0) >= 5; // Khóa nếu sai từ 5 lần trở lên
    }

    /**
     * Get the real IP address of the client
     */
    public static function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
}
