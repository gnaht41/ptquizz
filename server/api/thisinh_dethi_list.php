<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";

$user = Api::requireRole(["thisinh"]);
$id_nguoidung = $user["id_nguoidung"];

$conn = Database::connect();

$sql = "
    SELECT 
        b.id_baithi, 
        b.ten_baithi, 
        b.thoigianlam, 
        b.tongcauhoi, 
        m.tenmonhoc,
        (SELECT COUNT(*) FROM ketqua WHERE id_baithi = b.id_baithi AND id_nguoidung = ?) as lanthi
    FROM baithi b
    LEFT JOIN monhoc m ON b.id_monhoc = m.id_monhoc
    WHERE b.trangthai = 'Đang mở'
    ORDER BY b.id_baithi DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_nguoidung);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// $conn->close();

Api::json(["success" => true, "data" => $data]);
