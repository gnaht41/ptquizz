<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/ketqua.model.php";

Api::requireLogin();

$id_baithi = isset($_GET['id_baithi']) ? (int)$_GET['id_baithi'] : 0;

if ($id_baithi <= 0) {
    Api::json(["success" => false, "message" => "ID bài thi không hợp lệ"], 400);
}

$data = get_exam_submissions($id_baithi);

Api::json([
    "success" => true,
    "data" => $data
]);
