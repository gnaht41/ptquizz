<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang_new.model.php";

Api::boot();
$user = Api::requireRole(['giangvien', 'admin']);

$model = new NganHangModel();
$banks = $model->getAllBanks($user['id_nguoidung']);

Api::json(["success" => true, "data" => $banks]);
