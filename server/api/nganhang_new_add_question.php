<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang_new.model.php";

Api::boot();
$user = Api::requireRole(['giangvien', 'admin']);
$data = Api::jsonInput();

$id_nhch = (int) ($data['id_nhch'] ?? 0);
if ($id_nhch <= 0) {
    Api::json(["error" => "Thiếu ID ngân hàng"], 400);
}

$model = new NganHangModel();
$bank = $model->getBankById($id_nhch, $user['id_nguoidung']);
if (!$bank) {
    Api::json(["error" => "Ngân hàng không tồn tại hoặc không thuộc quyền của bạn"], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noidung = trim($data['noidungcauhoi'] ?? '');
    $dokho = trim($data['dokho'] ?? 'Dễ');
    $dapan_list = $data['dapan_list'] ?? [];

    if (empty($noidung) || count($dapan_list) < 2) {
        Api::json(["error" => "Nội dung câu hỏi và ít nhất 2 đáp án là bắt buộc"], 400);
    }

    $id_cauhoi = $model->addQuestionToBank($id_nhch, [
        'noidungcauhoi' => $noidung,
        'dokho' => $dokho,
        'dapan_list' => $dapan_list
    ]);

    if ($id_cauhoi) {
        Api::json(["success" => true, "id_cauhoi" => $id_cauhoi, "message" => "Thêm câu hỏi vào ngân hàng thành công"]);
    } else {
        Api::json(["error" => "Không thể thêm câu hỏi"], 500);
    }
}
