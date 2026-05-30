<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "core/Database.php";

// Check if logged in user is still active
if (isset($_SESSION['user'])) {
    $conn = Database::connect();
    $stmt = $conn->prepare("SELECT trangthai FROM nguoidung WHERE id_nguoidung = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userStatus = $result->fetch_assoc();
    // $conn->close();

    if (!$userStatus || $userStatus['trangthai'] !== 'active') {
        session_destroy();
        header("Location: index.php?act=dangnhap&error=account_locked");
        exit;
    }
}

$act = $_GET['act'] ?? 'trangchu';

switch ($act) {

    case 'dangky':
        $title = "Đăng ký - PT QUIZ";
        $page_css = "dangnhap-dangky.css";
        $view = "views/dangky.php";
        break;

    case 'dangnhap':
        $title = "Đăng nhập - PT QUIZ";
        $page_css = "dangnhap-dangky.css";
        $view = "views/dangnhap.php";
        break;

    case 'quenmatkhau':
        $title = "Quên mật khẩu - PT QUIZ";
        $page_css = "dangnhap-dangky.css";
        $view = "views/quenmatkhau.php";
        break;

    case 'gioithieu':
        $title = "Giới thiệu - PT QUIZ";
        $page_css = "gioithieu-dangky.css";
        $view = "views/gioithieu.php";
        break;

    case 'dethi':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $title = "Đề thi - PT QUIZ";
        $page_css = "trangchu.css";
        $view = "views/dethi.php";
        break;

    case 'lambai':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $id_baithi = (int)($_GET['id'] ?? 0);
        $user_id = $_SESSION['user']['id'];
        
        // SERVER-SIDE SECURITY: Prevent multiple different exams at once
        $conn = Database::connect();
        $sql = "SELECT id_baithi FROM lanthi WHERE id_nguoidung = ? AND trangthai = 'ongoing' LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $ongoing = $stmt->get_result()->fetch_assoc();
        
        if ($ongoing) {
            $ongoing_id = (int)$ongoing['id_baithi'];
            
            // Block if trying to start a DIFFERENT exam while one is ongoing
            if ($ongoing_id !== $id_baithi) {
                header("Location: index.php?act=dethi&error=ongoing_mismatch");
                exit;
            }
        }

        // PREMIUM FEATURE: Daily attempt limit for free users
        if (!isset($_SESSION['user']['premium_status']) || $_SESSION['user']['premium_status'] == 0) {
            $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM lanthi WHERE id_nguoidung = ? AND DATE(thoigianbatdau) = CURDATE()");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $attemptCount = $stmt->get_result()->fetch_assoc()['attempts'];
            
            if ($attemptCount >= 30) {
                header("Location: index.php?act=premium&error=limit_reached");
                exit;
            }
        }

        $title = "Làm bài - PT QUIZ";
        $page_css = "lambai.css";
        $view = "views/lambai.php";
        break;

    case 'ketqua':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $title = "Kết quả - PT QUIZ";
        $page_css = "dethi.css";
        $view = "views/ketqua.php";
        break;

    case 'dangxuat':
        session_destroy();
        header("Location: index.php");
        exit;

    case 'thongtin':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $title = "Thông tin cá nhân - PT QUIZ";
        $view = "views/thongtin.php";
        break;

    case 'lichsu':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $title = "Lịch sử làm bài - PT QUIZ";
        $view = "views/lichsu.php";
        break;

    case 'premium':
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?act=dangnhap");
            exit;
        }
        $title = "Nâng cấp Premium - PT QUIZ";
        $view = "views/premium.php";
        break;

    default:
        $title = "Trang chủ - PT QUIZ";
        $page_css = "trangchu.css";
        $view = "views/trangchu.php";
}

// Fetch latest premium info for session
if (isset($_SESSION['user'])) {
    $conn = Database::connect();
    $stmt = $conn->prepare("SELECT premium_status, premium_expire FROM nguoidung WHERE id_nguoidung = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $premiumInfo = $stmt->get_result()->fetch_assoc();
    if ($premiumInfo) {
        $premium_status = $premiumInfo['premium_status'];
        $premium_expire = $premiumInfo['premium_expire'];

        // Tự động vô hiệu hóa nếu gói premium đã hết hạn
        if ($premium_status == 1 && !empty($premium_expire)) {
            $expireDate = new DateTime($premium_expire);
            $now = new DateTime();
            if ($expireDate < $now) {
                $premium_status = 0;
                $premium_expire = null;
                // Cập nhật lại vào Database để hạ cấp người dùng về tài khoản thường
                $updateStmt = $conn->prepare("UPDATE nguoidung SET premium_status = 0, premium_expire = NULL WHERE id_nguoidung = ?");
                $updateStmt->bind_param("i", $_SESSION['user']['id']);
                $updateStmt->execute();
            }
        }

        $_SESSION['user']['premium_status'] = $premium_status;
        $_SESSION['user']['premium_expire'] = $premium_expire;
        
        // Tính số lượt làm bài thực tế trong ngày hôm nay
        $stmt_attempts = $conn->prepare("SELECT COUNT(*) as attempts FROM lanthi WHERE id_nguoidung = ? AND DATE(thoigianbatdau) = CURDATE()");
        $stmt_attempts->bind_param("i", $_SESSION['user']['id']);
        $stmt_attempts->execute();
        $_SESSION['user']['total_free_attempts'] = $stmt_attempts->get_result()->fetch_assoc()['attempts'];
    }
}

include "views/header.php";
include $view;
include "views/footer.php";
