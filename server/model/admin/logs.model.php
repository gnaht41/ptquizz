<?php
require_once __DIR__ . "/../Database.php";

function get_api_logs($filters = []) {
    $conn = Database::connect();
    
    // Lấy 100 bản ghi mới nhất để đảm bảo tốc độ cực nhanh
    $sql = "SELECT l.*, IFNULL(n.ten, 'Khách vãng lai') as ten, n.vaitro, n.trangthai 
            FROM api_logs l 
            LEFT JOIN nguoidung n ON l.id_nguoidung = n.id_nguoidung 
            WHERE 1=1";
    
    $params = [];
    $types = "";

    // Lọc theo mã trạng thái
    if (!empty($filters['status_code'])) {
        if ($filters['status_code'] == 'error') {
            $sql .= " AND l.response_code >= 400";
        } else {
            $sql .= " AND l.response_code = ?";
            $types .= "i";
            $params[] = (int)$filters['status_code'];
        }
    }

    // Tìm kiếm theo IP hoặc Endpoint hoặc Tên (nếu có)
    if (!empty($filters['method'])) {
        $sql .= " AND l.method = ?";
        $types .= "s";
        $params[] = $filters['method'];
    }

    if (!empty($filters['keyword'])) {
        $like = "%" . $filters['keyword'] . "%";
        $sql .= " AND (l.endpoint LIKE ? OR l.ip_address LIKE ? OR n.ten LIKE ?)";
        $types .= "sss";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    // Lọc theo ngày
    if (!empty($filters['date'])) {
        $sql .= " AND DATE(l.created_at) = ?";
        $types .= "s";
        $params[] = $filters['date'];
    }

    $sql .= " ORDER BY l.created_at DESC LIMIT 100";
    
    $stmt = $conn->prepare($sql);
    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    return $data;
}
