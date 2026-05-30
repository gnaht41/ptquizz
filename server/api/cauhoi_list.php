<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";
require_once __DIR__ . "/../model/giangvien/cauhoi.model.php";

$user = Api::requireLogin();

$id_baithi = isset($_GET["id_baithi"]) ? (int) $_GET["id_baithi"] : 0;

if ($id_baithi <= 0) {
    Api::json(["error" => "Thi?u ID bài thi"], 400);
}

$conn = Database::connect();
$sql = "SELECT bt.id_baithi
    FROM baithi bt
    JOIN monhoc mh ON bt.id_monhoc = mh.id_monhoc
    WHERE bt.id_baithi = ? AND (mh.id_nguoidung = ? OR ? = 'admin')";
$stmt = $conn->prepare($sql);
$role = $user["vaitro"] ?? "";
$ownerId = (int) ($user["id_nguoidung"] ?? 0);
$stmt->bind_param("iis", $id_baithi, $ownerId, $role);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    // $conn->close();
    Api::json(["error" => "B?n không có quy?n truy c?p bài thi này"], 403);
}
// $conn->close();

$model = new CauHoiModel();
$baithi = $model->getBaiThiInfo($id_baithi);

if (!$baithi) {
    Api::json(["error" => "Không tìm th?y bài thi"], 404);
}

Api::json([
    "success" => true,
    "baithi" => $baithi,
    "is_locked" => $model->isBaiThiLocked($id_baithi) ? 1 : 0,
    "questions" => $model->getByBaiThi($id_baithi),
]);
