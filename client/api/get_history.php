<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Response.php";


$conn = Database::connect();
$user_id = $_SESSION["user"]["id"];

$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 10;
$offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM lanthi WHERE id_nguoidung = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()["total"];

$sql = "
    SELECT
        l.id_lanthi,
        b.id_baithi,
        b.ten_baithi,
        b.tongcauhoi,
        l.socaudung,
        l.thoigianbatdau,
        l.thoigiannop,
        l.trangthai,
        l.diem
    FROM lanthi l
    JOIN baithi b ON l.id_baithi = b.id_baithi
    WHERE l.id_nguoidung = ?
    ORDER BY l.thoigianbatdau DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
}

Response::json([
    "success" => true,
    "data" => $history,
    "total" => (int) $total,
    "limit" => $limit,
    "offset" => $offset,
]);

