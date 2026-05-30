<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/cauhoi.model.php";

Api::boot();
$user = Api::requireRole(["admin", "giangvien"]);
$data = Api::jsonInput();

$id_baithi = (int) ($data["id_baithi"] ?? 0);
$id_nhch = (int) ($data["id_nhch"] ?? 0);
$counts = $data["counts"] ?? []; // e.g. ['Dễ' => 5, 'Trung bình' => 3, 'Khó' => 2]

if ($id_baithi <= 0 || $id_nhch <= 0 || empty($counts)) {
    Api::json(["error" => "Thiếu thông tin cần thiết"], 400);
}

$model = new CauHoiModel();
$result = $model->importFromBank($id_baithi, $id_nhch, $counts);

if ($result["success"]) {
    Api::json([
        "success" => true,
        "message" => "Đã import thành công " . $result["count"] . " câu hỏi từ ngân hàng vào bài thi.",
        "count" => $result["count"]
    ]);
} else {
    Api::json(["error" => $result["message"] ?? "Không thể import câu hỏi từ ngân hàng"], 400);
}
