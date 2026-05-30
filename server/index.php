<?php
ob_start();
session_start();

require_once "model/Database.php";
require_once "core/SecurityLogger.php";
require_once "model/giangvien/monhoc.model.php";

// Log the request
SecurityLogger::logRequest($_SESSION['user']['id_nguoidung'] ?? null, http_response_code());
require_once "model/giangvien/baithi.model.php";
require_once "model/giangvien/cauhoi.model.php"; // Thêm model câu hỏi
require_once "model/giangvien/ketqua.model.php"; // Thêm model kết quả thi


$user_role = $_SESSION['user']['vaitro'] ?? '';
$act = $_GET['act'] ?? 'dashboard';

if (!isset($_SESSION['user']) && $act != 'login') {
    header("Location: index.php?act=login");
    exit;
}

// Check if logged in user is still active
if (isset($_SESSION['user'])) {
    $conn = Database::connect();
    $stmt = $conn->prepare("SELECT trangthai FROM nguoidung WHERE id_nguoidung = ?");
    $stmt->bind_param("i", $_SESSION['user']['id_nguoidung']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userStatus = $result->fetch_assoc();
    // $conn->close();

    if (!$userStatus || $userStatus['trangthai'] !== 'active') {
        session_destroy();
        header("Location: index.php?act=login&error=account_locked");
        exit;
    }
}

switch ($act) {
    case 'login':
        $title = "Đăng nhập";
        $view = "views/auth/login.php";
        break;

    case 'dashboard':
        $title = "Bảng điều khiển";
        if ($user_role === 'giangvien') {
            $view = "views/giangvien/dashboard.php";
            $stats = get_lecturer_dashboard_stats($_SESSION['user']['id_nguoidung']);
            $chartData = get_lecturer_chart_data($_SESSION['user']['id_nguoidung']);
            $data = [
                'stats' => $stats,
                'chartData' => $chartData
            ];
        }
        if ($user_role === 'admin') {
            require_once "model/admin/dashboard.model.php";
            $view = "views/admin/dashboard.php";
            $stats = get_admin_dashboard_stats();
            $recentAlerts = get_recent_critical_logs();
            $data = [
                'stats' => $stats,
                'alerts' => $recentAlerts
            ];
        }
        break;

    case 'quanly-nguoidung':
        if ($user_role !== 'admin') {
            $title = "404 - Không tìm thấy";
            $view = "views/404.php";
            break;
        }

        $title = "Quản lý người dùng";
        $view = "views/admin/nguoidung/list.php";
        break;

    case 'quanly-nganhang-cauhoi':
        if (!in_array($user_role, ['admin', 'giangvien'], true)) {
            $title = "404 - Không tìm thấy";
            $view = "views/404.php";
            break;
        }

        $title = "Ngân hàng câu hỏi";
        $view = "views/giangvien/nganhang/list.php";
        break;

    case 'profile':
        if (!in_array($user_role, ['admin', 'giangvien'], true)) {
            $title = "404 - Không tìm thấy";
            $view = "views/404.php";
            break;
        }

        $title = "Thông tin cá nhân";
        $view = "views/profile.php";
        break;

    case 'dangxuat':
        session_destroy();
        header("Location: index.php?act=login");
        exit;

    // --- QUẢN LÝ MÔN HỌC ---
    case 'quanly-monhoc':
        require_once "controller/giangvien/monhoc.controller.php";
        $result = monhoc_index();
        $title = $result['title'];
        $view = $result['view'];
        $list_monhoc = $result['data'];
        break;

    case 'monhoc-save':
        require_once "controller/giangvien/monhoc.controller.php";
        monhoc_save();
        exit;

    case 'monhoc-delete':
        require_once "controller/giangvien/monhoc.controller.php";
        monhoc_delete();
        exit;

    // --- QUẢN LÝ BÀI THI ---
    case 'quanly-baithi':
    case 'quanly-dethi':
        require_once "controller/giangvien/baithi.controller.php";

        // Gọi hàm index từ controller để lấy dữ liệu đã được lọc theo ID người dùng
        $result = baithi_index();

        $title = $result['title'];
        $view = $result['view'];
        $list_baithi = $result['data'];
        $list_monhoc = $result['list_monhoc'];
        break;

    case 'baithi-save':
        require_once "controller/giangvien/baithi.controller.php";
        baithi_save();
        exit;

    case 'baithi-delete':
        require_once "controller/giangvien/baithi.controller.php";
        baithi_delete();
        exit;

    // ========== QUẢN LÝ CÂU HỎI ==========
    case 'cauhoi-list':
        require_once "controller/giangvien/cauhoi.controller.php";
        break;

    case 'cauhoi-add':
        require_once "controller/giangvien/cauhoi.controller.php";
        break;

    case 'cauhoi-edit':
        require_once "controller/giangvien/cauhoi.controller.php";
        break;

    case 'cauhoi-delete':
        $_GET['act'] = 'cauhoi-delete';
        require_once "controller/giangvien/cauhoi.controller.php";
        break;

    // --- KẾT QUẢ THI ---
    case 'ketqua-thi':
        require_once "controller/giangvien/ketqua.controller.php";
        $result = ketqua_index();
        $title = $result['title'];
        $view = $result['view'];
        $data = $result['data'] ?? null;
        $baithi = $result['baithi'] ?? null;
        $id_baithi = $result['id_baithi'] ?? null;
        break;

    // --- GIÁM SÁT BẢO MẬT ---
    case 'quanly-logs':
        if ($user_role !== 'admin') {
            $title = "404 - Không tìm thấy";
            $view = "views/404.php";
            break;
        }
        require_once "controller/admin/logs.controller.php";
        $result = logs_index();
        $title = $result['title'];
        $view = $result['view'];
        $data = $result['data'];
        break;

    case 'quanly-goi-premium':
        if ($user_role !== 'admin') {
            $title = "404 - Không tìm thấy";
            $view = "views/404.php";
            break;
        }
        $title = "Quản lý Gói Premium";
        $view = "views/admin/goipremium/list.php";
        break;

    default:
        $title = "404 - Không tìm thấy";
        $view = "views/404.php";
        break;
}

// HIỂN THỊ GIAO DIỆN
if ($act == 'login' || $view == "views/404.php") {
    if (file_exists($view)) {
        include $view;
    } else {
        echo "Trang không tồn tại!";
    }
} else {
    include "views/layouts/header.php";
    if (isset($view) && file_exists($view)) {
        include $view;
    } else {
        echo "View không tồn tại: $view";
    }
    include "views/layouts/footer.php";
}
ob_end_flush();
