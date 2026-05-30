<?php
require_once __DIR__ . "/../Database.php";

function get_admin_dashboard_stats() {
    $conn = Database::connect();
    
    // 1. Tổng số người dùng
    $resUsers = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE vaitro != 'admin'");
    $totalUsers = $resUsers->fetch_assoc()['total'];

    // 2. Số tài khoản bị khóa
    $resLocked = $conn->query("SELECT COUNT(*) as total FROM nguoidung WHERE trangthai != 'active'");
    $totalLocked = $resLocked->fetch_assoc()['total'];

    // 3. Số cảnh báo bảo mật (401, 403, 429) trong 48h qua
    $resAlerts = $conn->query("SELECT COUNT(*) as total FROM api_logs WHERE response_code >= 400 AND created_at > DATE_SUB(NOW(), INTERVAL 48 HOUR)");
    $totalAlerts = $resAlerts->fetch_assoc()['total'];

    // 4. Tổng số request API trong 24h qua
    $resRequests = $conn->query("SELECT COUNT(*) as total FROM api_logs WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $totalRequests = $resRequests->fetch_assoc()['total'];

    // $conn->close();

    return [
        'total_users' => $totalUsers,
        'total_locked' => $totalLocked,
        'total_alerts' => $totalAlerts,
        'total_requests' => $totalRequests
    ];
}

function get_recent_critical_logs() {
    $conn = Database::connect();
    $sql = "SELECT l.*, n.ten FROM api_logs l 
            LEFT JOIN nguoidung n ON l.id_nguoidung = n.id_nguoidung 
            WHERE l.response_code >= 400 
            ORDER BY l.created_at DESC LIMIT 5";
    $res = $conn->query($sql);
    $data = $res->fetch_all(MYSQLI_ASSOC);
    // $conn->close();
    return $data;
}
