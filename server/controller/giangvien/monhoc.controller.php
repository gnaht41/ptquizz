<?php
require_once "model/giangvien/monhoc.model.php";

function monhoc_index()
{
    $id_nguoidung = $_SESSION['user']['id_nguoidung'] ?? 0;
    $vaitro = $_SESSION['user']['vaitro'] ?? '';

    $list_monhoc = getAll_monhoc_with_user($id_nguoidung, $vaitro);

    return [
        'title' => 'Quản lý Môn học',
        'view' => 'views/giangvien/monhoc/list.php',
        'data' => $list_monhoc
    ];
}

function monhoc_save()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $id_monhoc = (int) ($_POST['id_monhoc'] ?? 0);
    $tenInput = trim($_POST['tenmonhoc'] ?? '');
    $id_nguoidung = (int) ($_SESSION['user']['id_nguoidung'] ?? 0);
    $vaitro = $_SESSION['user']['vaitro'] ?? '';
    $mieuta = $_POST['mieuta'] ?? null;

    if ($tenInput === '') {
        $_SESSION['error'] = "Tên môn học không được để trống!";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?act=quanly-monhoc'));
        exit;
    }

    if (isDuplicateMonHoc($tenInput, $id_monhoc)) {
        $_SESSION['error'] = "Tên môn học này đã tồn tại trong hệ thống!";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?act=quanly-monhoc'));
        exit;
    }

    if ($id_monhoc > 0) {
        $existing = getOne_monhoc($id_monhoc);
        if (!$existing) {
            $_SESSION['error'] = "Không tìm thấy môn học!";
            header("Location: index.php?act=quanly-monhoc");
            exit;
        }

        if ($vaitro !== 'admin' && (int) $existing['id_nguoidung'] !== $id_nguoidung) {
            $_SESSION['error'] = "Bạn không có quyền sửa môn học này!";
            header("Location: index.php?act=quanly-monhoc");
            exit;
        }

        update_monhoc_by_owner($id_monhoc, $tenInput, $mieuta, $id_nguoidung, $vaitro);
        $_SESSION['success'] = "Cập nhật môn học thành công!";
    } else {
        insert_monhoc($tenInput, $id_nguoidung, $mieuta);
        $_SESSION['success'] = "Thêm môn học mới thành công!";
    }

    header("Location: index.php?act=quanly-monhoc");
    exit;
}

function monhoc_delete()
{
    $id_monhoc = (int) ($_GET['id'] ?? 0);
    $id_nguoidung = (int) ($_SESSION['user']['id_nguoidung'] ?? 0);
    $vaitro = $_SESSION['user']['vaitro'] ?? '';

    if ($id_monhoc > 0) {
        $existing = getOne_monhoc($id_monhoc);

        if (!$existing) {
            $_SESSION['error'] = "Không tìm thấy môn học!";
        } elseif ($vaitro !== 'admin' && (int) $existing['id_nguoidung'] !== $id_nguoidung) {
            $_SESSION['error'] = "Bạn không có quyền xóa môn học này!";
        } elseif (count_baithi_by_monhoc($id_monhoc) > 0) {
            $_SESSION['error'] = "Môn học đang có bài thi liên quan, hãy xóa bài thi trước!";
        } elseif (delete_monhoc($id_monhoc, $id_nguoidung, $vaitro)) {
            $_SESSION['success'] = "Đã xóa môn học thành công!";
        } else {
            $_SESSION['error'] = "Không thể xóa môn học này hoặc bạn không có quyền!";
        }
    }

    header("Location: index.php?act=quanly-monhoc");
    exit;
}
