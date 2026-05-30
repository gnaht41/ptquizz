<?php
require_once "model/giangvien/baithi.model.php";

function baithi_index()
{
    // 1. Lấy ID từ Session (Phúc kiểm tra kỹ tên key 'id_nguoidung' trong Session của ông nhé)
    $id_nguoidung = $_SESSION['user']['id_nguoidung'] ?? 0;

    return [
        'title' => 'Quản lý Bài thi',
        'view' => 'views/giangvien/baithi/list.php',
        'data' => getAll_baithi($id_nguoidung),      // Truyền ID vào đây
        'list_monhoc' => getAll_monhoc($id_nguoidung) // Truyền ID vào đây luôn
    ];
}

function baithi_save()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = save_baithi($_POST);

        if ($result === false) {
            // Đã có lỗi, Model đã set $_SESSION['error'], quay về ngay
            header("Location: index.php?act=quanly-baithi");
            exit;
        } else {
            $_SESSION['success'] = "Lưu bài thi thành công!";
            header("Location: index.php?act=quanly-baithi");
            exit;
        }
    }
}

function baithi_delete()
{
    $id = $_GET['id'] ?? 0;
    if ($id > 0) {
        if (delete_baithi($id)) {
            $_SESSION['success'] = "Xóa bài thi thành công!";
        } else {
            $_SESSION['error'] = $_SESSION['error'] ?? "Không thể xóa bài thi.";
        }
    }
    header("Location: index.php?act=quanly-baithi");
    exit;
}
