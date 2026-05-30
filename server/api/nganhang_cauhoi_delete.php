<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang.model.php";

$user = Api::requireRole(["admin", "giangvien"]);
$data = Api::jsonInput();
$id_cauhoi_nganhang = (int) ($data["id_cauhoi_nganhang"] ?? 0);

if ($id_cauhoi_nganhang <= 0) {
    Api::json(["error" => "Thiếu ID câu hỏi ngân hàng"], 400);
}

$question = getQuestionBankQuestionById($id_cauhoi_nganhang, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
if (!$question) {
    Api::json(["error" => "Không tìm thấy câu hỏi ngân hàng"], 404);
}

if (!deleteQuestionBankQuestion($id_cauhoi_nganhang)) {
    Api::json(["error" => "Không thể xóa câu hỏi ngân hàng"], 500);
}

Api::json([
    "success" => true,
    "message" => "Đã xóa câu hỏi ngân hàng thành công",
]);
