<?php
require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/ketqua.model.php";

Api::requireLogin();

$id_lanthi = isset($_GET['id_lanthi']) ? (int)$_GET['id_lanthi'] : 0;

if ($id_lanthi <= 0) {
    Api::json(["success" => false, "message" => "ID lần thi không hợp lệ"], 400);
}

$data = get_submission_detail($id_lanthi);

Api::json([
    "success" => true,
    "data" => $data
]);
