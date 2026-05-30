<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/ketqua.model.php";

$user = Api::requireLogin();
$id_giangvien = $user['id_nguoidung'];

$data = get_exam_results_summary($id_giangvien);

Api::json([
    "success" => true,
    "data" => $data
]);
