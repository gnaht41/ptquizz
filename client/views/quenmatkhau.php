<div class="login-container">
    <div role="main">
        <div class="loginform">
            <div class="text-center mb-4">
                <div class="auth-icon-wrapper mb-3">
                    <i class="fa-solid fa-key text-primary fs-2"></i>
                </div>
                <h2 class="login-heading fw-bold mb-1">Quên mật khẩu</h2>
                <p class="text-muted small" id="step-description">Nhập email để nhận mã xác thực OTP</p>
            </div>

            <!-- Step 1: Nhập Email -->
            <div id="step-1">
                <div class="mb-4">
                    <div class="input-group custom-input-group">
                        <span class="input-group-text border-0 bg-light text-muted px-3">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" id="email-input" class="form-control border-0 bg-light py-3" placeholder="Địa chỉ Email của bạn" required>
                    </div>
                </div>
                <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm custom-btn" onclick="sendOTP()">
                    Gửi mã OTP <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </div>

            <!-- Step 2: Nhập OTP -->
            <div id="step-2" style="display: none;">
                <div class="mb-4">
                    <div class="input-group custom-input-group">
                        <span class="input-group-text border-0 bg-light text-muted px-3">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input type="text" id="otp-input" class="form-control border-0 bg-light py-3 text-center fw-bold" placeholder="Nhập 6 số OTP" maxlength="6" style="letter-spacing: 5px; font-size: 1.2rem;">
                    </div>
                </div>
                <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm custom-btn" onclick="verifyOTP()">
                    Xác thực mã OTP <i class="fa-solid fa-check-double ms-2"></i>
                </button>
                <div class="text-center mt-3">
                    <button class="btn btn-link text-muted small text-decoration-none" onclick="resendOTP()">Gửi lại mã</button>
                </div>
            </div>



            <div class="text-center mt-4">
                <a href="index.php?act=dangnhap" class="text-primary fw-bold text-decoration-none small">
                    <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserEmail = '';
let currentOTP = '';

async function sendOTP() {
    const email = document.getElementById('email-input').value;
    if (!email) {
        alert('Vui lòng nhập email');
        return;
    }

    const btn = document.querySelector('#step-1 .btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang gửi...';

    try {
        const res = await fetch(apiUrl("auth/forgot-password"), {
            method: 'POST',
            body: JSON.stringify({ email })
        });
        const json = await res.json();

        if (json.success) {
            currentUserEmail = email;
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('step-2').style.display = 'block';
            document.getElementById('step-description').innerText = 'Mã OTP đã được gửi đến ' + email;
            alert(json.message);
        } else {
            alert(json.error);
        }
    } catch (e) {
        alert('Lỗi kết nối server');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Gửi mã OTP <i class="fa-solid fa-paper-plane ms-2"></i>';
    }
}

async function verifyOTP() {
    const otp = document.getElementById('otp-input').value;
    if (otp.length !== 6) {
        alert('Vui lòng nhập đủ 6 số OTP');
        return;
    }

    const btn = document.querySelector('#step-2 .btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xử lý...';

    try {
        // 1. Xác thực OTP và tự động reset về 123456
        const res = await fetch(apiUrl("auth/reset-password"), {
            method: 'POST',
            body: JSON.stringify({ 
                email: currentUserEmail, 
                otp: otp,
                password: '123456' // Reset mặc định về 123456
            })
        });
        const json = await res.json();

        if (json.success) {
            alert('Xác thực thành công! Mật khẩu của bạn đã được đặt lại thành: 123456');
            window.location.href = 'index.php?act=dangnhap';
        } else {
            alert(json.error);
        }
    } catch (e) {
        alert('Lỗi hệ thống');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Xác thực mã OTP <i class="fa-solid fa-check-double ms-2"></i>';
    }
}



function resendOTP() {
    sendOTP();
}
</script>
