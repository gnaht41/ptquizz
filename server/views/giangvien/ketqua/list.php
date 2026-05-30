<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333;">Kết quả thi</h2>
    </div>

    <div style="margin-bottom: 16px; color: #6c757d; font-size: 14px;">
        Chọn một bài thi bên dưới để xem danh sách thí sinh và điểm chi tiết.
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; width:60px;">STT</th>
                <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Bài thi</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:150px;">Số lượt làm</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:200px;">Thống kê điểm</th>
                <th style="padding: 14px 20px; text-align: right; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:120px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:#94a3b8;">Bạn chưa có bài thi nào hoặc chưa có ai làm bài.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $index => $item): ?>
                    <tr style="border-bottom:1px solid #f1f5f9; cursor:pointer; transition: background 0.2s;" 
                        onclick="window.location.href='index.php?act=ketqua-thi&id_baithi=<?= $item['id_baithi'] ?>'"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding:16px 20px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;"><?= $index + 1 ?></td>
                        <td style="padding:16px 20px;">
                            <div style="font-weight:700; color:#1e293b; font-size:15px;"><?= htmlspecialchars($item['ten_baithi']) ?></div>
                            <div style="font-size:12px; color:#64748b; margin-top:4px;">
                                Môn: <span style="color:#3b82f6; font-weight:600;"><?= htmlspecialchars($item['tenmonhoc']) ?></span>
                            </div>
                        </td>
                        <td style="padding:16px 20px; text-align:center;">
                            <span style="background:#eff6ff; color:#1d4ed8; padding:4px 14px; border-radius:12px; font-size:12px; font-weight:700; border:1px solid #dbeafe;">
                                <?= $item['so_luot_lam'] ?> lượt
                            </span>
                        </td>
                        <td style="padding:16px 20px; text-align:center;">
                            <?php if ($item['so_luot_lam'] > 0): ?>
                                <div style="font-size:13px; color:#1e293b; font-weight:600;">Trung bình: <?= round($item['diem_trung_binh'], 2) ?></div>
                                <div style="font-size:11px; color:#94a3b8; margin-top:2px;">
                                    Cao nhất: <?= $item['diem_cao_nhat'] ?> | Thấp nhất: <?= $item['diem_thap_nhat'] ?>
                                </div>
                            <?php else: ?>
                                <span style="color:#cbd5e1; font-style:italic; font-size:12px;">Chưa có dữ liệu</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:16px 20px; text-align:right;">
                            <button style="background:#fff; color:#3b82f6; border:1px solid #dbeafe; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Xem chi tiết</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
