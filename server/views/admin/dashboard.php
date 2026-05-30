<div class="card border-0 shadow-sm" style="border-radius:20px;overflow:hidden;">
    <div style="padding:28px 30px;background:linear-gradient(135deg,#eff6ff 0%,#f8fafc 100%);border-bottom:1px solid #e2e8f0;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0;color:#0f172a;">Bảng điều khiển quản trị</h2>
                <p style="margin:8px 0 0;color:#64748b;">Quản lý người dùng và theo dõi các khu vực trọng yếu của hệ thống PT QUIZ.</p>
            </div>
            <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:28px;">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    <div class="card-body" style="padding:30px;">
        <!-- STATS CARDS -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1;"><?= $data['stats']['total_users'] ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase;">Người dùng</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-user-lock"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1;"><?= $data['stats']['total_locked'] ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase;">Đã khóa</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1;"><?= $data['stats']['total_alerts'] ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase;">Cảnh báo 48h</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div>
                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1;"><?= $data['stats']['total_requests'] ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase;">Requests 24h</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- RECENT ALERTS TABLE -->
            <div class="col-md-7">
                <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; overflow: hidden; height: 100%;">
                    <div style="padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h5 style="margin: 0; font-weight: 700; color: #1e293b;">Cảnh báo bảo mật gần đây</h5>
                            <p style="margin: 4px 0 0; font-size: 11px; color: #94a3b8;">Ghi lại các nỗ lực truy cập trái phép hoặc lỗi hệ thống (4xx, 5xx).</p>
                        </div>
                        <a href="index.php?act=quanly-logs" style="font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none;">Xem tất cả</a>
                    </div>
                    <div style="padding: 0;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <?php if (empty($data['alerts'])): ?>
                                    <tr><td style="padding: 40px; text-align: center; color: #94a3b8;">Hệ thống đang hoạt động an toàn.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($data['alerts'] as $alert): ?>
                                        <tr style="border-bottom: 1px solid #f8fafc;">
                                            <td style="padding: 15px 25px;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                                                    <div>
                                                        <div style="font-size: 13px; font-weight: 600; color: #334155;"><?= $alert['ten'] ?? 'Anonymous' ?></div>
                                                        <div style="font-size: 11px; color: #94a3b8;"><?= date('H:i d/m/Y', strtotime($alert['created_at'])) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="padding: 15px 25px; text-align: center;">
                                                <span style="background: #fef2f2; color: #b91c1c; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;"><?= $alert['response_code'] ?></span>
                                            </td>
                                            <td style="padding: 15px 25px; text-align: right;">
                                                <div style="font-size: 12px; color: #64748b; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $alert['endpoint'] ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="col-md-5">
                <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; padding: 25px; height: 100%;">
                    <h5 style="margin: 0 0 20px; font-weight: 700; color: #1e293b;">Lối tắt quản trị</h5>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="index.php?act=quanly-nguoidung" style="display: flex; align-items: center; gap: 15px; padding: 15px; border-radius: 12px; background: #f8fafc; text-decoration: none; border: 1px solid transparent; transition: 0.2s;" onmouseover="this.style.borderColor='#cbd5e1';this.style.background='white'" onmouseout="this.style.borderColor='transparent';this.style.background='#f8fafc'">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Quản lý người dùng</div>
                                <div style="font-size: 12px; color: #64748b;">Xem và chỉnh sửa danh sách</div>
                            </div>
                        </a>
                        <a href="index.php?act=quanly-logs" style="display: flex; align-items: center; gap: 15px; padding: 15px; border-radius: 12px; background: #f8fafc; text-decoration: none; border: 1px solid transparent; transition: 0.2s;" onmouseover="this.style.borderColor='#cbd5e1';this.style.background='white'" onmouseout="this.style.borderColor='transparent';this.style.background='#f8fafc'">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfeff; color: #0891b2; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">Nhật ký bảo mật</div>
                                <div style="font-size: 12px; color: #64748b;">Kiểm tra các hành vi bất thường</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
