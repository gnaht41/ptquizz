<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang.model.php";

$user = Api::requireRole(["admin", "giangvien"]);

$id_nganhang = isset($_GET["id_nganhang"]) ? (int) $_GET["id_nganhang"] : 0;
$id_monhoc = isset($_GET["id_monhoc"]) ? (int) $_GET["id_monhoc"] : 0;

if ($id_nganhang <= 0) {
    Api::json(["error" => "Thiếu ID ngân hàng câu hỏi"], 400);
}

$bank = getQuestionBankById($id_nganhang, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
if (!$bank) {
    Api::json(["error" => "Không tìm thấy ngân hàng câu hỏi"], 404);
}

if ($id_monhoc <= 0) {
    $firstSubject = $bank["subjects"][0]["id_monhoc"] ?? 0;
    $id_monhoc = (int) $firstSubject;
}

Api::json([
    "success" => true,
    "bank" => $bank,
    "selected_subject_id" => $id_monhoc,
    "questions" => $id_monhoc > 0
        ? getQuestionBankQuestions($id_nganhang, $id_monhoc, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "")
        : [],
]);
