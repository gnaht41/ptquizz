<?php
require_once __DIR__ . "/../../model/giangvien/ketqua.model.php";

function ketqua_index() {
    $id_giangvien = $_SESSION['user']['id_nguoidung'] ?? 0;
    
    // Nếu có id_baithi => xem cụ thể danh sách thí sinh của bài thi đó
    if (isset($_GET['id_baithi'])) {
        $id_baithi = (int)$_GET['id_baithi'];
        $submissions = get_exam_submissions($id_baithi);
        
        // Cần lấy tên bài thi để hiện tiêu đề
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT ten_baithi FROM baithi WHERE id_baithi = ?");
        $stmt->bind_param("i", $id_baithi);
        $stmt->execute();
        $baithi = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        // $conn->close();

        return [
            'id_baithi' => $id_baithi,
            'title' => "Kết quả: " . ($baithi['ten_baithi'] ?? '---'),
            'view' => "views/giangvien/ketqua/submissions.php",
            'data' => $submissions,
            'baithi' => $baithi
        ];
    }
    
    // Nếu có id_lanthi => xem chi tiết bài làm của 1 thí sinh
    if (isset($_GET['id_lanthi'])) {
        $id_lanthi = (int)$_GET['id_lanthi'];
        $detail = get_submission_detail($id_lanthi);
        
        return [
            'title' => "Chi tiết bài làm: " . ($detail['info']['ten'] ?? '---'),
            'view' => "views/giangvien/ketqua/detail.php",
            'data' => $detail
        ];
    }

    // Mặc định: hiện danh sách các bài thi để chọn
    $list_baithi = get_exam_results_summary($id_giangvien);
    return [
        'title' => "Kết quả thi",
        'view' => "views/giangvien/ketqua/list.php",
        'data' => $list_baithi
    ];
}
