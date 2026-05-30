<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/giangvien/nganhang.model.php";

$user = Api::requireLogin();
$data = Api::jsonInput();

$id_cauhoi_nganhang = (int) ($data["id_cauhoi_nganhang"] ?? 0);
$id_nganhang = (int) ($data["id_nganhang"] ?? 0);
$id_monhoc = (int) ($data["id_monhoc"] ?? 0);
$noidungcauhoi = trim((string)($data["noidungcauhoi"] ?? ""));
$dokho = trim((string)($data["dokho"] ?? "de"));
$loai_cauhoi = (int) ($data["loai_cauhoi"] ?? 1);
$trangthai = trim((string)($data["trangthai"] ?? "active"));
$options = $data["options"] ?? [];
$correctIndex = isset($data["correct_index"]) ? (int) $data["correct_index"] : -1;

$loigiai_chitiet = trim((string)($data["loigiai_chitiet"] ?? ""));

if ($noidungcauhoi === "" || $loigiai_chitiet === "") {
    Api::json(["error" => "Nội dung câu hỏi và lời giải chi tiết không được để trống"], 400);
}

if ($loai_cauhoi === 1) {
    // Trắc nghiệm
    if (count($options) < 2 || $correctIndex < 0) {
        Api::json(["error" => "Trắc nghiệm cần ít nhất 2 đáp án và 1 đáp án đúng"], 400);
    }
} else {
    // Điền từ (Fill-in-the-blank)
    $placeholderCount = substr_count($noidungcauhoi, '[...]');
    if ($placeholderCount === 0) {
        Api::json(["error" => "Câu hỏi điền từ phải có ít nhất một dấu [...] trong nội dung"], 400);
    }
    
    if (count($options) !== $placeholderCount) {
        Api::json(["error" => "Số lượng đáp án (" . count($options) . ") phải khớp với số lượng dấu [...] trong câu hỏi ($placeholderCount)"], 400);
    }
    $correctIndex = 0; // Force first
}

$answers = [];
$temp_check = [];
foreach ($options as $index => $noidung) {
    $noidung = trim((string) $noidung);
    if ($noidung === "") {
        Api::json(["error" => "Đáp án không được để trống"], 400);
    }
    
    // Normalize for duplicate check (only for multiple choice)
    if ($loai_cauhoi === 1) {
        $normalized = mb_strtolower($noidung, "UTF-8");
        if (in_array($normalized, $temp_check, true)) {
            Api::json(["error" => "Các đáp án không được trùng nhau"], 400);
        }
        $temp_check[] = $normalized;
    }

    $isCorrect = ($loai_cauhoi === 2 || $index === $correctIndex) ? 1 : 0;
    $answers[] = [
        "noidung" => $noidung,
        "dapandung" => $isCorrect,
        "loigiai_chitiet" => $isCorrect ? $loigiai_chitiet : null,
    ];
}

// Logic check permissions is inside Model usually or we can do it here
// For simplicity assuming model handles or we verify bank exists
if ($id_cauhoi_nganhang > 0) {
    $existingQuestion = getQuestionBankQuestionById($id_cauhoi_nganhang, (int) ($user["id_nguoidung"] ?? 0), $user["vaitro"] ?? "");
    if (!$existingQuestion) {
        Api::json(["error" => "Không tìm thấy câu hỏi ngân hàng"], 404);
    }

    $ok = updateQuestionBankQuestion($id_cauhoi_nganhang, $id_monhoc, $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai, $answers);
    if (!$ok) {
        Api::json(["error" => "Không thể cập nhật câu hỏi ngân hàng"], 500);
    }

    Api::json([
        "success" => true,
        "message" => "Cập nhật câu hỏi ngân hàng thành công",
    ]);
}

if ($id_nganhang <= 0) {
    Api::json(["error" => "ID ngân hàng không hợp lệ"], 400);
}

$newId = createQuestionBankQuestion($id_nganhang, $id_monhoc, $noidungcauhoi, $dokho, $loai_cauhoi, $trangthai, $answers);
if ($newId <= 0) {
    Api::json(["error" => "Không thể tạo câu hỏi ngân hàng"], 500);
}

Api::json([
    "success" => true,
    "message" => "Tạo câu hỏi ngân hàng thành công",
], 201);
