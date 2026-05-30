<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-8">
            <div id="profileAlert"></div>

            <div class="card border-0 shadow-sm" style="border-radius:20px;overflow:hidden;">
                <div style="padding:28px 30px;background:linear-gradient(135deg,#eff6ff 0%,#f8fafc 100%);border-bottom:1px solid #e2e8f0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                        <div>
                            <h2 style="margin:0;color:#0f172a;">Thông tin cá nhân</h2>
                            <p style="margin:8px 0 0;color:#64748b;">Cập nhật hồ sơ đăng nhập của bạn bằng API an toàn theo session hiện tại.</p>
                        </div>
                        <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:28px;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding:30px;">
                    <div id="profileLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>

                    <form id="profileForm" style="display:none;" enctype="multipart/form-data">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="/project-tracnghiem/server/public/imgs/avatars/default.jpg" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                                <label for="profileAvatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle shadow" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid white; transition: all 0.2s;">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="profileAvatar" name="avatar" class="d-none" accept="image/png, image/jpeg, image/gif, image/jpg">
                            </div>
                            <div class="mt-2 small text-muted">Định dạng hỗ trợ: JPG, PNG, GIF. Tối đa 2MB.</div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ tên</label>
                                <input type="text" class="form-control" id="profileName" name="ten" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="profileEmail" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò</label>
                                <input type="text" class="form-control" id="profileRole" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <input type="text" class="form-control" id="profileStatus" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngày tạo</label>
                                <input type="text" class="form-control" id="profileCreatedAt" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="profilePassword" name="matkhau" placeholder="Để trống nếu giữ nguyên">
                            </div>
                        </div>

                        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:28px;flex-wrap:wrap;">
                            <a href="index.php?act=dashboard" class="btn btn-light">Quay lại</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function profileRoleLabel(role) {
    return role === 'admin' ? 'Quản trị viên' : 'Giảng viên';
}

function profileStatusLabel(status) {
    return status === 'active' ? 'Đang hoạt động' : 'Đã khóa';
}

function formatProfileDate(value) {
    if (!value) return '---';
    const date = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString('vi-VN');
}

function showProfileAlert(message, type = 'success') {
    document.getElementById('profileAlert').innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:14px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

async function loadProfile() {
    try {
        const res = await fetch(serverApiUrl('profile/detail'));
        const json = await res.json();

        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể tải thông tin cá nhân');
        }

        const user = json.data || {};
        
        if (user.avatar) {
            document.getElementById('avatarPreview').src = `/project-tracnghiem/server/public/imgs/avatars/${user.avatar}`;
        }
        
        document.getElementById('profileName').value = user.ten || '';
        document.getElementById('profileEmail').value = user.email || '';
        document.getElementById('profileRole').value = profileRoleLabel(user.vaitro || '');
        document.getElementById('profileStatus').value = profileStatusLabel(user.trangthai || '');
        document.getElementById('profileCreatedAt').value = formatProfileDate(user.ngaytao || '');

        document.getElementById('profileLoading').style.display = 'none';
        document.getElementById('profileForm').style.display = 'block';
    } catch (error) {
        document.getElementById('profileLoading').innerHTML = `<div class="text-danger">${error.message}</div>`;
    }
}

document.getElementById('profileAvatar').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById('profileForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData();
    formData.append('ten', document.getElementById('profileName').value.trim());
    formData.append('email', document.getElementById('profileEmail').value.trim());
    formData.append('matkhau', document.getElementById('profilePassword').value);
    
    const fileInput = document.getElementById('profileAvatar');
    if (fileInput.files.length > 0) {
        formData.append('avatar', fileInput.files[0]);
    }

    try {
        const res = await fetch(serverApiUrl('profile/update'), {
            method: 'POST',
            body: formData
        });
        const json = await res.json();

        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể cập nhật thông tin cá nhân');
        }

        showProfileAlert(json.message || 'Cập nhật thành công');
        document.getElementById('profilePassword').value = '';
        
        // Update header in real-time
        const newName = document.getElementById('profileName').value.trim();
        document.getElementById('headerUserName').innerText = `Chào, ${newName}`;
        if (json.avatar) {
            document.getElementById('headerUserAvatar').src = `/project-tracnghiem/server/public/imgs/avatars/${json.avatar}`;
        }

        await loadProfile();
    } catch (error) {
        showProfileAlert(error.message, 'danger');
    }
});

loadProfile();
</script>
