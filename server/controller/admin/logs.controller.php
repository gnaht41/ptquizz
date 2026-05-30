<?php
require_once __DIR__ . '/../../model/Database.php';

function logs_index()
{
    $conn = Database::connect();
    
    // Tiếp nhận bộ lọc
    $date = $_GET['date'] ?? '';
    $method = $_GET['method'] ?? '';
    $keyword = $_GET['keyword'] ?? '';

    $where = ["1=1"];
    $params = [];
    $types = "";

    if ($date !== '') {
        $where[] = "DATE(l.created_at) = ?";
        $params[] = $date;
        $types .= "s";
    }

    if ($method !== '') {
        $where[] = "l.method = ?";
        $params[] = $method;
        $types .= "s";
    }

    if ($keyword !== '') {
        $where[] = "(n.ten LIKE ? OR n.email LIKE ? OR l.action_name LIKE ?)";
        $like = "%$keyword%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $types .= "sss";
    }

    $where_sql = implode(" AND ", $where);

    $sql = "SELECT l.*, n.ten, n.email, n.trangthai, n.vaitro 
            FROM api_logs l 
            LEFT JOIN nguoidung n ON l.id_nguoidung = n.id_nguoidung 
            WHERE $where_sql
            ORDER BY l.created_at DESC 
            LIMIT 50"; // Giảm xuống 50 để chạy cực nhanh
            
    $stmt = $conn->prepare($sql);
    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $list_logs = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Ưu tiên dùng tên hành động đã được SecurityLogger dịch sẵn
            if (!empty($row['action_name']) && $row['action_name'] !== 'Truy cập hệ thống') {
                $row['clean_action'] = $row['action_name'];
            } else {
                $row['clean_action'] = formatActionName($row['endpoint']);
            }
            $list_logs[] = $row;
        }
    }
    
    // $conn->close();
    
    return [
        'title' => 'Giám sát Bảo mật API',
        'view' => 'views/admin/logs/list.php',
        'data' => $list_logs
    ];
}

function formatActionName($endpoint)
{
    $url_parts = parse_url($endpoint);
    $query = $url_parts['query'] ?? '';
    parse_str($query, $params);
    
    $act = $params['act'] ?? '';
    if ($act) {
        return "Quản lý: " . ucwords(str_replace(['-', 'quanly'], [' ', ''], $act));
    }
    
    // Check if it's an API route
    if (strpos($endpoint, '/api/') !== false) {
        $path_parts = explode('/api/', $endpoint);
        $api_action = end($path_parts);
        return "API: " . ucwords(str_replace(['_', '.php'], [' ', ''], $api_action));
    }
    
    return $endpoint;
}
