<div class="login-container">
    <div role="main">
        <div class="loginform">

            <div class="text-center mb-4">
                <div class="auth-icon-wrapper mb-3">
                    <i class="fa-solid fa-user-plus text-primary fs-2"></i>
                </div>
                <h2 class="login-heading fw-bold mb-1">Tạo tài khoản</h2>
                <p class="text-muted small">Tham gia PT QUIZ để luyện thi ngay hôm nay</p>
            </div>

            <form id="registerForm" class="login-form">

                <div class="mb-4">
                    <div class="input-group custom-input-group">
                        <span class="input-group-text border-0 bg-light text-muted px-3">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="fullname" class="form-control border-0 bg-light py-3" placeholder="Họ và tên" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="input-group custom-input-group">
                        <span class="input-group-text border-0 bg-light text-muted px-3">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control border-0 bg-light py-3" placeholder="Địa chỉ Email" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="input-group custom-input-group">
                        <span class="input-group-text border-0 bg-light text-muted px-3">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="regPasswordInput" class="form-control border-0 bg-light py-3" placeholder="Mật khẩu" required>
                        <button class="btn btn-light border-0 bg-light text-muted px-3" type="button" id="toggleRegPwdBtn">
                            <i class="fa-solid fa-eye" id="toggleRegPwdIcon"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm custom-btn" type="submit">
                    Đăng ký ngay
                </button>

                <!-- Link login -->
                <div class="text-center mt-4">
                    <span class="text-muted">Đã có tài khoản?</span>
                    <a href="index.php?act=dangnhap" class="text-primary fw-bold text-decoration-none ms-1">Đăng nhập</a>
                </div>

            </form>

            <script>
            document.addEventListener("DOMContentLoaded", function() {

                const form = document.getElementById("registerForm");
                if (!form) return;

                const toggleRegPwdBtn = document.getElementById('toggleRegPwdBtn');
                if (toggleRegPwdBtn) {
                    toggleRegPwdBtn.addEventListener('click', function() {
                        const pwdInput = document.getElementById('regPasswordInput');
                        const pwdIcon = document.getElementById('toggleRegPwdIcon');
                        if (pwdInput.type === 'password') {
                            pwdInput.type = 'text';
                            pwdIcon.classList.remove('fa-eye');
                            pwdIcon.classList.add('fa-eye-slash');
                        } else {
                            pwdInput.type = 'password';
                            pwdIcon.classList.remove('fa-eye-slash');
                            pwdIcon.classList.add('fa-eye');
                        }
                    });
                }

                form.addEventListener("submit", async function(e) {
                    e.preventDefault();

                    const data = {
                        fullname: form.fullname.value,
                        email: form.email.value,
                        password: form.password.value
                    };

                    try {
                        const response = await fetch(apiUrl("auth/register"), {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            alert(result.error);
                            return;
                        }

                        alert(result.message);
                        window.location.href = "index.php?act=dangnhap";

                    } catch (err) {
                        alert("Lỗi kết nối server");
                        console.error(err);
                    }
                });

            });
            </script>
        </div>
    </div>
</div>
