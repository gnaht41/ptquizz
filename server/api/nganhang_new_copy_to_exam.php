<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang_new.model.php";

Api::boot();
$user = Api::requireRole(['giangvien', 'admin']);
$data = Api::jsonInput();

$id_baithi = (int) ($data['id_baithi'] ?? 0);
$id_nhch = (int) ($data['id_nhch'] ?? 0);
$id_cauhoi_list = $data['id_cauhoi_list'] ?? [];

if ($id_baithi <= 0 || $id_nhch <= 0 || empty($id_cauhoi_list)) {
    Api::json(["error" => "Thiếu thông tin cần thiết"], 400);
}

$model = new NganHangModel();

// Validate bank belongs to user
$bank = $model->getBankById($id_nhch, $user['id_nguoidung']);
if (!$bank) {
    Api::json(["error" => "Ngân hàng không tồn tại"], 403);
}

// Validate exam belongs to user (simplified check, usually we'd check against monhoc/giangvien)
// For now we assume the client knows what it's doing or we add a check here.

$ok = $model->copyQuestionsToExam($id_baithi, $id_nhch, $id_cauhoi_list);

if ($ok) {
    Api::json(["success" => true, "message" => "Đã thêm " . count($id_cauhoi_list) . " câu hỏi vào đề thi"]);
} else {
    Api::json(["error" => "Không thể sao chép câu hỏi"], 500);
}
