<?php
// 1. Phải có session_start() ở đầu file (nếu file index chưa có)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giả sử bạn đã include file Database và các Class cần thiết ở index.php
// Nếu chạy file này độc lập, hãy uncomment dòng dưới:
// include_once '../models/Database.php'; 

if (isset($_POST['btn_login'])) {
    $email = $_POST['email'];
    $passwordInput = $_POST['password'];

    $conn = Database::connect();

    // Tìm user theo email và trạng thái active
    $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE email = ? AND trangthai = 'active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Kiểm tra mật khẩu
        if (password_verify($passwordInput, $user['matkhau'])) {
            // Chỉ cho phép giangvien hoặc admin
            if ($user['vaitro'] == 'giangvien' || $user['vaitro'] == 'admin') {
                $_SESSION['user'] = [
                    'id_nguoidung' => $user['id_nguoidung'],
                    'ten' => $user['ten'],
                    'vaitro' => $user['vaitro'],
                    'avatar' => $user['avatar'] ?? 'default.jpg'
                ];
                header("Location: index.php?act=dashboard");
                exit;
            } else {
                $error = "Bạn không có quyền truy cập khu vực quản trị!";
            }
        } else {
            $error = "Mật khẩu không chính xác!";
        }
    } else {
        $error = "Email không tồn tại hoặc tài khoản đã bị khóa!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT QUIZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        .login-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
            border: 1px solid #e1e8ed;
        }

        .brand-logo {
            color: #0d6efd;
            /* Màu xanh biển chuẩn Bootstrap */
            font-size: 50px;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h4 {
            font-weight: 700;
            color: #212529;
            /* Màu đen xám */
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 13px;
        }

        .btn-login {
            background-color: #0d6efd;
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }

        .alert-custom {
            font-size: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="brand-logo">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="login-header">
            <h4>Hệ thống PT QUIZ</h4>
            <small class="text-muted">Hệ thống quản lý bài thi trực tuyến</small>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-custom border-0 shadow-sm">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-uppercase">Tài khoản Email</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="name@example.com" required
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-uppercase">Mật khẩu</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-white border-start-0" id="togglePassword" style="cursor: pointer;">
                        <i class="fas fa-eye text-muted" id="eyeIcon"></i>
                    </span>
                </div>
            </div>
            <button type="submit" name="btn_login" class="btn btn-primary w-100 btn-login shadow-sm">
                ĐĂNG NHẬP NGAY
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="mb-0 small text-muted">© 2026 Developed by <span class="fw-bold text-dark">PT QUIZ</span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>
