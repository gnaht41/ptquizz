<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang_new.model.php";

Api::boot();
$user = Api::requireRole(['giangvien', 'admin']);
$data = Api::jsonInput();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_nganhang = trim($data['ten_nganhang'] ?? '');
    $id_mon = (int) ($data['id_mon'] ?? 0);
    $mota = trim($data['mota'] ?? '');

    if (empty($ten_nganhang) || $id_mon <= 0) {
        Api::json(["error" => "Tên ngân hàng và môn học là bắt buộc"], 400);
    }

    $model = new NganHangModel();
    $id = $model->createBank([
        'ten_nganhang' => $ten_nganhang,
        'id_mon' => $id_mon,
        'id_giangvien' => $user['id_nguoidung'],
        'mota' => $mota,
        'trangthai' => 1
    ]);

    if ($id) {
        Api::json(["success" => true, "id_nhch" => $id, "message" => "Tạo ngân hàng câu hỏi thành công"]);
    } else {
        Api::json(["error" => "Không thể tạo ngân hàng câu hỏi"], 500);
    }
}
