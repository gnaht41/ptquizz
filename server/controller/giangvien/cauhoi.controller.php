<?php
// controller/giangvien/cauhoi.controller.php
require_once __DIR__ . '/../../model/giangvien/cauhoi.model.php';
$cauhoiModel = new CauHoiModel();

$act = isset($_GET['act']) ? $_GET['act'] : '';
$parts = explode('-', $act);
$action = isset($parts[1]) ? $parts[1] : 'list';

$id_baithi = isset($_GET['id_baithi']) ? (int)$_GET['id_baithi'] : (isset($_POST['id_baithi']) ? (int)$_POST['id_baithi'] : 0);

switch ($action) {
    case 'list':
        $baithi = $cauhoiModel->getBaiThiInfo($id_baithi);
        $list_cauhoi = $cauhoiModel->getByBaiThi($id_baithi);
        $ten_baithi = $baithi['tenbaithi'] ?? 'Danh sách câu hỏi';
        $view = "views/giangvien/cauhoi/list.php";
        break;

    case 'add':
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_cauhoi = isset($_POST['id_cauhoi']) ? (int)$_POST['id_cauhoi'] : 0;
            $noidungcauhoi = trim($_POST['noidungcauhoi']);
            $dokho = $_POST['dokho'];
            $options = $_POST['option'] ?? [];

            // FIX LỖI Ở ĐÂY: Radio trả về chuỗi index, không phải mảng
            $correct_index = isset($_POST['is_correct']) ? (int)$_POST['is_correct'] : -1;

            // Kiểm tra số lượng khi THÊM MỚI
            if ($action == 'add') {
                $baithi = $cauhoiModel->getBaiThiInfo($id_baithi);
                $current_q = $cauhoiModel->getByBaiThi($id_baithi);
                if (count($current_q) >= $baithi['tongcauhoi']) {
                    $_SESSION['error'] = "⚠️ Đã đạt giới hạn tối đa {$baithi['tongcauhoi']} câu hỏi.";
                    header("Location: index.php?act=cauhoi-list&id_baithi=$id_baithi");
                    exit;
                }
            }

            // Kiểm tra tính hợp lệ dữ liệu
            if (empty($noidungcauhoi) || count($options) < 2 || $correct_index === -1) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ nội dung và chọn đáp án đúng!";
                header("Location: index.php?act=cauhoi-list&id_baithi=$id_baithi");
                exit;
            }

            // Xử lý danh sách đáp án & Check trùng đáp án
            $dapan_list = [];
            $temp_check = [];
            foreach ($options as $index => $noidung) {
                $noidung = trim($noidung);
                if (in_array(mb_strtolower($noidung), $temp_check)) {
                    $_SESSION['error'] = "⚠️ Các đáp án không được trùng nhau!";
                    header("Location: index.php?act=cauhoi-list&id_baithi=$id_baithi");
                    exit;
                }
                $temp_check[] = mb_strtolower($noidung);

                $dapan_list[] = [
                    'noidung' => $noidung,
                    'dapandung' => ($index === $correct_index) ? 1 : 0
                ];
            }

            // Gọi Model xử lý
            if ($action == 'add') {
                $result = $cauhoiModel->create($id_baithi, $noidungcauhoi, $dokho, $dapan_list);
            } else {
                $result = $cauhoiModel->update($id_cauhoi, $noidungcauhoi, $dokho, $dapan_list);
            }

            if ($result['success']) {
                $_SESSION['success'] = ($action == 'add' ? 'Thêm' : 'Cập nhật') . ' câu hỏi thành công!';
            } else {
                $_SESSION['error'] = "Lỗi: " . $result['message'];
            }

            header("Location: index.php?act=cauhoi-list&id_baithi=$id_baithi");
            exit;
        }
        break;

    case 'delete':
        $id_cauhoi = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id_cauhoi > 0) {
            $result = $cauhoiModel->delete($id_cauhoi);
            if ($result['success']) {
                $_SESSION['success'] = "Đã xóa câu hỏi thành công!";
            } else {
                $_SESSION['error'] = "Lỗi xóa: " . $result['message'];
            }
        }
        header("Location: index.php?act=cauhoi-list&id_baithi=$id_baithi");
        exit;
}
