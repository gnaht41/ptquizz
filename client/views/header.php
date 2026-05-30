<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT QUIZ</title>

    <link rel="stylesheet" href="public/css/style.css">

    <?php if (!empty($page_css)) : ?>
    <link rel="stylesheet" href="public/css/<?= $page_css ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="icon" type="image/jpg" href="public/img/ptstore-no-background.png">

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FONT AWESOME CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="topnav-container shadow-sm" style="position: relative; z-index: 1050;">
            <nav class="navbar navbar-expand-lg navbar-light bg-white">

                <div class="container">

                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php?act=trangchu">

                        <img src="public/img/ptstore.jpg" class="topnav-logo" alt="Logo">

                        <span class="brand-text">PT QUIZ</span>
                    </a>

                    <!-- Toggle mobile -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbar-collapse">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menu -->
                    <div class="collapse navbar-collapse justify-content-end" id="navbar-collapse">

                        <ul class="navbar-nav align-items-center">

                            <li class="nav-item">
                                <a class="nav-link" href="index.php?act=gioithieu">
                                    Giới thiệu
                                </a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0)"
                                    onclick="requireLogin('index.php?act=dethi')">Đề thi</a>
                            </li>


                            <?php if (isset($_SESSION['user'])): ?>
                            <!-- Premium Expiration Alert -->
                            <?php 
                            if ($_SESSION['user']['premium_status'] == 1 && !empty($_SESSION['user']['premium_expire'])) {
                                $expire = new DateTime($_SESSION['user']['premium_expire']);
                                $now = new DateTime();
                                $diff = $now->diff($expire);
                                $daysLeft = $diff->invert ? 0 : $diff->days;
                                
                                if ($daysLeft <= 2 && $daysLeft >= 0): ?>
                                    <li class="nav-item me-2">
                                        <div class="alert alert-warning py-1 px-2 mb-0 small animate__animated animate__pulse animate__infinite d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Gói Premium sắp hết hạn (<?= $daysLeft ?> ngày). Quý khách có muốn gia hạn không?</span>
                                            <a href="index.php?act=premium" class="btn btn-sm btn-warning ms-2" style="font-size:0.75rem; padding: 0.1rem 0.4rem;">Gia hạn ngay</a>
                                        </div>
                                    </li>
                                <?php endif;
                            } ?>

                            <li class="nav-item">
                                <a class="nav-link position-relative d-flex align-items-center" href="index.php?act=premium" style="gap: 5px;">
                                    <?php if ($_SESSION['user']['premium_status'] == 1): ?>
                                        <div class="premium-status-badge d-flex align-items-center bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill border border-warning" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-crown me-1 text-warning shadow-sm"></i>
                                            <span class="fw-bold">PREMIUM</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="free-status-badge d-flex align-items-center bg-light text-secondary px-2 py-1 rounded-pill border" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-bolt me-1 text-primary"></i>
                                            <span>Lượt còn lại: <strong class="text-primary"><?= max(0, 30 - ($_SESSION['user']['total_free_attempts'] ?? 0)) ?></strong></span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </li>

                            <li class="nav-item dropdown ms-3">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="position-relative">
                                        <img src="/server/public/imgs/avatars/<?= !empty($_SESSION['user']['avatar']) ? htmlspecialchars($_SESSION['user']['avatar']) : 'default.jpg' ?>" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid <?= $_SESSION['user']['premium_status'] == 1 ? '#ffc107' : '#e2e8f0' ?>;">
                                        <?php if ($_SESSION['user']['premium_status'] == 1): ?>
                                            <i class="fas fa-check-circle text-warning position-absolute" style="bottom: -2px; right: 5px; font-size: 0.8rem; background: white; border-radius: 50%;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <span class="fw-bold"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <?php if ($_SESSION['user']['premium_status'] == 1): ?>
                                        <li class="px-3 py-2 bg-light small">
                                            <div class="text-warning font-weight-bold"><i class="fas fa-crown mr-1"></i>Thành viên Premium</div>
                                            <div class="text-muted">Hết hạn: <?= date('d/m/Y', strtotime($_SESSION['user']['premium_expire'])) ?></div>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?act=thongtin">
                                            <i class="fa-solid fa-id-card me-2 text-primary"></i>Thông tin cá nhân
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?act=lichsu">
                                            <i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Lịch sử làm bài
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="index.php?act=premium">
                                            <i class="fa-solid fa-gem me-2 text-warning"></i>Nâng cấp Premium
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 text-danger" href="index.php?act=dangxuat"
                                            onclick="return logoutConfirm()">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php else: ?>

                            <li class="nav-item ms-2">
                                <a class="nav-link" href="index.php?act=dangky">
                                    Đăng ký
                                </a>
                            </li>

                            <li class="nav-item ms-2">
                                <a href="index.php?act=dangnhap" class="btn btn-primary px-4">
                                    Đăng nhập
                                </a>
                            </li>

                            <?php endif; ?>

                        </ul>

                    </div>

            </nav>
        </div>

        <!-- Login Prompt Modal -->
        <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-body text-center p-4 p-md-5">
                        <div class="mb-4">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-lock" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-3 text-dark">Yêu cầu đăng nhập</h4>
                        <p class="text-muted mb-4">Bạn phải đăng nhập tài khoản mới được tham gia làm bài thi. Vui lòng
                            đăng nhập để tiếp tục.</p>
                        <div class="d-flex flex-column gap-2">
                            <a href="index.php?act=dangnhap"
                                class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm custom-btn"><i
                                    class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập ngay</a>
                            <button type="button"
                                class="btn btn-light rounded-pill py-2 fw-bold text-muted transition-hover"
                                data-bs-dismiss="modal">Để sau</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;
        const isPremium = <?= (isset($_SESSION['user']['premium_status']) && $_SESSION['user']['premium_status'] == 1) ? 'true' : 'false' ?>;
        const remainingAttempts = <?= isset($_SESSION['user']['total_free_attempts']) ? max(0, 30 - $_SESSION['user']['total_free_attempts']) : 30 ?>;

        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            if (error === 'ongoing_mismatch') {
                alert("TRUY CẬP BỊ TỪ CHỐI!\n\nHệ thống ghi nhận bạn đang có một bài thi khác đang làm dở.\n\nBạn phải hoàn thành hoặc Hủy bài thi đó trước khi bắt đầu bài mới.");
            } else if (error === 'session_active') {
                alert("TRUY CẬP BỊ TỪ CHỐI!\n\nBài thi này hiện đang được ĐƯỢC MỞ ở một thiết bị hoặc trình duyệt khác.\n\nVui lòng đóng cửa sổ đang làm trước khi vào lại.");
            }
        });

        function apiUrl(route, params = {}) {
            const cleanRoute = String(route || '').replace(/^\/+|\/+$/g, '');
            const url = new URL(`api/${cleanRoute}`, window.location.href);

            Object.entries(params).forEach(([key, value]) => {
                if (value !== undefined && value !== null && value !== "") {
                    url.searchParams.set(key, value);
                }
            });

            return url.toString();
        }

        function logoutConfirm() {
            return confirm("Bạn chắc chắn muốn đăng xuất? Bài thi đang làm (nếu có) sẽ được bảo lưu.");
        }

        function requireLogin(url) {
            if (!isLoggedIn) {
                const loginModal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
                loginModal.show();
            } else {
                window.location.href = url;
            }
        }

        async function confirmLambai(id, isOngoing) {
            if (!isLoggedIn) {
                const loginModal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
                loginModal.show();
                return;
            }

            if (!isOngoing && !isPremium && remainingAttempts <= 0) {
                if (confirm("Lượt thi hôm nay của bạn đã hết (đã dùng 30 lượt)!\nNâng cấp Premium ngay để làm bài không giới hạn?")) {
                    window.location.href = 'index.php?act=premium';
                }
                return;
            }

            // CRITICAL SECURITY CHECK: Check for any ongoing or active exams
            try {
                const res = await fetch(apiUrl("exam/list"));
                const json = await res.json();
                if (json.success && json.data) {
                    const activeExam = json.data.find(row => parseInt(row.is_ongoing) === 1);
                    
                    if (activeExam) {
                        // 1. BLOCK if trying to start a DIFFERENT exam while one is ongoing
                        if (parseInt(activeExam.id_baithi) !== parseInt(id)) {
                            alert(`TRUY CẬP BỊ TỪ CHỐI!\n\nHệ thống ghi nhận bạn đang làm bài thi '${activeExam.ten_baithi}' dở dang.\n\nBạn phải hoàn thành hoặc Hủy bài thi đó trước khi bắt đầu bài mới.`);
                            return;
                        }
                        
                        // 2. BLOCK if the SAME exam is CURRENTLY active (Heartbeat < 30s)
                        if (parseInt(activeExam.is_active) === 1) {
                            alert(`TRUY CẬP BỊ TỪ CHỐI!\n\nHệ thống ghi nhận bài thi này hiện đang ĐƯỢC MỞ ở một thiết bị khác (App hoặc Web).\n\nBạn KHÔNG THỂ tham gia cùng lúc. Vui lòng đóng cửa sổ đang làm trước khi vào lại.`);
                            return;
                        }
                    }
                }
            } catch (e) {
                console.error("Lỗi kiểm tra session:", e);
            }

            const msg = isOngoing ? "Bạn có muốn tiếp tục làm bài thi này không?" :
                "Bạn có chắc chắn muốn bắt đầu làm bài thi này không?";
            if (confirm(msg)) {
                window.location.href = `index.php?act=lambai&id=${id}`;
            }
        }
        </script>
        <div class="main">