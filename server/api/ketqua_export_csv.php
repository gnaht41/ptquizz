<?php
require_once __DIR__ . "/../model/Database.php";
require_once __DIR__ . "/../model/giangvien/ketqua.model.php";

// Auth and Roles are handled by the central API router (api/index.php)

$id_baithi = (int)($_GET['id_baithi'] ?? 0);
if (!$id_baithi) {
    echo json_encode(["error" => "Invalid Exam ID"]);
    exit;
}

// Fetch data
$submissions = get_exam_submissions($id_baithi);

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ketqua_baithi_'.$id_baithi.'_'.date('Ymd').'.csv');

// Add BOM for UTF-8 Excel support
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

// Headers
fputcsv($output, ['STT', 'Họ tên', 'Email', 'Điểm', 'Số câu đúng', 'Thời gian nộp', 'Thời lượng (giây)']);

// Data
foreach ($submissions as $index => $sub) {
    fputcsv($output, [
        $index + 1,
        $sub['ten_thisinh'],
        $sub['email_thisinh'],
        $sub['diem'],
        $sub['socaudung'],
        $sub['thoigiannop'],
        $sub['thoi_gian_lam_giay']
    ]);
}

fclose($output);
exit;
