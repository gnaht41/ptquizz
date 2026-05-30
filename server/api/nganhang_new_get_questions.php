<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang_new.model.php";

Api::boot();
$user = Api::requireRole(['giangvien', 'admin']);

$id_nhch = (int) ($_GET['id'] ?? 0);
if ($id_nhch <= 0) {
    Api::json(["error" => "Thiếu ID ngân hàng"], 400);
}

$model = new NganHangModel();
$bank = $model->getBankById($id_nhch, $user['id_nguoidung']);
if (!$bank) {
    Api::json(["error" => "Ngân hàng không tồn tại hoặc không thuộc quyền của bạn"], 403);
}

$questions = $model->getQuestionsByBank($id_nhch);

Api::json(["success" => true, "data" => $questions]);
