<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 20px;">Danh sách thí sinh nộp bài</h2>
            <div style="margin-top: 5px; color: #64748b; font-size: 14px;">
                Bài thi: <strong style="color: #1e293b;"><?= htmlspecialchars($baithi['ten_baithi'] ?? '---') ?></strong>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="api/ketqua/export-excel?id_baithi=<?= $id_baithi ?>" 
               style="background:#059669; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; border:none;">
               <i class="fas fa-file-excel"></i> Xuất file Excel
            </a>
            <a href="index.php?act=ketqua-thi" 
               style="background:#f1f5f9; color:#475569; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; border:1px solid #e2e8f0;">
               <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; width:60px;">STT</th>
                <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Họ tên / Email</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:150px;">Điểm số</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:200px;">Thời gian nộp</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:150px;">Thời lượng</th>
                <th style="padding: 14px 20px; text-align: right; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:120px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">Chưa có thí sinh nào nộp bài thi này.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $index => $sub): ?>
                    <tr style="border-bottom:1px solid #f1f5f9; cursor:pointer;" 
                        onclick="window.location.href='index.php?act=ketqua-thi&id_lanthi=<?= $sub['id_lanthi'] ?>'"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding:16px 20px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;"><?= $index + 1 ?></td>
                        <td style="padding:16px 20px;">
                            <div style="font-weight:700; color:#1e293b; font-size:15px;"><?= htmlspecialchars($sub['ten_thisinh']) ?></div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:2px;"><?= htmlspecialchars($sub['email_thisinh']) ?></div>
                        </td>
                        <td style="padding:16px 20px; text-align:center;">
                            <?php 
                                $color = '#1e293b';
                                if ($sub['diem'] >= 8) $color = '#15803d'; // Giỏi
                                elseif ($sub['diem'] >= 5) $color = '#1d4ed8'; // Trung bình / Khá
                                else $color = '#b91c1c'; // Yếu
                            ?>
                            <div style="font-size:18px; font-weight:800; color:<?= $color ?>;"><?= $sub['diem'] ?></div>
                            <div style="font-size:11px; color:#94a3b8; margin-top:2px;">Đúng <?= $sub['socaudung'] ?> câu</div>
                        </td>
                        <td style="padding:16px 20px; text-align:center; color:#475569; font-size:14px;">
                            <?= date('d/m/Y H:i', strtotime($sub['thoigiannop'])) ?>
                        </td>
                        <td style="padding:16px 20px; text-align:center; color:#475569; font-size:14px;">
                            <?php 
                                $s = $sub['thoi_gian_lam_giay'];
                                if ($s < 60) echo $s . " giây";
                                else echo floor($s / 60) . " phút " . ($s % 60) . " giây";
                            ?>
                        </td>
                        <td style="padding:16px 20px; text-align:right;">
                            <button style="background:#fff; color:#3b82f6; border:1px solid #dbeafe; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Xem bài làm</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
