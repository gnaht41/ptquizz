<?php
// Giao diện quản lý gói Premium
?>
<div id="premiumAlert"></div>

<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
    <div>
        <h2 style="margin:0;color:#0f172a;">Quản lý Gói Premium</h2>
        <p style="margin:6px 0 0;color:#64748b;">Quản lý giá cả, thời hạn và trạng thái các gói nâng cấp thành viên.</p>
    </div>
    <div>
        <button type="button" onclick="openPremiumModal()"
            style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 10px 24px; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
            <i class="fas fa-plus"></i> Thêm gói mới
        </button>
    </div>
</div>

<div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;flex-wrap:wrap;">
    <button type="button" id="premiumActiveTab" onclick="switchPremiumStatus('active')"
        style="border:1px solid #bfdbfe;background:#eff6ff;color:#1d4ed8;padding:9px 16px;border-radius:10px;font-weight:700;cursor:pointer;">
        <i class="fas fa-check-circle me-1"></i> Đang bán
    </button>
    <button type="button" id="premiumTrashTab" onclick="switchPremiumStatus('inactive')"
        style="border:1px solid #e2e8f0;background:#fff;color:#64748b;padding:9px 16px;border-radius:10px;font-weight:700;cursor:pointer;">
        <i class="fas fa-trash-restore me-1"></i> Thùng rác
    </button>
</div>

<div class="card border-0 shadow-sm" style="border-radius:18px; overflow:hidden;">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="vertical-align: middle;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:16px 24px; color:#64748b; font-weight:600; font-size:13px; text-transform:uppercase;">STT</th>
                    <th style="padding:16px 24px; color:#64748b; font-weight:600; font-size:13px; text-transform:uppercase;">Tên gói</th>
                    <th style="padding:16px 24px; color:#64748b; font-weight:600; font-size:13px; text-transform:uppercase;">Giá (VND)</th>
                    <th style="padding:16px 24px; color:#64748b; font-weight:600; font-size:13px; text-transform:uppercase;">Thời hạn (ngày)</th>
                    <th style="padding:16px 24px; color:#64748b; font-weight:600; font-size:13px; text-transform:uppercase; text-align:right;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="premiumTableBody"></tbody>
        </table>
    </div>
</div>

<div id="premiumModal" style="display:none;position:fixed;z-index:10000;inset:0;background:rgba(15,23,42,0.6);align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px);">
    <div style="width:100%; max-width:480px; background:#fff; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.2); overflow:hidden;">
        <form id="premiumForm">
            <div style="padding:30px 30px 20px;">
                <h4 id="premiumModalTitle" style="margin:0 0 8px; color:#1e293b; font-weight:700;">Thêm gói Premium</h4>
                <p style="color:#64748b; font-size:14px; margin:0;">Thiết lập thông tin hiển thị và giá cho gói.</p>
            </div>

            <div style="padding:0 30px 30px;">
                <input type="hidden" id="premiumId">
                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569; font-size:14px;">Tên gói</label>
                    <input type="text" id="premiumName" required placeholder="VD: Premium Tháng"
                        style="width:100%; padding:12px 16px; border-radius:10px; border:1px solid #e2e8f0; outline:none; transition:all 0.2s;">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569; font-size:14px;">Giá (VNĐ)</label>
                        <input type="number" id="premiumPrice" required placeholder="45000"
                            style="width:100%; padding:12px 16px; border-radius:10px; border:1px solid #e2e8f0; outline:none;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569; font-size:14px;">Thời hạn (ngày)</label>
                        <input type="number" id="premiumDuration" required placeholder="30"
                            style="width:100%; padding:12px 16px; border-radius:10px; border:1px solid #e2e8f0; outline:none;">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#475569; font-size:14px;">Miêu tả</label>
                    <textarea id="premiumDesc" rows="3" required placeholder="VD: Làm trắc nghiệm không giới hạn. Xem lời giải chi tiết."
                        style="width:100%; padding:12px 16px; border-radius:10px; border:1px solid #e2e8f0; outline:none; font-family:inherit;"></textarea>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:10px;">
                    <button type="button" onclick="closePremiumModal()"
                        style="background:#f1f5f9; color:#64748b; font-weight:600; padding:12px 24px; border-radius:10px; border:none; cursor:pointer;">Hủy</button>
                    <button type="submit"
                        style="background:#3b82f6; color:white; font-weight:700; padding:12px 30px; border-radius:10px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(59, 130, 246, 0.2);">
                        Lưu gói
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let premiumPackages = [];
    let currentPremiumStatus = 'active';

    function showPremiumAlert(message, type = 'success') {
        const bg = type === 'success' ? '#dcfce7' : '#fee2e2';
        const color = type === 'success' ? '#166534' : '#991b1b';
        const border = type === 'success' ? '#bbf7d0' : '#fecaca';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const alertBox = document.getElementById('premiumAlert');
        alertBox.innerHTML = `
            <div style="background:${bg}; color:${color}; border:1px solid ${border}; padding:16px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:12px; animation: slideDown 0.3s ease;">
                <i class="fas ${icon}"></i>
                <span style="font-weight:500;">${message}</span>
            </div>
        `;
        setTimeout(() => alertBox.innerHTML = '', 5000);
    }

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }

    function setTabStyle(status) {
        const activeTab = document.getElementById('premiumActiveTab');
        const trashTab = document.getElementById('premiumTrashTab');
        const activeStyle = 'border:1px solid #bfdbfe;background:#eff6ff;color:#1d4ed8;padding:9px 16px;border-radius:10px;font-weight:700;cursor:pointer;';
        const normalStyle = 'border:1px solid #e2e8f0;background:#fff;color:#64748b;padding:9px 16px;border-radius:10px;font-weight:700;cursor:pointer;';
        activeTab.style.cssText = status === 'active' ? activeStyle : normalStyle;
        trashTab.style.cssText = status === 'inactive' ? activeStyle : normalStyle;
    }

    function switchPremiumStatus(status) {
        currentPremiumStatus = status;
        setTabStyle(status);
        loadPremiumPackages();
    }

    async function loadPremiumPackages() {
        try {
            const res = await fetch(serverApiUrl('admin/goipremium', { status: currentPremiumStatus }));
            const json = await res.json();
            if (json.success) {
                premiumPackages = json.data || [];
                renderPremiumTable();
            } else {
                showPremiumAlert(json.message || json.error || 'Không thể tải danh sách gói premium', 'danger');
            }
        } catch (error) {
            showPremiumAlert('Lỗi kết nối server khi tải danh sách gói premium', 'danger');
        }
    }

    function renderPremiumTable() {
        const tbody = document.getElementById('premiumTableBody');
        if (premiumPackages.length === 0) {
            const emptyText = currentPremiumStatus === 'inactive'
                ? 'Thùng rác chưa có gói premium nào.'
                : 'Chưa có gói premium đang bán.';
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#64748b;">${emptyText}</td></tr>`;
            return;
        }

        tbody.innerHTML = premiumPackages.map((pkg, index) => {
            const activeActions = `
                <button onclick="editPremiumPackage(${Number(pkg.id_goi)})" title="Sửa" class="premium-icon-btn" style="color:#6366f1;">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="copyPremiumPackage(${Number(pkg.id_goi)})" title="Sao chép" class="premium-icon-btn" style="color:#10b981;">
                    <i class="fas fa-copy"></i>
                </button>
                <button onclick="deletePremiumPackage(${Number(pkg.id_goi)})" title="Xóa mềm" class="premium-icon-btn premium-danger-btn">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            const trashActions = `
                <button onclick="restorePremiumPackage(${Number(pkg.id_goi)})" title="Khôi phục" class="premium-icon-btn" style="color:#16a34a;border-color:#bbf7d0;">
                    <i class="fas fa-trash-restore"></i>
                </button>
            `;

            return `
                <tr>
                    <td style="padding:20px 24px; color:#64748b; font-weight:500;">${index + 1}</td>
                    <td style="padding:20px 24px;">
                        <div style="font-weight:600; color:#1e293b;">${pkg.ten_goi}</div>
                        <div style="font-size:12px; color:#94a3b8; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${pkg.mieuta || ''}</div>
                    </td>
                    <td style="padding:20px 24px; color:#1e293b; font-weight:700;">${formatVND(pkg.gia)}</td>
                    <td style="padding:20px 24px; color:#475569;">
                        <span style="background:#f1f5f9; padding:4px 12px; border-radius:6px; font-size:13px; font-weight:600;">${pkg.thoihan_ngay} ngày</span>
                    </td>
                    <td style="padding:20px 24px; text-align:right;">
                        <div style="display:flex; justify-content:flex-end; gap:8px;">
                            ${currentPremiumStatus === 'active' ? activeActions : trashActions}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function openPremiumModal(id = 0) {
        document.getElementById('premiumForm').reset();
        document.getElementById('premiumId').value = '';
        document.getElementById('premiumModalTitle').textContent = 'Thêm gói Premium';

        if (id > 0) {
            const pkg = premiumPackages.find(p => Number(p.id_goi) === Number(id));
            if (pkg) {
                document.getElementById('premiumModalTitle').textContent = 'Cập nhật gói Premium';
                document.getElementById('premiumId').value = pkg.id_goi;
                document.getElementById('premiumName').value = pkg.ten_goi;
                document.getElementById('premiumPrice').value = pkg.gia;
                document.getElementById('premiumDuration').value = pkg.thoihan_ngay;
                document.getElementById('premiumDesc').value = pkg.mieuta || '';
            }
        }
        document.getElementById('premiumModal').style.display = 'flex';
    }

    function closePremiumModal() {
        document.getElementById('premiumModal').style.display = 'none';
    }

    document.getElementById('premiumForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            id_goi: document.getElementById('premiumId').value,
            ten_goi: document.getElementById('premiumName').value,
            gia: document.getElementById('premiumPrice').value,
            thoihan_ngay: document.getElementById('premiumDuration').value,
            mieuta: document.getElementById('premiumDesc').value
        };

        const method = payload.id_goi ? 'PATCH' : 'POST';
        try {
            const res = await fetch(serverApiUrl('admin/goipremium'), {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (json.success) {
                showPremiumAlert(json.message);
                closePremiumModal();
                switchPremiumStatus('active');
            } else {
                showPremiumAlert(json.message || json.error || 'Không thể lưu gói premium', 'danger');
            }
        } catch (error) {
            showPremiumAlert('Lỗi kết nối server khi lưu gói premium', 'danger');
        }
    });

    async function copyPremiumPackage(id) {
        if (!confirm('Bạn có muốn sao chép gói này không?')) return;
        try {
            const res = await fetch(serverApiUrl('admin/goipremium'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ copy_id: id })
            });
            const json = await res.json();
            if (json.success) {
                showPremiumAlert(json.message);
                loadPremiumPackages();
            } else {
                showPremiumAlert(json.message || json.error || 'Không thể sao chép gói premium', 'danger');
            }
        } catch (error) {
            showPremiumAlert('Lỗi kết nối server khi sao chép gói premium', 'danger');
        }
    }

    function editPremiumPackage(id) {
        openPremiumModal(id);
    }

    async function deletePremiumPackage(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa gói này không? Gói sẽ được chuyển vào thùng rác và có thể khôi phục lại.')) return;
        try {
            const res = await fetch(serverApiUrl('admin/goipremium'), {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_goi: id })
            });
            const json = await res.json();
            if (json.success) {
                showPremiumAlert(json.message);
                loadPremiumPackages();
            } else {
                showPremiumAlert(json.message || json.error || 'Không thể xóa gói premium', 'danger');
            }
        } catch (error) {
            showPremiumAlert('Lỗi kết nối server khi xóa gói premium', 'danger');
        }
    }

    async function restorePremiumPackage(id) {
        if (!confirm('Bạn có chắc chắn muốn khôi phục gói premium này không? Gói sẽ hiển thị lại cho thí sinh mua.')) return;
        try {
            const res = await fetch(serverApiUrl('admin/goipremium'), {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_goi: id, action: 'restore' })
            });
            const json = await res.json();
            if (json.success) {
                showPremiumAlert(json.message);
                loadPremiumPackages();
            } else {
                showPremiumAlert(json.message || json.error || 'Không thể khôi phục gói premium', 'danger');
            }
        } catch (error) {
            showPremiumAlert('Lỗi kết nối server khi khôi phục gói premium', 'danger');
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('premiumModal');
        if (event.target == modal) closePremiumModal();
    }

    loadPremiumPackages();
</script>

<style>
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .premium-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }
    .premium-danger-btn {
        color: #ef4444;
        border-color: #fee2e2;
    }
    button:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }
    input:focus,
    textarea:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
</style>
