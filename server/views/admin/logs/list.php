<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div d-flex flex-column>
            <h2 style="margin: 0; color: #333; font-size: 24px; font-weight: 700;">Giám sát Bảo mật API</h2>
            <p style="margin: 5px 0 0; color: #64748b; font-size: 14px;">Theo dõi mọi yêu cầu hệ thống và phát hiện hành vi bất thường.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                <i class="fas fa-circle me-1" style="font-size: 8px;"></i> LIVE MONITORING
            </span>
        </div>
    </div>

    <!-- BỘ LỌC HÀNH ĐỘNG -->
    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
        <form method="GET" action="index.php" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="act" value="quanly-logs">
            
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Theo Ngày</label>
                <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>" 
                    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; color: #334155;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Phương thức</label>
                <select name="method" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; color: #334155; min-width: 120px;">
                    <option value="">Tất cả</option>
                    <option value="GET" <?= ($_GET['method'] ?? '') === 'GET' ? 'selected' : '' ?>>GET (Xem)</option>
                    <option value="POST" <?= ($_GET['method'] ?? '') === 'POST' ? 'selected' : '' ?>>POST (Thêm)</option>
                    <option value="PATCH" <?= ($_GET['method'] ?? '') === 'PATCH' ? 'selected' : '' ?>>PATCH (Sửa)</option>
                    <option value="DELETE" <?= ($_GET['method'] ?? '') === 'DELETE' ? 'selected' : '' ?>>DELETE (Xóa)</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
                <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Tìm kiếm</label>
                <input type="text" name="keyword" value="<?= $_GET['keyword'] ?? '' ?>" placeholder="Tên, Email hoặc Hành động..." 
                    style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; color: #334155;">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">
                    <i class="fas fa-filter me-1"></i> Lọc
                </button>
                <a href="index.php?act=quanly-logs" style="background: #fff; color: #64748b; border: 1px solid #cbd5e1; padding: 9px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none; text-align: center;">
                    Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:180px;">Thời gian</th>
                    <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Người dùng</th>
                    <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Hành động</th>
                    <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:140px;">IP Address</th>
                    <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:100px;">Status</th>
                    <th style="padding: 14px 20px; text-align: right; color:#64748b; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:100px;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:50px; color:#94a3b8;">
                            <i class="fas fa-shield-alt fa-3x mb-3" style="opacity:0.2;"></i>
                            <p>Chưa có dữ liệu nhật ký nào.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $log): ?>
                        <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding:16px 20px;">
                                <div style="font-weight:700; color:#1e293b; font-size:14px;"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                <div style="font-size:11px; color:#94a3b8; margin-top:2px;"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                            </td>
                            <td style="padding:16px 20px;">
                                <div style="font-weight:600; color:#1e293b; font-size:14px;">
                                    <?= $log['ten'] ?? '<span style="color:#94a3b8; font-style:italic;">Khách</span>' ?>
                                    <?php if ($log['trangthai'] === 'inactive'): ?>
                                        <span style="background:#fee2e2; color:#ef4444; font-size:9px; padding:1px 5px; border-radius:4px; margin-left:5px; border:1px solid #fecaca;">BỊ KHÓA</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:11px; color:#64748b; margin-top:2px;"><?= $log['email'] ?? 'Anonymous Request' ?></div>
                            </td>
                            <td style="padding:16px 20px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <?php 
                                        $methodColor = '#15803d'; $methodBg = '#f0fdf4'; // Default GET (Green)
                                        if ($log['method'] == 'POST') { $methodColor = '#1d4ed8'; $methodBg = '#eff6ff'; } // POST (Blue)
                                        if (in_array($log['method'], ['PATCH', 'PUT'])) { $methodColor = '#b45309'; $methodBg = '#fffbeb'; } // UPDATE (Orange)
                                        if ($log['method'] == 'DELETE') { $methodColor = '#b91c1c'; $methodBg = '#fef2f2'; } // DELETE (Red)
                                    ?>
                                    <span style="background: <?= $methodBg ?>; color: <?= $methodColor ?>; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; border: 1px solid <?= $methodColor ?>22;"><?= $log['method'] ?></span>
                                    <span style="font-size:13px; font-weight:600; color:#334155;"><?= $log['clean_action'] ?></span>
                                </div>
                                <div style="font-size:10px; color:#94a3b8; margin-top:4px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= $log['endpoint'] ?>
                                </div>
                            </td>
                            <td style="padding:16px 20px; text-align:center;">
                                <span style="font-size:12px; font-weight:600; color:#475569;"><?= $log['ip_address'] ?></span>
                            </td>
                            <td style="padding:16px 20px; text-align:center;">
                                <?php 
                                    $statusColor = '#15803d'; $statusBg = '#f0fdf4';
                                    if ($log['response_code'] >= 400) { $statusColor = '#b45309'; $statusBg = '#fffbeb'; }
                                    if ($log['response_code'] >= 500) { $statusColor = '#b91c1c'; $statusBg = '#fef2f2'; }
                                ?>
                                <span style="background:<?= $statusBg ?>; color:<?= $statusColor ?>; border:1px solid currentColor; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700;">
                                    <?= $log['response_code'] ?>
                                </span>
                            </td>
                            <td style="padding:16px 20px; text-align:right;">
                                <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                    <?php if ($log['id_nguoidung'] && $log['vaitro'] !== 'admin'): ?>
                                        <button type="button" class="btn-toggle-user" data-id="<?= $log['id_nguoidung'] ?>" data-status="<?= $log['trangthai'] ?>"
                                            title="<?= $log['trangthai'] === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' ?>"
                                            style="background: #fff; color: <?= $log['trangthai'] === 'active' ? '#ef4444' : '#10b981' ?>; border: 1px solid currentColor; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                                            <i class="fas <?= $log['trangthai'] === 'active' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                        </button>
                                    <?php endif; ?>

                                    <button type="button" class="btn-detail" data-bs-toggle="modal" data-bs-target="#logModal<?= $log['id_log'] ?>" 
                                        style="background: #fff; color: #3b82f6; border: 1px solid #dbeafe; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal Chi Tiết -->
                                <div class="modal fade" id="logModal<?= $log['id_log'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
                                            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 20px 25px;">
                                                <h5 class="modal-title" style="font-weight: 700; color: #1e293b;">Chi tiết Yêu cầu #<?= $log['id_log'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" style="padding: 25px; text-align: left;">
                                                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                                                    <h6 style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px;">Dữ liệu gửi lên (Payload)</h6>
                                                    <pre style="font-family: 'Consolas', monospace; font-size: 13px; background: transparent; padding: 0; margin: 0; white-space: pre-wrap; color: #334155;"><?php 
                                                        $params = json_decode($log['request_params'], true);
                                                        echo json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                                    ?></pre>
                                                </div>
                                                <div style="margin-top: 20px;">
                                                    <h6 style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">Thông tin Trình duyệt / Thiết bị</h6>
                                                    <p style="font-size: 13px; color: #475569; line-height: 1.6;"><?= htmlspecialchars($log['user_agent']) ?></p>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top: none; padding: 15px 25px 25px;">
                                                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f1f5f9; color: #64748b; padding: 10px 25px; border-radius: 8px; border: none; font-weight: 600;">Đóng</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.btn-toggle-user').forEach(button => {
    button.addEventListener('click', async function() {
        const userId = this.getAttribute('data-id');
        const currentStatus = this.getAttribute('data-status');
        const actionText = currentStatus === 'active' ? 'Khóa' : 'Mở khóa';
        
        if (!confirm(`Bạn có chắc chắn muốn ${actionText} tài khoản này không?`)) return;
        
        try {
            const apiUrl = typeof serverApiUrl === 'function' 
                ? serverApiUrl('nguoidung/toggle-status') 
                : 'api/nguoidung/toggle-status';

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_nguoidung: userId })
            });
            
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                location.reload(); 
            } else {
                alert('Lỗi: ' + (result.message || result.error || 'Không xác định'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Đã có lỗi xảy ra: ' + error.message);
        }
    });
});
</script>
