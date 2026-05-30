<?php

require_once __DIR__ . "/../core/Api.php";
require_once __DIR__ . "/../model/Database.php";
require_once __DIR__ . "/../model/giangvien/cauhoi.model.php";

$user = Api::requireLogin();
$data = Api::jsonInput();

$id_baithi = (int) ($data["id_baithi"] ?? 0);
$id_cauhoi = (int) ($data["id_cauhoi"] ?? 0);
$noidungcauhoi = trim((string)($data["noidungcauhoi"] ?? ""));
$dokho = $data["dokho"] ?? "Dễ";
$loai_cauhoi = (int) ($data["loai_cauhoi"] ?? 1);
$options = $data["options"] ?? [];
$correctIndex = isset($data["correct_index"]) ? (int) $data["correct_index"] : -1;
$loigiai_chitiet = trim((string)($data["loigiai_chitiet"] ?? ""));

if ($id_baithi <= 0 || $noidungcauhoi === "" || $loigiai_chitiet === "") {
    Api::json(["error" => "Dữ liệu không hợp lệ: Cần nhập nội dung câu hỏi và lời giải chi tiết"], 400);
}

if ($loai_cauhoi === 1) {
    // Trắc nghiệm
    if (count($options) < 2 || $correctIndex < 0) {
        Api::json(["error" => "Trắc nghiệm cần ít nhất 2 đáp án và 1 đáp án đúng"], 400);
    }
} else {
    // Điền từ (Fill-in-the-blank)
    // Kiểm tra xem có dấu [...] nào không
    $placeholderCount = substr_count($noidungcauhoi, '[...]');
    if ($placeholderCount === 0) {
        Api::json(["error" => "Câu hỏi điền từ phải có ít nhất một dấu [...] trong nội dung"], 400);
    }
    
    if (count($options) !== $placeholderCount) {
        Api::json(["error" => "Số lượng đáp án (" . count($options) . ") phải khớp với số lượng dấu [...] trong câu hỏi ($placeholderCount)"], 400);
    }
    $correctIndex = 0; // Luôn coi là đúng cho logic model
}

$conn = Database::connect();
$role = $user["vaitro"] ?? "";
$ownerId = (int) ($user["id_nguoidung"] ?? 0);

// Kiểm tra quyền với bài thi
$sql = "SELECT bt.id_baithi
    FROM baithi bt
    JOIN monhoc mh ON bt.id_monhoc = mh.id_monhoc
    WHERE bt.id_baithi = ? AND (mh.id_nguoidung = ? OR ? = 'admin')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $id_baithi, $ownerId, $role);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    Api::json(["error" => "Bạn không có quyền sửa bài thi này"], 403);
}

$dapan_list = [];
$temp_check = [];
foreach ($options as $index => $noidung) {
    $noidung = trim((string) $noidung);
    if ($noidung === "") {
        Api::json(["error" => "Đáp án không được để trống"], 400);
    }

    $normalized = mb_strtolower($noidung, "UTF-8");
    // Chỉ check trùng đáp án với loại trắc nghiệm
    if ($loai_cauhoi === 1) {
        if (in_array($normalized, $temp_check, true)) {
            Api::json(["error" => "Các đáp án trắc nghiệm không được trùng nhau"], 400);
        }
        $temp_check[] = $normalized;
    }

    $isCorrect = ($loai_cauhoi === 2 || $index === $correctIndex) ? 1 : 0;
    $dapan_list[] = [
        "noidung" => $noidung,
        "dapandung" => $isCorrect,
        "loigiai_chitiet" => ($isCorrect && !empty($data["loigiai_chitiet"])) ? $data["loigiai_chitiet"] : null,
    ];
}

$model = new CauHoiModel();
$result = $id_cauhoi > 0
    ? $model->update($id_cauhoi, $noidungcauhoi, $dokho, $loai_cauhoi, $dapan_list)
    : $model->create($id_baithi, $noidungcauhoi, $dokho, $loai_cauhoi, $dapan_list);

if (!($result["success"] ?? false)) {
    Api::json(["error" => $result["message"] ?? "Không thể lưu câu hỏi"], 400);
}

Api::json([
    "success" => true,
    "message" => $id_cauhoi > 0 ? "Cập nhật câu hỏi thành công" : "Thêm câu hỏi thành công",
]);
