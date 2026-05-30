<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$conn = Database::connect();
$user_id = $_SESSION["user"]["id"] ?? 0;

$mon = isset($_GET["mon"]) ? (int) $_GET["mon"] : 0;
$term = $_GET["term"] ?? "";
$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 0;

$where = "WHERE b.trangthai = 'Đang mở' 
          AND b.thoigianbatdau <= NOW() 
          AND (b.thoigianketthuc IS NULL OR b.thoigianketthuc >= NOW())";
$params = [$user_id];
$types = "i";

if ($mon > 0) {
    $where .= " AND b.id_monhoc = ?";
    $params[] = $mon;
    $types .= "i";
}

if ($term !== "") {
    $where .= " AND b.ten_baithi LIKE ?";
    $params[] = "%" . $term . "%";
    $types .= "s";
}

$sql = "
    SELECT
        b.id_baithi,
        b.ten_baithi,
        b.thoigianlam,
        b.tongcauhoi,
        m.tenmonhoc,
        l.thoigianconlai,
        CASE WHEN l.id_lanthi IS NOT NULL THEN 1 ELSE 0 END as is_ongoing
    FROM baithi b
    LEFT JOIN monhoc m ON b.id_monhoc = m.id_monhoc
    LEFT JOIN lanthi l ON b.id_baithi = l.id_baithi
        AND l.id_nguoidung = ?
        AND l.trangthai = 'ongoing'
    $where
    ORDER BY b.id_baithi DESC
";

if ($limit > 0) {
    $sql .= " LIMIT ?";
    $params[] = $limit;
    $types .= "i";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_all(MYSQLI_ASSOC);

Response::json(["success" => true, "data" => $data]);

