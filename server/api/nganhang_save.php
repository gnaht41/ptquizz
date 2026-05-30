<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../core/ApiSecurityValidator.php";
require_once __DIR__ . "/../model/giangvien/nganhang.model.php";

$user = Api::requireRole(["admin", "giangvien"]);
$data = Api::jsonInput();

try {
    $id_nganhang = isset($data["id_nganhang"]) ? ApiSecurityValidator::validateInt($data["id_nganhang"], "ID ngân hàng", 0) : 0;
    $ten_nganhang = ApiSecurityValidator::validateString($data["ten_nganhang"] ?? "", "Tên ngân hàng", 3, 255);
    $mieuta = trim((string) ($data["mieuta"] ?? ""));
    $mieuta = $mieuta === "" ? null : ApiSecurityValidator::validateString($mieuta, "Mô tả", 0, 1000);
    $trangthai = ApiSecurityValidator::validateEnum($data["trangthai"] ?? "active", "Trạng thái", ["active", "inactive"]);
} catch (Exception $e) {
    Api::json(["error" => $e->getMessage()], 400);
}

$subjectIds = $data["subject_ids"] ?? [];
if (!is_array($subjectIds)) {
    Api::json(["error" => "Danh sách môn học không hợp lệ"], 400);
}

$validSubjects = validateBankSubjects($subjectIds, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
if ($validSubjects === false) {
    Api::json(["error" => "Có môn học không thuộc quyền truy cập của bạn"], 403);
}
if (empty($validSubjects)) {
    Api::json(["error" => "Vui lòng chọn ít nhất một môn học"], 400);
}

if ($id_nganhang > 0) {
    $existing = getQuestionBankById($id_nganhang, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
    if (!$existing) {
        Api::json(["error" => "Không tìm thấy ngân hàng câu hỏi"], 404);
    }

    $ok = updateQuestionBank($id_nganhang, $ten_nganhang, $mieuta, $trangthai, $validSubjects);
    if (!$ok) {
        Api::json(["error" => "Không thể cập nhật ngân hàng câu hỏi"], 500);
    }

    Api::json([
        "success" => true,
        "message" => "Cập nhật ngân hàng câu hỏi thành công",
        "data" => getQuestionBankById($id_nganhang, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? ""),
    ]);
}

$newId = createQuestionBank($ten_nganhang, $mieuta, $validSubjects, (int) ($user["id_nguoidung"] ?? 0));
if ($newId <= 0) {
    Api::json(["error" => "Không thể tạo ngân hàng câu hỏi"], 500);
}

Api::json([
    "success" => true,
    "message" => "Tạo ngân hàng câu hỏi thành công",
    "data" => getQuestionBankById($newId, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? ""),
], 201);
