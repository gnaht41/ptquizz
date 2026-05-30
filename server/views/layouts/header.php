<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT QUIZ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/jpg" href="public/imgs/ptstore-no-background.png">

    <style>
        :root {
            --primary-blue: #0d6efd;
            --dark-text: #212529;
            --light-bg: #f8fafc;
            --sidebar-bg: #ffffff;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: var(--light-bg);
        }

        .top-nav {
            height: 65px;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #eef2f7;
        }

        .nav-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-user-area {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 5px 15px;
            border-radius: 50px;
            transition: 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: var(--dark-text);
        }

        .nav-user-area:hover {
            background: #f1f5f9;
        }

        .nav-user-trigger {
            border: none;
            background: transparent;
        }

        .nav-user-trigger::after {
            display: none;
        }

        .admin-dropdown {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 8px;
            min-width: 250px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .admin-dropdown .dropdown-item {
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 500;
            color: #334155;
        }

        .admin-dropdown .dropdown-item:hover {
            background: #eff6ff;
            color: var(--primary-blue);
        }

        .user-info-text {
            text-align: right;
            line-height: 1.2;
        }

        .user-name {
            display: block;
            font-weight: 700;
            font-size: 14px;
            color: #1e293b;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .main-wrapper {
            display: flex;
            margin-top: 65px;
            height: calc(100vh - 65px);
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid #eef2f7;
            padding-top: 20px;
            flex-shrink: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 15px;
        }

        .sidebar-menu li a {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #475569;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: 0.2s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            background: #eff6ff;
            color: var(--primary-blue);
        }

        .sidebar-menu li.active a i {
            color: var(--primary-blue);
        }

        .menu-label {
            padding: 20px 20px 10px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content-body {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <nav class="top-nav">
        <a href="index.php" class="nav-logo">
            <i class="fas fa-graduation-cap"></i> PT QUIZ ADMIN
        </a>

        <div class="dropdown">
            <button class="nav-user-area nav-user-trigger dropdown-toggle" type="button" id="serverUserDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-info-text d-none d-md-block">
                    <span class="user-name" id="headerUserName">Chào, <?php echo $_SESSION['user']['ten']; ?></span>
                    <span class="user-role"><?php echo ($_SESSION['user']['vaitro'] == 'admin') ? 'Quản trị viên' : 'Giảng viên'; ?></span>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1); overflow: hidden;">
                    <img id="headerUserAvatar" src="/project-tracnghiem/server/public/imgs/avatars/<?= htmlspecialchars($_SESSION['user']['avatar'] ?? 'default.jpg') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end admin-dropdown" aria-labelledby="serverUserDropdown">
                <li>
                    <a class="dropdown-item" href="index.php?act=profile">
                        <i class="fas fa-id-card me-2"></i>Thông tin cá nhân
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="index.php?act=dangxuat">
                        <i class="fas fa-right-from-bracket me-2"></i>Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-wrapper">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="<?php echo ($act == 'dashboard') ? 'active' : ''; ?>">
                    <a href="index.php?act=dashboard"><i class="fas fa-chart-pie"></i> Tổng quan</a>
                </li>

                <?php if ($_SESSION['user']['vaitro'] == 'giangvien'): ?>
                    <div class="menu-label">Giảng dạy</div>
                    <li class="<?php echo ($act == 'quanly-monhoc') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-monhoc"><i class="fas fa-book"></i> Môn học</a>
                    </li>
                    <li class="<?php echo ($act == 'quanly-nganhang-cauhoi') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-nganhang-cauhoi"><i class="fas fa-database"></i> Ngân hàng câu hỏi</a>
                    </li>
                    <li class="<?php echo ($act == 'quanly-dethi') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-dethi"><i class="fas fa-copy"></i> Đề thi & Bài tập</a>
                    </li>
                    <div class="menu-label">Thống kê & Báo cáo</div>
                    <li class="<?php echo ($act == 'ketqua-thi') ? 'active' : ''; ?>">
                        <a href="index.php?act=ketqua-thi"><i class="fas fa-chart-line"></i> Kết quả thi</a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['user']['vaitro'] == 'admin'): ?>
                    <div class="menu-label">Hệ thống</div>
                    <li class="<?php echo ($act == 'quanly-nguoidung') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-nguoidung"><i class="fas fa-user-cog"></i> Người dùng</a>
                    </li>
                    <li class="<?php echo ($act == 'quanly-logs') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-logs"><i class="fas fa-shield-halved"></i> Giám sát Bảo mật</a>
                    </li>
                    <li class="<?php echo ($act == 'quanly-goi-premium') ? 'active' : ''; ?>">
                        <a href="index.php?act=quanly-goi-premium"><i class="fas fa-crown"></i> Gói Premium</a>
                    </li>
                <?php endif; ?>

            </ul>
        </aside>

        <main class="content-body">
            <script>
            function serverApiUrl(route, params = {}) {
                const cleanRoute = String(route || '').replace(/^\/+|\/+$/g, '');
                const url = new URL(`api/${cleanRoute}`, window.location.href);

                Object.entries(params).forEach(([key, value]) => {
                    if (value !== undefined && value !== null && value !== "") {
                        url.searchParams.set(key, value);
                    }
                });

                return url.toString();
            }
            </script>