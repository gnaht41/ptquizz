<div id="userAlert"></div>

<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
    <div>
        <h2 style="margin:0;color:#0f172a;font-weight:700;">Quản lý người dùng</h2>
        <p style="margin:6px 0 0;color:#64748b;font-size:14px;">Quản lý và phân quyền tài khoản `thí sinh` và `giảng viên` qua API bảo mật.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openUserModal()" style="border-radius:10px; padding:10px 20px; font-weight:600;">
        <i class="fas fa-user-plus me-2"></i>Thêm người dùng
    </button>
</div>

<!-- BỘ LỌC ĐỒNG NHẤT -->
<div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
    <form id="userFilterForm" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Vai trò</label>
            <select class="form-select" id="filterRole" name="vaitro" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; min-width: 150px;">
                <option value="">Tất cả</option>
                <option value="thisinh">Thí sinh</option>
                <option value="giangvien">Giảng viên</option>
            </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px;">
            <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Trạng thái</label>
            <select class="form-select" id="filterStatus" name="trangthai" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; min-width: 150px;">
                <option value="">Tất cả</option>
                <option value="active">Đang hoạt động</option>
                <option value="inactive">Đã khóa</option>
            </select>
        </div>

        <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
            <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Tìm kiếm</label>
            <input class="form-control" type="text" id="filterKeyword" name="keyword" placeholder="Nhập tên hoặc email..." style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="padding: 9px 20px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                <i class="fas fa-filter me-1"></i> Lọc
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()" style="padding: 9px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; background:white;">
                Làm mới
            </button>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm mt-4" style="border-radius:16px;">
    <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
            <div>
                <h5 style="margin:0;">Danh sách tài khoản</h5>
                <small class="text-muted" id="userCountLabel">Đang tải dữ liệu...</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Đang tải danh sách người dùng...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" style="display:none;position:fixed;z-index:10000;inset:0;background:rgba(15,23,42,0.55);align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(2px);">
    <div style="width:100%;max-width:760px;">
        <div style="background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 24px 80px rgba(15,23,42,0.28);border:1px solid rgba(226,232,240,0.95);">
            <form id="userForm">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:22px 24px 0;">
                    <div>
                        <h4 class="modal-title" id="userModalTitle">Thêm người dùng</h4>
                        <small class="text-muted">Chỉ tạo và quản lý tài khoản thí sinh, giảng viên.</small>
                    </div>
                    <button type="button" class="btn-close" onclick="closeUserModal()" aria-label="Close"></button>
                </div>
                <div style="padding:18px 24px 8px;">
                    <input type="hidden" id="userId" name="id_nguoidung">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ tên</label>
                            <input type="text" class="form-control" id="userName" name="ten" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="userEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vai trò</label>
                            <select class="form-select" id="userRole" name="vaitro" required>
                                <option value="thisinh">Thí sinh</option>
                                <option value="giangvien">Giảng viên</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select class="form-select" id="userStatus" name="trangthai" required>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Đã khóa</option>
                            </select>
                        </div>
                        <div class="col-12" id="passwordFieldContainer">
                            <label class="form-label fw-semibold">Mật khẩu</label>
                            <input type="password" class="form-control" id="userPassword" name="matkhau" placeholder="Ít nhất 6 ký tự">
                            <small class="text-muted" id="userPasswordHint">Bắt buộc khi tạo mới.</small>
                        </div>
                        <div class="col-12" id="resetPasswordContainer" style="display:none;">
                            <div class="form-check form-switch p-3 border rounded" style="background: #f8fafc;">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="resetPasswordToggle">
                                <label class="form-check-label fw-semibold" for="resetPasswordToggle">
                                    Reset mật khẩu về <code style="color: #e11d48; background: #fff1f2; padding: 2px 6px; border-radius: 4px;">User@123456</code>
                                </label>
                                <div class="text-muted small mt-1">Admin không thể xem mật khẩu hiện tại, chỉ có thể đặt lại về mật khẩu mặc định.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:12px;padding:8px 24px 24px;">
                    <button type="button" class="btn btn-light" onclick="closeUserModal()">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Lưu người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const userModal = document.getElementById('userModal');
const userTableBody = document.getElementById('userTableBody');
const userCountLabel = document.getElementById('userCountLabel');
const userForm = document.getElementById('userForm');
const userPasswordHint = document.getElementById('userPasswordHint');
let users = [];

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function roleLabel(role) {
    return role === 'giangvien' ? 'Giảng viên' : 'Thí sinh';
}

function statusBadge(status) {
    if (status === 'inactive') {
        return '<span class="badge text-bg-secondary">Đã khóa</span>';
    }
    return '<span class="badge text-bg-success">Đang hoạt động</span>';
}

function formatDate(value) {
    if (!value) return '---';
    const date = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return escapeHtml(value);
    return date.toLocaleString('vi-VN');
}

function showAlert(message, type = 'success') {
    const el = document.getElementById('userAlert');
    el.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:14px;">
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

function renderUsers() {
    if (!users.length) {
        userTableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Chưa có người dùng phù hợp bộ lọc.</td></tr>';
        userCountLabel.textContent = '0 tài khoản';
        return;
    }

    userCountLabel.textContent = `${users.length} tài khoản`;
    userTableBody.innerHTML = users.map((user, index) => {
        const actionButton = user.trangthai === 'inactive'
            ? `<button class="btn btn-sm btn-outline-success btn-restore-user" data-id="${Number(user.id_nguoidung)}"><i class="fas fa-rotate-left me-1"></i>Mở lại</button>`
            : `<button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="${Number(user.id_nguoidung)}"><i class="fas fa-user-slash me-1"></i>Khóa mềm</button>`;

        return `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <div class="fw-semibold">${escapeHtml(user.ten)}</div>
                </td>
                <td>${escapeHtml(user.email)}</td>
                <td>${escapeHtml(roleLabel(user.vaitro))}</td>
                <td>${statusBadge(user.trangthai)}</td>
                <td>${escapeHtml(formatDate(user.ngaytao))}</td>
                <td class="text-end">
                    <div style="display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;">
                        <button class="btn btn-sm btn-outline-primary btn-edit-user" data-id="${Number(user.id_nguoidung)}">
                            <i class="fas fa-pen me-1"></i>Sửa
                        </button>
                        ${actionButton}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

async function loadUsers() {
    userTableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Đang tải danh sách người dùng...</td></tr>';
    const params = {
        vaitro: document.getElementById('filterRole').value,
        trangthai: document.getElementById('filterStatus').value,
        keyword: document.getElementById('filterKeyword').value.trim()
    };

    try {
        const res = await fetch(serverApiUrl('nguoidung/list', params));
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể tải danh sách người dùng');
        }
        users = Array.isArray(json.data) ? json.data : [];
        renderUsers();
    } catch (error) {
        userTableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${escapeHtml(error.message)}</td></tr>`;
        userCountLabel.textContent = 'Lỗi tải dữ liệu';
    }
}

function openUserModal(id = 0) {
    userForm.reset();
    document.getElementById('userId').value = '';
    document.getElementById('userStatus').value = 'active';
    document.getElementById('userRole').value = 'thisinh';
    document.getElementById('userModalTitle').textContent = 'Thêm người dùng';
    userPasswordHint.textContent = 'Bắt buộc khi tạo mới.';
    document.getElementById('userPassword').required = true;

    if (id) {
        const user = users.find((item) => Number(item.id_nguoidung) === Number(id));
        if (!user) return;

        document.getElementById('userModalTitle').textContent = 'Cập nhật người dùng';
        document.getElementById('userId').value = user.id_nguoidung;
        document.getElementById('userName').value = user.ten || '';
        document.getElementById('userEmail').value = user.email || '';
        document.getElementById('userEmail').readOnly = true; // Không cho sửa email
        document.getElementById('userRole').value = user.vaitro || 'thisinh';
        document.getElementById('userStatus').value = user.trangthai || 'active';
        
        // Ẩn ô mật khẩu, hiện ô reset
        document.getElementById('passwordFieldContainer').style.display = 'none';
        document.getElementById('resetPasswordContainer').style.display = 'block';
        document.getElementById('resetPasswordToggle').checked = false;
        document.getElementById('userPassword').required = false;
    } else {
        document.getElementById('userEmail').readOnly = false;
        document.getElementById('passwordFieldContainer').style.display = 'block';
        document.getElementById('resetPasswordContainer').style.display = 'none';
        document.getElementById('userPassword').required = true;
    }

    userModal.style.display = 'flex';
}

function closeUserModal() {
    userModal.style.display = 'none';
}

function resetFilters() {
    document.getElementById('userFilterForm').reset();
    loadUsers();
}

async function saveUser(payload) {
    const method = payload.id_nguoidung ? 'PATCH' : 'POST';
    const res = await fetch(serverApiUrl('nguoidung/save'), {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.error || 'Không thể lưu người dùng');
    }
    return json;
}

async function softDeleteUser(id) {
    const res = await fetch(serverApiUrl('nguoidung/delete'), {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_nguoidung: id })
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.error || 'Không thể khóa tài khoản');
    }
    return json;
}

document.getElementById('userFilterForm').addEventListener('submit', function(event) {
    event.preventDefault();
    loadUsers();
});

userForm.addEventListener('submit', async function(event) {
    event.preventDefault();

    const payload = {
        id_nguoidung: Number(document.getElementById('userId').value || 0),
        ten: document.getElementById('userName').value.trim(),
        email: document.getElementById('userEmail').value.trim(),
        vaitro: document.getElementById('userRole').value,
        trangthai: document.getElementById('userStatus').value,
        matkhau: document.getElementById('userPassword').value,
        reset_pwd: document.getElementById('resetPasswordToggle').checked
    };

    try {
        const json = await saveUser(payload);
        closeUserModal();
        showAlert(json.message || 'Lưu người dùng thành công');
        await loadUsers();
    } catch (error) {
        showAlert(error.message, 'danger');
    }
});

userTableBody.addEventListener('click', async function(event) {
    const editBtn = event.target.closest('.btn-edit-user');
    if (editBtn) {
        openUserModal(Number(editBtn.dataset.id));
        return;
    }

    const deleteBtn = event.target.closest('.btn-delete-user');
    if (deleteBtn) {
        const id = Number(deleteBtn.dataset.id);
        if (!confirm('Bạn có chắc muốn khóa mềm tài khoản này không?')) {
            return;
        }

        try {
            const json = await softDeleteUser(id);
            showAlert(json.message || 'Khóa tài khoản thành công');
            await loadUsers();
        } catch (error) {
            showAlert(error.message, 'danger');
        }
        return;
    }

    const restoreBtn = event.target.closest('.btn-restore-user');
    if (restoreBtn) {
        const id = Number(restoreBtn.dataset.id);
        const user = users.find((item) => Number(item.id_nguoidung) === id);
        if (!user) return;

        try {
            const json = await saveUser({
                id_nguoidung: id,
                ten: user.ten,
                email: user.email,
                vaitro: user.vaitro,
                trangthai: 'active',
                matkhau: ''
            });
            showAlert(json.message || 'Mở lại tài khoản thành công');
            await loadUsers();
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }
});

window.addEventListener('click', function(event) {
    if (event.target === userModal) {
        closeUserModal();
    }
});

loadUsers();
</script>
